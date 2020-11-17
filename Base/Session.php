<?php


namespace Base;

use Base\Exception;
class Session
{

    const FIELD_USER_ID = 'id';
    const FIELD_IP = 'user_ip';
    const FIELD_USER_DEVICE = 'user_device';

    private static $_instance;

    private function __construct()
    {
        session_start();
    }

    private function __clone()
    {
    }

    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function set(string $key, $value)
    {
        $_SESSION[$key] = $value;

    }

    private function get($key)
    {

        return $_SESSION[$key] ?? null;

    }

    public function destroy()
    {
        session_destroy();
    }

    public function save(int $userId)
    {
        if ($userId <= 0) {

            throw new \Exception(' Cant save session for userId# ' . $userId);
        }
        $request = new Request();

        $this->set(self::FIELD_USER_ID, $userId);
        $this->set(self::FIELD_IP, $request->getIp());
        $this->set(self::FIELD_USER_DEVICE, $request->getUserDevice());
    }

    public function check()
    {
        $request = Context::getInstance()->getRequest();

        if ($request->getIp() !== $this->get(self::FIELD_IP)) {
            return false;
        }

        if (crc32($request->getUserAgent()) !== crc32($this->get(self::FIELD_USER_DEVICE))) {
            return false;
        }

        return true;
    }


    public function getUserId()
    {
        return $this->get(self::FIELD_USER_ID);
    }

}