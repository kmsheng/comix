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

    public function setOptions(array $options)
    {

        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            // handle for the variable like this_thingy
            $key_array = explode('_', $key);
            $part_string = '';
            foreach ($key_array as $part) {
                $part_string .= ucfirst($part);
            }
            $method = 'set' . $part_string;
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    public function  getId()
    {
        return $this->_id;
    }
    public function setName($name)
    {
        $this->_name = (string) $name;
        return $this;
    }
    public function getName()
    {
        return $this->_name;
    }
}
