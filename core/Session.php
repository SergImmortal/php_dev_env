<?php


namespace Core;


class Session
{
    private static $_sessionStart = false;

    public function initSession()
    {
        if(!isset($_SESSION)) {
            session_start();
        }
    }

    public function createSession()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function setSession($key, $value)
    {
        $this->initSession();
        $_SESSION[$key] = $value;
    }

    public function getSession($key)
    {
        $this->initSession();
        if (isset($_SESSION[$key])){
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    public function deleteSession($key)
    {
        $this->initSession();
        unset($_SESSION[$key]);
    }

    public function destroySession()
    {
        if (isset($_SESSION)){
            session_destroy();
        }
    }
}