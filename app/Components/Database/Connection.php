<?php
namespace Components\Database;

class Connection
{
    public $connection;

    public function __construct($host, $database, $username, $password = '', array $options = [])
    {
        if (!isset($options['driver'])) {
            $driver = 'mysql';
        } else {
            $driver = $options['driver'];
        }

        if (isset($options['port']) && $options['port'] !== null && is_numeric($options['port'])) {
            $host = $host . ':' . $options['port'];
        }

        $this->connection = new \PDO("$driver:host=$host;dbname=$database", $username, $password);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function close()
    {
        $this->connection = null;
        return $this;
    }
}