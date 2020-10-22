<?php


namespace Core;


class DB
{
    private static $instances = [];
    public $connection;
    public $query;
    public $statement;

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

    public static function getInstance(): DB
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static;
        }

        return self::$instances[$class];
    }

    public function getConnection()
    {
        if(!$this->connection){
            $config = yaml_parse_file(BASE_PATH."config.yaml");
            $this->connection= new \PDO('mysql:host=localhost;dbname='.$config["db"]["name"].";charset=utf8", $config["db"]["user"], $config["db"]["password"]);
        }
        return $this->connection;
    }

    public function select($query,array $param=[])
    {
        $this->statement=$this->getConnection()->prepare($query);
        $this->statement->execute((array) $param);
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function update($query,array $param=[])
    {
        $this->statement=$this->getConnection()->prepare($query);
        $this->statement->execute($param);
        return $this->statement->fetch(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function insert($query,array $param=[])
    {
        $this->statement=$this->getConnection()->prepare($query);
        return ($this->statement->execute($param)) ? $this->connection->lastInsertId() : false;
    }

    public function delete($query,array $param=[])
    {
        $this->statement=$this->getConnection()->prepare($query);
        $this->statement->execute($param);
        return $this->statement->fetch(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}