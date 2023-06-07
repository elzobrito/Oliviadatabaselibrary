<?php

namespace OliviaCache;

class SessionCache implements CacheInterface
{
    private $sessionKey;

    public function __construct($sessionKey)
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $this->sessionKey = $sessionKey;
    }

    public function get($key)
    {
        $cache = isset($_SESSION[$this->sessionKey]) ? $_SESSION[$this->sessionKey] : array();
        return isset($cache[$key]) ? $cache[$key] : null;
    }

    public function set($key, $value)
    {
        $_SESSION[$this->sessionKey][$key] = $value;
    }

    public function delete()
    {
        unset($_SESSION[$this->sessionKey]);
    }
}
