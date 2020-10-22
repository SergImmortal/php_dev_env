<?php


namespace App\Controllers;


use Core\DB;
use Core\Mvc\Controller;

class CategoriesController extends Controller
{
    public function listAction()
    {
        $categories = DB::getInstance()->select("SELECT id,title_{$this->lang} as title FROM categories");
        $this->response->setStatus("success");
        $this->response->setData($categories);
        return $this->response->json();
    }

    public function fullListAction()
    {
        $categories = DB::getInstance()->select("SELECT id,title_en, title_ua FROM categories");
        $this->response->setStatus("success");
        $this->response->setData($categories);
        return $this->response->json();
    }

    public function addAction()
    {
        $params = $this->request->getPost();
        if (isset($params["title_ua"]) && isset($params["title_en"])) {
            $id = DB::getInstance()->insert(
                "INSERT INTO `categories` ( title_ua, title_en) VALUES (:title_ua, :title_en)",
                $params);
            if (!$id) {
                $this->response->setStatus();
                $this->response->setStatusCode(422);
                $this->response->setMessage("Don't inserted");
            } else {
                $this->response->setStatus("success");
                $this->response->setStatusCode(200);
                $this->response->setMessage("OK");
                $this->response->setData(["id" => $id]);
            }
        } else {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Title not found");
        }
        return $this->response->json();
    }

    public function updateAction()
    {
        $params = $this->request->getPost();
        if (isset($params["title_ua"]) && isset($params["title_en"]) && isset($params["id"])) {
            DB::getInstance()->update(
                "UPDATE `categories` SET `title_en` = :title_en, `title_ua` = :title_ua WHERE `id` = :id",
                $params);
            $this->response->setStatus("success");
            $this->response->setMessage("Updated");
        } else {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Mandatory parameters required ");
        }
        return $this->response->json();
    }

    public function deleteAction()
    {
        $params = $this->request->getPost();
        if (isset($params["id"])) {
            DB::getInstance()->delete(
                'DELETE FROM `photo_category` WHERE `id_category` = :id', $params);
            DB::getInstance()->delete(
                'DELETE FROM `categories` WHERE `id` = :id', $params);
            $this->response->setStatus("success");
            $this->response->setMessage("Delete");
        } else {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Something went wrong");
        }
        return $this->response->json();
    }
}