<?php


namespace App\Controllers;


use Core\ACL;
use Core\DB;
use Core\Mvc\Controller;

class UserController extends Controller
{
    const PUBLIC = 1;
    const ADMIN = 2;
    const USER = 3;
    const ROLES = [
        self::PUBLIC=>"public",
        self::ADMIN=>"admin",
        self::USER=>"user"
    ];

    public function loginAction()
    {
        $email = $this->request->getPost("email");
        if (!empty($email)){
            $password = $this->request->getPost("password");
            $user = DB::getInstance()->select('SELECT *  FROM `users` WHERE email=:email',
                [
                    "email" => $this->request->getPost("email")
                ]);
            if (isset($user[0])) {
                if (password_verify($password . $this->config["salt"], $user[0]["password"])) {
                    $this->session->createSession();
                    $this->session->setSession("user_id", $user[0]["id"]);
                    $this->session->setSession("role_id",$user[0]["role_id"]);
                    $this->response->setStatus("success");
                    $this->response->setMessage("The password is correct!");
                } else {
                    $this->response->setStatus();
                    $this->response->setStatusCode(422);
                    $this->response->setMessage("The password is incorrect.");
                }
            }
        }else {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Email ia required.");
        }
        return $this->response->json();
    }

    public function logoutAction()
    {
        $this->session->destroySession();
        $this->response->setStatus("success");
        $this->response->setStatusCode(200);
        $this->response->setMessage("The session is destroy.");
        return $this->response->json();
    }

    public function getAction()
    {
        $data = DB::getInstance()->select(
            'SELECT r.resource, r.permission FROM permissions 
                  LEFT JOIN roles ON permissions.role_id = roles.id 
                  LEFT JOIN resources r on permissions.resource_id = r.id
                  WHERE role_id IN (:id, :role_id)',
        [
            "id"=>static::PUBLIC,
            "role_id"=>$this->session->getSession("role_id")
        ]);
        if ($this->session->getSession("role_id")!= false){
            $role = self::ROLES[$this->session->getSession("role_id")];
        }else{
            $role = self::ROLES[self::PUBLIC];
        }
        if (!$data) {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Resources not found");
        } else {
            $this->session->createSession();
            $this->response->setStatus("success");
            $this->response->setMessage("OK");
            $this->response->setData([
                "role"=>$role,
                "routes"=>$data
            ]);
        }
        return $this->response->json();
    }

    public function createAction()
    {
        $email = $this->request->getPost("email");
        if ($email){
           $user =  DB::getInstance()->select("SELECT * FROM `users` WHERE email=:email",
            [
                "email"=>$this->request->getPost("email")
            ]);
           if (count($user)!=0){
               $this->response->setStatus();
               $this->response->setStatusCode(422);
               $this->response->setMessage("User ready exist");
           }
           else {
               $password = $this->request->getPost("password");
               if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])((?=.*[0-9])|(?=.*[!@#$%\^&\*]))(?=.{8,20})/', $password)) {
                   $passwordUser = password_hash($password . $this->config["salt"], PASSWORD_BCRYPT);
                   $id = DB::getInstance()->insert(
                       "INSERT INTO `users` (role_id, user_agent, ip ,email, password, username) VALUES (:role_id, :user_agent, :ip, :email, :password, :username)",
                       [
                           "role_id" => static::USER,
                           "user_agent" => $_SERVER['HTTP_USER_AGENT'],
                           "ip" => isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'],
                           "email" => $this->request->getPost("email"),
                           "password" => $passwordUser,
                           "username" => $this->request->getPost("username")
                       ]);
                   if (!$id) {
                       $this->response->setStatus();
                       $this->response->setStatusCode(422);
                       $this->response->setMessage("User don't inserted");
                   } else {
                       $this->session->createSession();
                       $this->session->setSession("role_id", static::USER);
                       $this->session->setSession("user_id", $id);
                       $this->response->setStatus("success");
                       $this->response->setMessage("OK");
                   }
               }
           }
        } else {
            $this->response->setStatus();
            $this->response->setStatusCode(422);
            $this->response->setMessage("Email is required");
        }
        return $this->response->json();
    }
}