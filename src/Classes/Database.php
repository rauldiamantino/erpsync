<?php

namespace App\Classes;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
  private static ?Database $instance = null;
  private PDO $connection;

  private function __construct()
  {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();

    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];
    $charset = $_ENV['DB_CHARSET'];

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    try {

      $this->connection = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ]);
    }
    catch (PDOException $e) {
      die('Database connection error: ' . $e->getMessage());
    }
  }

  public static function getInstance(): Database
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function getConnection(): PDO
  {
    return $this->connection;
  }

  // Prevent object duplication via clone
  public function __clone() {}

  // Prevent object duplication via unserialize
  public function __wakeup() {}
}