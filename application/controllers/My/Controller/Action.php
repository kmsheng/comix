<?php

abstract class My_Controller_Action extends Zend_Controller_Action
{
    protected $domain = 'http://www.8comic.com';

    public function init()
    {
    }

    /* @param url   The URL contains the required HTML for dom query.
     *
     * @return      A Zend Dom Query instance.
     */
    public function getDomQuery($url)
    {
        $html = file_get_contents($url);
        return new Zend_Dom_Query($html);
    }

    /* @param url   URL to fetch the link of each chapter.
     *
     * @return      An array contains chapter links.
     */
    public function getChapterLinks($url)
    {
        $dom = $this->getDomQuery($url);
        $links = $dom->query('a');

        preg_match('/\d+\.html/', $url, $number);

        // Here's the base url to the intro page of comic.
        $baseUrl = 'http://www.8comic.com/love/drawing-' . $number[0] . '?ch=';
        $chapterLinks = array();

        foreach ($links as $link) {

            $onclick = $link->getAttribute('onclick');

            // Regular expression for cview('7820-7.html',11);
            preg_match('/^cview\(\'\d+-(\d+)\.html\'\,\d+\)/', $onclick, $chapter);

            // If it fetches the chpater number
            if (!empty($chapter)) {
                $obj = new stdClass;
                $obj->value = trim($link->nodeValue);
                $obj->url = $baseUrl . $chapter[1];
                $chapterLinks[] = $obj;
            }
        }

        return $chapterLinks;
    }

    /* @param url   URL to the comic page of certain chapter and has JavaScript contains var codes="".
     *
     * @return      An array contains the url to the first page of each chapter.
     */
    public function fetchFirstPages($url)
    {
        $firstPages = array();
        $data = $this->getPageData($url);
        $itemid = $this->getItemId($url);

        foreach ($data as $datum) {

            $firstPages[] = $this->getImageUrl($datum, $itemid);
        }

        return $firstPages;
    }

    public function getItemId($url)
    {
        preg_match('/(\d+).html/', $url, $urlMatches);
        return $urlMatches[1];
    }

    /* @param url   URL of any page that display the comic.
     *
     * @return      An array contains several objects that have
     *              num, sid, did, page, code as the member variables of object.
     */
    public function getPageData($url)
    {
        $data = array();
        $dom = $this->getDomQuery($url);
        $scripts = $dom->query('script');

        foreach ($scripts as $script) {

            preg_match('/var codes=\"([\w\s|]+)\"/', $script->nodeValue, $matches);

            if (!empty($matches)) {
                break;
            }
        }

        $matches = explode('|', $matches[1]);

        foreach ($matches as $match) {

            $pieces = preg_split("/\s+/", $match);
            $obj = new stdClass;

            $obj->num = $pieces[0];
            $obj->sid = $pieces[1];
            $obj->did = $pieces[2];
            $obj->page = $pieces[3];
            $obj->code = $pieces[4];

            $data[] = $obj;
        }

        return $data;
    }

    /* @param datum     An object contains num, sid, did, page, code as member variables.
     *
     * @return          The absolute URL of comic image.
     */
    public function getImageUrl($datum, $itemid, $p = 1)
    {
        $num = $datum->num;
        $sid = $datum->sid;
        $did = $datum->did;
        $page = $datum->page;
        $code = $datum->code;

        // Here's how the China website encrypts their URLs of comic image.
        if ($p < 10) {
            $img = '00' . $p;
        } else if ($p < 100) {
            $img = '0' . $p;
        } else {
            $img = $p;
        }

        $m = (int) (((($p - 1) / 10) % 10) + ((($p - 1) % 10) * 3));

        $img .= '_' . substr($code, $m, 3);

        return 'http://img' . $sid . '.8comic.com/' . $did . '/' . $itemid . '/' . $num . '/' . $img . '.jpg';
    }

    /* @param url   URL to be verified.
     *
     * @return      True if it's comic intro page.
     */
    public function isComicIntroPage($url)
    {
        $search = array('/', '.');
        $replace = array('\/', '\.');
        $domainRegexp = str_replace($search, $replace, $this->domain);

        return preg_match("/" . $domainRegexp . "\/html\/\d+.html/i", $url);

    }

    /* @param e Zend Exception Instance.
     */
    public function showErrorMessage($e)
    {
        echo "Caught exception: " . get_class($e) . "\n";
        echo "Message: " . $e->getMessage() . "\n";
    }

}
