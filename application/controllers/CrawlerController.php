<?php

class CrawlerController extends My_Controller_Action
{
    protected $homePage;
    protected $homePageMapper;

    public function init()
    {
        parent::init();

        // Set the time limit to be 30 minutes because parsing website can be time consuming.
        set_time_limit(1800);

        $this->homePage = new Application_Model_HomePage();
        $this->homePageMapper = new Application_Model_HomePageMapper();

        $this->_helper->layout->disableLayout();
    }

    /* @param url   Incomplete url that does not include domain name.
     *              e.g. /msg, member/reg.aspx
     *
     * @return      The full URL, e.g. http://www.8comic.com/member/reg.aspx
     */
    public function getFullUrl($url)
    {

        if (!preg_match('/http:\/\//i', $url)) {
            if ($url{0} != '/') {
                $url = '/' . $url;
            }
            $url = $this->domain . $url;
        }

        return $url;
    }

    /* @param url   URL to parse links.
     * @param data  An array that contains the link data, could be empty.
     *
     * @return      An array that contains link data.
     */
    public function getLinks($url, $data)
    {
        $dom = $this->getDomQuery($url);

        $a = $dom->query('a');
        $href = '';

        foreach($a as $link) {
            $obj = new stdClass;
            $href = $this->getFullUrl($link->getAttribute('href'));

            if ($this->hasChapterLinks($href) && !$this->hasUrl($href)) {

                $obj->href = $href;
                $obj->value = trim($link->nodeValue);

                if (!empty($obj->value)) {
                    $data[] = $obj;
                }
            }
        }

        return $data;
    }
    public function indexAction()
    {
    }

}
