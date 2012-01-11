<?php

class IndexController extends My_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->view->headScript()
            ->prependFile('/static/js/pins.js');
    }

    public function chapterAction()
    {
        $this->view->headScript()
            ->prependFile('/static/js/chapter.js');
    }

    // browse comic
    public function browseAction()
    {
        $this->view->headScript()
            ->appendFile('/static/js/extlib/jquery-lightbox/jquery.lightbox-0.5.js')
            ->appendFile('/static/js/lightbox.js');

        $this->view->headLink()
            ->appendStylesheet('/static/css/extlib/jquery-lightbox/jquery.lightbox-0.5.css');
    }

}
