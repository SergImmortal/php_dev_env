<?php


namespace App\Controllers;


use Core\DB;
use Core\Mvc\Controller;
use Imagick;

class PhotoController extends Controller
{
    public function listAction()
    {
        $dataSet = DB::getInstance()->select("SELECT `path`, `name`, `id` FROM `photo`");
        $links = [];
        foreach ($dataSet as $data) {
            $links[] = [
                "path" => $this->config["domain"] . $data["path"] . "min/" . $data["name"],
                "id" => $data["id"]
            ];
        }
        $this->response->setStatus("success");
        $this->response->setMessage("OK");
        $this->response->setData($links);
        return $this->response->json();
    }

    public function photoAction()
    {
        $params = $this->request->getGet();
        $data = [];
        $where = "";
        $lang = "en";
        if (!empty($params["category"])){
            $where = " LEFT JOIN photo_category pc on photo.id = pc.id_photo WHERE pc.id_category = :category";
            $data["category"]=$params["category"];
        }
        $dataSet = DB::getInstance()->select(
            "SELECT `path`, `name`, photo.id, title_{$lang} as title, description_{$lang} as description
                    FROM `photo` 
                    {$where}", $data
        );
        $links = [];
        foreach ($dataSet as $data) {
            $links[] = [
                "path" => $this->config["domain"] ,
                "name" => $data["name"],
                "id" => $data["id"],
                "min" => $data["path"] . "min/",
                "middle" => $data["path"] . "middle/",
                "full" => $data["path"],
                "title" => $data["title"],
                "description" => $data["description"]
            ];
        }
        $this->response->setStatus("success");
        $this->response->setMessage("OK");
        $this->response->setData($links);
        return $this->response->json();
    }

    public function addAction()
    {
        $uploadDir = BASE_PATH . "public/img/";
        $newFile = hash('crc32', basename($_FILES['file']['name']), false) . time() . ".jpg";
        $uploadFile = $uploadDir . $newFile;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            $this->resize($uploadFile, $uploadDir, $newFile, 320, 240, "min/");
            $this->resize($uploadFile, $uploadDir, $newFile, 640, 480, "middle/");

            $id = DB::getInstance()->insert(
                "INSERT INTO `photo` ( path, name, title_ua, title_en, description_ua, description_en) VALUES (:path, :name, :title_ua, :title_en, :description_ua, :description_en)",
                [
                    "path" => "/img/",
                    "name" => $newFile,
                    "title_ua" => $this->request->getPost("title_ua"),
                    "title_en" => $this->request->getPost("title_en"),
                    "description_ua" => $this->request->getPost("description_ua"),
                    "description_en" => $this->request->getPost("description_en")
                ]);
            if (!$id) {
                $this->response->setStatus();
                $this->response->setStatusCode(422);
                $this->response->setMessage("Fail don't inserted");
            } else {
                $params = ["id_photo" => $id];
                $query = "INSERT INTO `photo_category` ( id_photo, id_category) VALUES ";
                $paramsString = "";
                $categories = $this->request->getPost("categories");
                foreach ($categories as $category) {
                    $paramsString .= " (:id_photo, :id_c_{$category}),";
                    $params["id_c_{$category}"] = $category;
                }
                $paramsString = substr($paramsString, 0, -1);
                $categoryId = DB::getInstance()->insert(
                    $query . $paramsString,
                    $params
                );
                if (!$categoryId) {
                    $this->response->setStatus();
                    $this->response->setStatusCode(422);
                    $this->response->setMessage("Something when wrong");
                } else {
                    $this->response->setStatus("success");
                    $this->response->setStatusCode(200);
                    $this->response->setMessage("OK");
                    $this->response->setData([
                        "id" => $id,
                        "path" => $this->config["domain"] . "/img/" . $newFile
                    ]);
                }
            }
        } else {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Error fail did't upload");
        }

        return $this->response->json();
    }

    public function resize($uploadFile, $uploadDir, $newFile, $newWidth, $newHeight, $path)
    {
        $minFile = new Imagick($uploadFile);
        $imageprops = $minFile->getImageGeometry();
        $width = $imageprops["width"];
        $height = $imageprops["height"];
        if ($height > $width) {
            $newWidth = round(($width * $newHeight) / $height);

        } else {
            $newHeight = round(($height / $width) * $newWidth);
        }
        $minFile->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);
        $minFile->writeImage($uploadDir . $path . $newFile);
    }

    public function deleteAction()
    {
        $db = DB::getInstance();
        $photoData = $db->select(
            "SELECT `path`, `name` FROM `photo` WHERE id=:id",
            [
                "id" => $this->request->getPost("id")
            ]
        );
        if (empty($photoData[0])) {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Photo not found");
        } else {
            if (unlink(BASE_PATH . "public" . $photoData[0]["path"] . $photoData[0]["name"])
                && unlink(BASE_PATH . "public" . $photoData[0]["path"] . "min/" . $photoData[0]["name"])
                && unlink(BASE_PATH . "public" . $photoData[0]["path"] . "middle/" . $photoData[0]["name"])) {
                $db->delete('DELETE FROM `photo_category` WHERE `id_photo` = :id',
                    [
                        "id" => $this->request->getPost("id")
                    ]);
                $db->delete('DELETE FROM `photo` WHERE `id` = :id',
                    [
                        "id" => $this->request->getPost("id")
                    ]);
                $this->response->setStatus("success");
                $this->response->setStatusCode(200);
                $this->response->setMessage("OK");
            } else {
                $this->response->setStatus();
                $this->response->setStatusCode(422);
                $this->response->setMessage("Photo not found");
            }
        }
        return $this->response->json();
    }

    public function getPhotoAction()
    {
        $flags = DB::getInstance()->select(
            "SELECT `is_visible` AS visible, `slider_home` AS slider, `title_en`, `title_ua`, `description_en`, `description_ua` FROM `photo` WHERE id=:id",
            ["id" => $this->request->getPost("id")]
        );
        $categories = DB::getInstance()->select(
            "SELECT `id_category` FROM `photo_category` WHERE `id_photo` = :id",
            ["id" => $this->request->getPost("id")]
        );
        $this->response->setStatus("success");
        $this->response->setStatusCode(200);
        $this->response->setMessage("OK");
        $this->response->setData(
            [
                "visible" => $flags[0]["visible"],
                "slider" => $flags[0]["slider"],
                "title_ua" => $flags[0]["title_ua"],
                "title_en" => $flags[0]["title_en"],
                "description_en" => $flags[0]["description_en"],
                "description_ua" => $flags[0]["description_ua"],
                "categories" => array_column($categories, "id_category")
            ]
        );
        return $this->response->json();
    }

    public function updateAction()
    {
        DB::getInstance()->update(
            "UPDATE `photo` SET `is_visible` = :visible, `slider_home` = :slider, `title_ua` = :title_ua, `title_en` = :title_en,
                   `description_ua`= :description_ua, `description_en`=:description_en  WHERE id=:id",
            [
                "visible" => $this->request->getPost("visible"),
                "slider" => $this->request->getPost("slider"),
                "title_ua" => $this->request->getPost("title_ua"),
                "title_en" => $this->request->getPost("title_en"),
                "description_ua" => $this->request->getPost("description_ua"),
                "description_en" => $this->request->getPost("description_en"),
                "id" => $this->request->getPost("id")
            ]
        );
        DB::getInstance()->delete(
            "DELETE FROM `photo_category` WHERE `id_photo`= :id",
            ["id" => $this->request->getPost("id")]
        );
        $params = ["id_photo" => $this->request->getPost("id")];
        $query = "INSERT INTO `photo_category` ( id_photo, id_category) VALUES ";
        $paramsString = "";
        $categories = $this->request->getPost("categories");
        foreach ($categories as $category) {
            $paramsString .= " (:id_photo, :id_c_{$category}),";
            $params["id_c_{$category}"] = $category;
        }
        $paramsString = substr($paramsString, 0, -1);
        $categoryId = DB::getInstance()->insert(
            $query . $paramsString,
            $params
        );
        if (!$categoryId) {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Something when wrong");
        } else {
            $this->response->setStatus("success");
            $this->response->setStatusCode(200);
            $this->response->setMessage("OK");
        }
        return $this->response->json();
    }
}