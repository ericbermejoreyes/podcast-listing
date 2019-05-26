<?php
namespace Components\Database;

class ConnectionManager
{
    private $driver;
    private $host;
    private $port;
    private $database;
    private $username;
    private $password;
    private $connections = [];

    public function __construct($driver, $host, $port, $database, $username, $password)
    {
        $this->driver = $driver;
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }

    public function getConnection () {
        if (count($this->connections) > 0) {
            return array_pop($this->connections);
        } else {
            return $this->createConnection($this->host, $this->database, $this->username, $this->password, ['driver' => $this->driver, 'port' => $this->port]);
        }
    }

    public function createConnection($host, $database, $username, $password = '', array $options = [])
    {
        return new Connection($host, $database, $username, $password, $options);
    }
}