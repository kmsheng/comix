<?php

class CrawlerController extends My_Controller_Action
{
    protected $homePage;
    protected $homePageMapper;
    protected $uniqueLinks = array();

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

        foreach ($a as $link) {
            $obj = new stdClass;
            $href = $this->getFullUrl($link->getAttribute('href'));
            $value = trim($link->nodeValue);

            if (empty($value)) {
                continue;
            }

            if ($this->hasChapterLinks($href) && !$this->hasUrl($href)) {

                $obj->href = $href;
                $obj->value = $value;

                $data[] = $obj;
            }
        }

        return $data;
    }

    /* @param url   URL that has the chpater links.
     *
     * @return      True if match the regular expression.
     */
    public function hasChapterLinks($url) {
        $rule = '';

        switch ($this->domain) {
            case 'http://www.8comic.com':
                $rule = '/\/html/';
                break;
            default:
                $rule = '//';
        }

        if (preg_match($rule, $url)) {
            return true;
        }

        return false;
    }

    /* @param url   URL to be verified.
     *
     * @return      True if the url has already run.
     */
    public function hasUrl($url)
    {
        if (in_array($url, $this->uniqueLinks)) {
            return true;
        }

        // The array contains unique links.
        $this->uniqueLinks[] = $url;

        return false;
    }

    /* @param link  An object contains member variables href and value.
     * @param data  An array contains the image data; it could be empty for the first call.
     *
     * @return data An image data array.
     */
    public function getImages($link, $data)
    {
        $dom = $this->getDomQuery($link->href);

        try {
            $imgs = $dom->query('td > img');
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
        }

        foreach ($imgs as $img) {
            $obj = new stdClass;
            $src = $this->getFullUrl($img->getAttribute('src'));

            if (preg_match('/\.jpg/', $src)) {
                $obj->img->src = $src;
                $obj->href = '/index/chapter?url=' . $link->href;
                $obj->value = $link->value;

                $obj->description = trim($this->getDescription($link->href));

                $data[] = $obj;
            }
        }

        return $data;
    }

    /* @param url       URL to fetch the description of an comic.
     * @param length    Desired length of description.
     *
     * @return          The description of an comic.
     */
    public function getDescription($url, $length = 100)
    {

        $dom = $this->getDomQuery($url);
        $descriptions = $dom->query('td');

        foreach ($descriptions as $description) {

            // Here's where they put the description, it might be changed.
            if ('f0f8ff' === $description->getAttribute('bgcolor')) {

                $text = $description->nodeValue;
                if (mb_strlen($text) > $length) {
                    $text = mb_substr($text, 0, 100, 'UTF-8');
                }
                return $text;
            }
        }
    }

    /* @param filePath  Either local path or remote url is fine.
     * @param newWidth  The new width which the thumb is gonna be.
     *
     * */
    public function resizeImage($filePath, $newWidth)
    {

        // Get new sizes
        list($width, $height) = getimagesize($filePath);

        $newHeight = ($height * $newWidth) / $width;

        // Load
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        $source = imagecreatefromjpeg($filePath);

        // Resize
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        preg_match('/\/([\w\_]+\.jpg)/', $filePath, $matches);

        $filename = $matches[1];
        $path = dirname(__FILE__) . '/../../public/static/img/chapter-thumbs/' . $filename;

        // Save resized image
        imagejpeg($thumb, $path, 100);

    }

    /* @param data  An object contains value, img src, description
     *
     * @return      True if successfully finish update.
     */
    public function updateHomePage($data)
    {

        foreach ($data as $datum) {
            preg_match('/(\d+)\.html/', $datum->href, $matches);
            $id = $matches[1];

            $this->homePage->setId($id);
            $this->homePage->setName($datum->value);
            $this->homePage->setImgUrl($datum->img->src);
            $this->homePage->setDescription($datum->description);

            try {
                $this->homePageMapper->save($this->homePage);
            } catch (Zend_Exception $e) {
                $this->showErrorMessage($e);
            }
        }

        return true;
    }

    public function indexAction()
    {
    }

    public function makeChapterThumbsAction()
    {

        foreach ($this->homePageMapper->fetchAll() as $datum) {

            $id = $datum->getId();
            $url = $this->domain . '/html/' . $id . '.html';

            try {
                $chapterLinks = $this->getChapterLinks($url);
            } catch (Zend_Exception $e) {
                $this->showErrorMessage($e);
            }

            $link = $chapterLinks[0];

            try {
                $pages = $this->fetchFirstPages($link->url);
            } catch (Zend_Exception $e) {
                $this->showErrorMessage($e);
            }

            $count = count($chapterLinks);
            $data = array();

            foreach($pages as $page) {
                $this->resizeImage($page, 192);
            }
        }

        $this->view->output = 'done fetching chapter thumbs';
    }

    /* Provide the home page data in json format.
     */
    public function provideHomePageDataAction()
    {

        $output = array();

        foreach ($this->homePageMapper->fetchAll() as $datum) {
            $array = array();
            $array['href'] = '/index/chapter?url=' . $this->domain .'/html/' . $datum->getId() . '.html';
            $array['name'] = $datum->getName();
            $array['description'] = $datum->getDescription();
            $array['src'] = $datum->getImgUrl();

            $output[] = $array;
        }

        $this->view->output = Zend_Json::encode($output);

        header('Content-type: application/json');
    }

    public function provideChapterDataAction()
    {

        $data = array();

        if (!isset($_GET['url'])) {
            throw new Zend_Exception('Please input url to parse for chapter links');
        }

        if (!$this->isComicIntroPage($_GET['url'])) {
            throw new Zend_Exception('The url provided is not comic intro page.');
        }

        try {
            $chapterLinks = $this->getChapterLinks($_GET['url']);
        } catch (Zend_Exception $e) {
            $this->showErrorMessage($e);
        }

        $link = $chapterLinks[0];

        try {
            $pages = $this->fetchFirstPages($link->url);
        } catch (Zend_Exception $e) {
            $this->showErrorMessage($e);
        }

        $images = 1000;
        $count = count($chapterLinks);
        $limit = ($count > $images) ? $count - $images : 0;

        $index = 0;
        foreach($chapterLinks as $chapterLink) {

            $obj = new stdClass;
            preg_match('/\/(\w+\.jpg)/', $pages[$index], $matches);

            $filename = $matches[1];
            $path = dirname(__FILE__) . '/../../public/static/img/chapter-thumbs/' . $filename;

            // if thumb exists, use it. Otherwise, download remote large picture instead.
            $obj->src = file_exists($path) ? '/static/img/chapter-thumbs/' . $filename : $pages[$index];
            $obj->href = '/index/browse?url=' . $chapterLink->url;
            $obj->value = $chapterLink->value;
            $obj->flag = ($index >= $limit) ? true : false;

            $data[] = $obj;
            $index++;
        }

        $data = array_reverse($data);

        $this->view->output = Zend_Json::encode($data);

        header('Content-type: application/json');
    }

    /* Parse the links and image urls of home page
     * and store them in the database.
     */
    public function updateHomePageAction()
    {

        $links = array();
        $data = array();

        $links = $this->getLinks($this->domain, $links);

        foreach($links as $link) {
            $data = $this->getImages($link, $data);
        }

        if ($this->updateHomePage($data)) {
            $this->view->output = '更新完成!';
        }
    }

    /* Provides comic pages data.
     */
    public function provideComicAction()
    {
        $images = array();

        if (!isset($_GET['url'])) {
            throw new Zend_Exception('please input the number of chapter!');
        }

        $url = $_GET['url'];

        $itemid = $this->getItemId($url);
        $data = $this->getPageData($url);

        $chapter = $this->getChapterByUrl($url);
        $chapterData = $this->findChapterData($data, $chapter);

        for ($index = 1; $index <= $chapterData->page; $index++) {

            $images[$index] = $this->getImageUrl($chapterData, $itemid, $index);
        }

        $output = Zend_Json::encode($images);
        $this->view->output = $output;

        header('Content-type: application/json');

    }

    /* @param data      Object contains member variables such as num, sid, did, page, code.
     * @param chapter   The desired chapter.
     *
     * @return          Datum that matches the chapter.
     */
    public function findChapterData($data, $chapter)
    {

        foreach ($data as $datum) {

            if ((int) $chapter === (int) $datum->num) {
                return $datum;
            }
        }

        return null;
    }

    /* @param url   URL to get chapter.
     *
     * @return      Chapter if it matches the regular expression.
     */
    public function getChapterByUrl($url)
    {
        preg_match('/ch=(\d+)$/', $url, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }
        return null;
    }

}
