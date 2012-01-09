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
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }

        $this->$method($value);
    }
    public function __get($name)
    {
        $method = 'get' . $name;

        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }

        return $this->$method();
    }
}
