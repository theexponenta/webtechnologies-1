<?php

declare(strict_types=1);

class DBSession {

    private string $host;
    private string $port;
    private string $username;
    private string $password;
    private string $dbName;

    private ?mysqli $connection;

    public function __construct($host, $port, $username, $password, $dbName) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->dbName = $dbName;
        $this->connection = null;
    }

    public function connect(): void {
        $this->connection = new mysqli($this->host.':'.$this->port, $this->username, $this->password, $this->dbName);
        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function close(): void {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function query(string $query, ?array $params = null): mysqli_result|false {
        return $this->connection->execute_query($query, $params);
    }

    public function affetedRows(): int {
        return $this->connection->affected_rows;
    }
}
