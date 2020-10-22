<?php


namespace App\Controllers;


use Core\DB;
use Core\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $dataSet = DB::getInstance()->select("SELECT `path`, `name` FROM `photo` WHERE is_visible=1 AND slider_home=1");
        $links = [];
        foreach ($dataSet as $data)
        {
            $links[] = $this->config["domain"].$data["path"].$data["name"];
        }
        $this->response->setStatus("success");
        $this->response->setMessage("OK");
        $this->response->setData($links);
        return $this->response->json();
    }
}