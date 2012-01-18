<?php

class IndexController extends My_Controller_Action
{
    protected $homePageMapper;
    protected $title;
    protected $domain;

    public function init()
    {
        parent::init();

        $this->domain = Zend_Registry::get('amazon_s3_comix');

        $this->homePageMapper = new Application_Model_HomePageMapper();
        $this->title = 'comix - 輕鬆讀';

        $this->view->headLink()->appendStylesheet($this->domain . '/static/css/style.css');
        $this->view->headScript()->appendFile($this->domain . '/static/js/extlib/modernizr/modernizr-2.0.6.min.js')
            ->appendFile($this->domain . '/static/js/plugins.js')
            ->appendFile($this->domain . '/static/js/script.js');
    }

    // main page which has many comics shown
    public function indexAction()
    {
        $this->view->headScript()
            ->appendFile($this->domain . '/static/js/home-page.js')
            ->appendFile($this->domain . '/static/js/pins.js');

        $this->view->title = $this->title;

    }

    // choose which chapter to watch
    public function chapterAction()
    {
        $this->view->headScript()
            ->appendFile($this->domain . '/static/js/chapter.js')
            ->appendFile($this->domain . '/static/js/pins.js');

        if (!isset($_GET['url'])) {
            throw new Zend_Exception('please input url!');
        }

        $id = $this->getItemId($_GET['url']);
        $name = $this->homePageMapper->getNameById($id);

        $this->view->title = $this->title . ' - ' .  $name;
    }

    // browse comic
    public function browseAction()
    {
        $this->view->headScript()
            ->appendFile($this->domain . '/static/js/extlib/jquery-lightbox/jquery.lightbox-0.5.js')
            ->appendFile($this->domain . '/static/js/lightbox.js');

        $this->view->headLink()
            ->appendStylesheet($this->domain  . '/static/css/extlib/jquery-lightbox/jquery.lightbox-0.5.css');

        if (!isset($_GET['url'])) {
            throw new Zend_Exception('please input url');
        }

        if (!isset($_GET['text'])) {
            throw new Zend_Exception('please input text');
        }

        $id = $this->getItemId($_GET['url']);
        $name = $this->homePageMapper->getNameById($id);

        $this->view->title = $this->title . ' - ' . $name . ' - ' . htmlspecialchars($_GET['text']);
    }

}
