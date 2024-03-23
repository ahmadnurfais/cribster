<?php
namespace Db;

class DatabaseConnector
{
    private $connection = null;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = 3306;
        $user = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_NAME'];

        try {
            $this->connection = new \PDO("mysql:host=$host;port=$port;charset=utf8mb4;dbname=$database", $user, $password);
        } catch (\PDOException $error) {
            exit ($error->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function close() {
        $this->connection = null;
    }
}
