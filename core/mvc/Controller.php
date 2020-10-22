<?php


namespace Core\Mvc;


use Core\Request;
use Core\Response;
use Core\Session;

class Controller
{
    /**
     * @var array
     */
    public $config;
    /**
     * @var Request
     */

    public $request;
    /**
     * @var Response
     */
    public $response;

    /**
     * @var Session
     */
    public $session;

    /**
     * @param Request $request
     */

    /**
     * @var string $lang
     */
    public $lang;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function setLang(string $lang)
    {
        if ($lang == "uk") {
            $lang = "ua";
        } elseif ($lang == 'en-US') {
            $lang = 'en';
        }
        $this->lang = $lang;
    }
}