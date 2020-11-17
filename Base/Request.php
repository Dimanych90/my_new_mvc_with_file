<?php


namespace Base;


class Request
{


    /**
     * Request constructor.
     */
    public function __construct()
    {
    }

    public function reqUserIp()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    public function getUserDevice()
    {
        return $_SERVER["HTTP_ACCEPT_ENCODING"];
    }

    public function getIp()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

}