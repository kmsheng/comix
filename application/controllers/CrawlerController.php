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

    public function indexAction()
    {
    }

}
