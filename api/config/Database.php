<?php

require __DIR__ . '../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Database {
    private static $instance = null;
    private $databaseConnection;
    private $host;
    private $databaseName;
    private $userName;
    private $password;

    public function __construct() {
        $this->host = $_ENV['localhost'];
        $this->databaseName = $_ENV['phprest'];
        $this->userName = $_ENV[''];
        $this->password = $_ENV[''];

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->databaseName}",
                $this->userName,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            error_log("Database Connection Error: " . $exception->getMessage());
            die("Database connection failed. Contact administrator.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
