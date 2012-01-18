<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
    }

    protected function _initConfig()
    {
        $config = $this->getOptions();

        // amazon s3 url
        Zend_Registry::set('amazon_s3_comix', $config['amazon']['s3']['comix']);
    }
}

