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

    {
        $this->view->headScript()
            ->prependFile('/static/js/home-page.js');
    }

}
