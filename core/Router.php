<?php


namespace Core;


class Router
{
    protected $controller = "index";
    protected $action = "index";

    public function __construct()
    {
        if (stripos($_SERVER["REQUEST_URI"], "?")){
            $queryString = strstr(substr($_SERVER["REQUEST_URI"], 1), "?", true);
        }else{
            $queryString = substr($_SERVER["REQUEST_URI"], 1);
        }
        $queryArray = explode('/', $queryString);
        if (count($queryArray) === 1 && count($queryArray)) {
            if (!empty($queryArray[1])) {
                $this->controller = $queryArray[1];
            }
        } else {
            if (!empty($queryArray[1])) {
                $this->controller = $queryArray[1];
            }
            if (!empty($queryArray[2])) {
                $this->action = $queryArray[2];
            }
        }

    }

    /**
     * @return string
     */
    public function getController()
    {
        return ucfirst($this->controller);
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

}