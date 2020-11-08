<?php

namespace App\DAO\Database;
include "NotORM.php";

abstract class Connection {

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var \NotORM
     */
    protected $db;

    public function __construct()
    {
        $host   = getenv("DB_HOST");
        $port   = getenv("DB_PORT");
        $dbname = getenv("DB_NAME");
        $user   = getenv("DB_USER");
        $pass   = getenv("DB_PASSWORD");

        $dsn = "mysql:host={$host};dbname={$dbname};port={$port};charset=utf8";

        $this->pdo = new \PDO($dsn ,$user, $pass);
        $this->db = new \NotORM($this->pdo);
    }
}

