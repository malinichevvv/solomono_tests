<?php

/**
 * Database class
 * Можно сделать singleton, но опустил для простоты
 */
class Database
{
    private string $host = 'localhost';
    private string $db_name = 'solomono_tests';
    private string $username = 'root';
    private string $password = '';
    private string $charset = 'utf8mb4';

    /**
     * @var PDO|null
     */
    public ?PDO $conn;

    /**
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        // @TODO: add validation
        if (!empty($config['host'])) $this->host = $config['host'];
        if (!empty($config['db_name'])) $this->db_name = $config['db_name'];
        if (!empty($config['username'])) $this->username = $config['username'];
        if (!empty($config['password'])) $this->password = $config['password'];
        if (!empty($config['charset'])) $this->charset = $config['charset'];

        $this->connect();
    }

    /**
     * @return void
     */
    private function connect(): void
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Ошибка подключения: " . $e->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->conn;
    }

    /**
     * @return void
     */
    public function closeConnection(): void
    {
        $this->conn = null;
    }
}