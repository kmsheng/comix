<?php

class IndexController extends My_Controller_Action
{
    protected $homePageMapper;
    protected $title;

    public function init()
    {
        parent::init();

        $this->homePageMapper = new Application_Model_HomePageMapper();
        $this->title = 'comix - 輕鬆讀';
    }

    // main page which has many comics shown
    public function indexAction()
    {
        $this->view->headScript()
            ->prependFile('/static/js/pins.js')
            ->prependFile('/static/js/home-page.js');

        $this->view->title = $this->title;
    }

    // choose which chapter to watch
    public function chapterAction()
    {
        $this->view->headScript()
            ->prependFile('/static/js/pins.js')
            ->prependFile('/static/js/chapter.js');

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
            ->appendFile('/static/js/extlib/jquery-lightbox/jquery.lightbox-0.5.js')
            ->appendFile('/static/js/lightbox.js');

        $this->view->headLink()
            ->appendStylesheet('/static/css/extlib/jquery-lightbox/jquery.lightbox-0.5.css');

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
