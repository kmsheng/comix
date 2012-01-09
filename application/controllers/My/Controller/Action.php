<?php

abstract class My_Controller_Action extends Zend_Controller_Action
{
    protected $domain = 'http://www.8comic.com';

    public function init()
    {
    }

    /* @param url   The URL contains the required HTML for dom query.
     *
     * @return      A Zend Dom Query instance.
     */
    public function getDomQuery($url)
    {
        $html = file_get_contents($url);
        return new Zend_Dom_Query($html);
    }

}
