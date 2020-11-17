<?php


namespace Base;


class Context
{
    private $_request;

    private static $_instance;

    public static function getInstance()
    {
        if (!self::$_instance){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request):Request
    {
        $this->_request = $request;
    }
}