<?php


namespace Core;


use App\Controllers\UserController;

class ACL
{
    private static $instances = [];
    public $permission = [];
    public $roleID = false;


    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): ACL
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static;
        }

        return self::$instances[$class];
    }

    public function setRoleID($roleID)
    {
        $this->roleID = $roleID;
    }

    public function getPermissions()
    {
        $params = [];

        if (empty($this->permission)){
            if ($this->roleID == false) {
                $where = ":id";
                $params["id"] = UserController::PUBLIC;
            } else {
                $where = ":id, :role_id";
                $params = [
                    "id" => UserController::PUBLIC,
                    "role_id" => $this->roleID
                ];
            }
            $this->permission = DB::getInstance()->select(
                "SELECT r.resource, r.permission FROM permissions 
                  LEFT JOIN roles ON permissions.role_id = roles.id 
                  LEFT JOIN resources r on permissions.resource_id = r.id
                  WHERE role_id IN ({$where})",
                $params);
        }
    }

    public function isAllowed($resource, $permission)
    {
        $this->getPermissions();
        $access = false;
        foreach ($this->permission as $key => $value) {
            if ($value["resource"] == lcfirst($resource) and $value["permission"] == $permission) {
                $access = true;
            }
        }
        return $access;
    }

}