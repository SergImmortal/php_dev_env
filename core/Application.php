<?php


namespace Core;


use App\Controllers\IndexController;
use Core\Mvc\Controller;

class Application
{
    public function __construct()
    {
        define("APP_PATH", dirname(__FILE__, 2) . "/app/");
        define("CORE_PATH", dirname(__FILE__) . "/");
        define("BASE_PATH", dirname(__FILE__, 2) . "/");
        spl_autoload_register(function ($className) {
            $className = explode("\\", $className);
            foreach ($className as $index => $word) {
                if ($index == count($className)-1) {
                    $className[$index] = $word . ".php";
                } else {
                    $className[$index] = strtolower($word);
                }
            }
            $className = BASE_PATH . implode("/", $className);
            if (file_exists($className)) {
                require_once($className);
            } else {
                throw new \Exception("Class '$className' is not defined");
            }
        });
        $router = new Router();
        $controllerClassName = "App\\Controllers\\" . $router->getController() . "Controller";
        $controller = new $controllerClassName();
        $actionName = $router->getAction() . "Action";

        if (method_exists($controller, $actionName)) {
            $this->execute($controller, $actionName, $router);
        } else {
            throw new \Exception("Method '$actionName' is not defined");
        }
    }

    public function execute($controller, $actionName, $router)
    {
        $session = new Session();
        ACL::getInstance()->setRoleID($session->getSession("role_id"));
        if (ACL::getInstance()->isAllowed($router->getController(), $router->getAction())) {
            /** @var Controller $controller */
            $controller->setRequest(new Request());
            $controller->setResponse(new Response());
            $controller->setSession($session);
            $controller->setConfig(yaml_parse_file(BASE_PATH . "config.yaml"));
            $controller->setLang(isset($_COOKIE["lang"])? $_COOKIE["lang"]: "uk");
            $controller->$actionName();
        } else {
            http_response_code(401);
            exit("Access denied");
        }
    }
}