<?php

class Application_Model_HomePage
{
    protected $_id;
    protected $_name;
    protected $_description;
    protected $_img_url;

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
}
