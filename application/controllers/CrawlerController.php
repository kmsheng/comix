<?php

class CrawlerController extends My_Controller_Action
{
    protected $homePage;
    protected $homePageMapper;

    public function init()
    {

        $this->homePage = new Application_Model_HomePage();
        $this->homePageMapper = new Application_Model_HomePageMapper();

    }

    public function indexAction()
    {
    }

}
