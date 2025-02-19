<?php

namespace Root\Html;

use PDO;

class Database {
    private $host = 'php-oop-exercice-db';
    private $db = 'blog';
    private $user = 'root';
    private $password = 'password';
    private $pdo;

    public function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=UTF8";
        $this->pdo = new PDO($dsn, $this->user, $this->password);
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}