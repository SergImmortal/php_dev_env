<?php


namespace Core;


class Response
{
    public $body = [];

    public function setStatusCode($statusCode)
    {
        http_response_code($statusCode);
    }

    public function setHeaders($name, $value)
    {
        header($name . ": " . $value);
    }

    public function setData($data)
    {
        $this->body["data"] = $data;
    }

    public function setStatus($status="error")
    {
        $this->body["status"] = $status;
    }

    public function setMessage($message)
    {
        $this->body["message"] = $message;
    }

    public function json($data = false)
    {
        if ($data !== false) {
            $this->body = $data;
        }
        $this->setHeaders("Content-Type", "application/json");
        echo json_encode($this->body);
        return $this;
    }

    public function response($data = false)
    {
        if ($data !== false) {
            $this->body = $data;
        }
        echo $this->body;
        return $this;
    }
}