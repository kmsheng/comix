<?php

class IndexController extends My_Controller_Action
{

    public function init()
    {
        parent::init();

        $this->view->headScript()
            ->prependFile('/static/js/pins.js');
    }

    public function indexAction()
    {
    }

}
