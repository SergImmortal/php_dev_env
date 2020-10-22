<?php


namespace Core;


class Request
{
    protected $postParams = [];
    protected $getParams = [];
    protected $headers = [];
    protected $type;

    public function __construct()
    {
        $this->type = $_SERVER["REQUEST_METHOD"];
        $this->setHeaders();
        $this->setGet();
        $this->setPost();
    }

    public function setGet()
    {
        $this->getParams = $_GET;
    }

    public function setPost()
    {
        if ($this->getHeaders("Content-Type") === "application/json") {
            $jsonStr = file_get_contents('php://input');
            $this->postParams = json_decode($jsonStr, true);
        } else {
            $this->postParams = $_POST;
        }
    }

    public function setHeaders()
    {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $this->headers[str_replace(
                    ' ',
                    '-',
                    ucwords(
                        strtolower(
                            str_replace(
                                '_',
                                ' ',
                                substr(
                                    $name,
                                    5
                                )
                            )
                        )
                    )
                )] = $value;
            }
        }
    }

    public function getGet($name = false)
    {
        if ($name !== false) {
            if (isset($this->getParams[$name])) {
                return $this->getParams[$name];
            } else {
                return null;
            }
        } else {
            return $this->getParams;
        }
    }

    public function getPost($name = false)
    {
        if ($name !== false) {
            if (isset($this->postParams[$name])) {
                return $this->postParams[$name];
            } else {
                return null;
            }
        } else {
            return $this->postParams;
        }
    }

    public function getHeaders($name = false)
    {
        if ($name !== false) {
            if (isset($this->headers[$name])) {
                return $this->headers[$name];
            } else {
                return null;
            }
        } else {
            return $this->headers;
        }
    }

    public function isPost()
    {
        if ($this->type === "POST") {
            return true;
        } else {
            return false;
        }
    }

    public function isGet()
    {
        if ($this->type === "GET") {
            return true;
        } else {
            return false;
        }
    }
}