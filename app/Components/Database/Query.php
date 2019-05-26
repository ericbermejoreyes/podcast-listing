<?php
namespace Components\Database;

class Query
{
    const SELECT = 'SELECT';
    const INSERT = 'INSERT INTO';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE FROM';

    private $db;
    private $query;
    private $parameters;
    private $table;
    private $tableAs;
    private $alias;
    private $wheres = [];
    private $select;
    private $insert;
    private $delete;
    private $update;

    public function __construct($table, $alias, $db)
    {
        $this->table = $table;
        $this->alias = $alias;
        $this->tableAs = $table . ' AS ' . $alias;
        $this->db = $db;
    }

    public function select($statement = '*')
    {
        $this->select = $statement;
        return $this;
    }

    public function insert(array $values)
    {
        $this->insert = array_map(function ($value) {
            return '\'' . addslashes($value) . '\'';
        }, $values);
        return $this;
    }

    public function update(array $values)
    {
        foreach ($values as $key => $value) {
            $this->update[] = $key . ' = ' . '\'' . addslashes($value) . '\'';
        }
        return $this;
    }

    public function delete(...$conditions)
    {
        $this->delete = $conditions;
        return $this;
    }

    public function where($statement)
    {
        $this->wheres = [$statement];
        return $this;
    }

    public function addWhere($statement)
    {
        $this->wheres[] = $statement;
        return $this;
    }

    public function getQueryString()
    {
        if (!empty($this->select)) {
            $this
                ->resetQuery()
                ->appendQuery(self::SELECT)
                ->appendQuery($this->select)
                ->appendQuery('FROM ' . $this->tableAs);

            if (!empty($this->wheres)) {
                $this->appendQuery($this->getWheres());
            }
        } else if (!empty($this->insert)) {
            $this
                ->resetQuery()
                ->appendQuery(self::INSERT)
                ->appendQuery($this->table)
                ->appendQuery('(' . implode(', ', array_keys($this->insert)) . ')')
                ->appendQuery('VALUES (' . implode(', ', $this->insert) . ')');
        } else if (!empty($this->update)) {
            $this
                ->resetQuery()
                ->appendQuery(self::UPDATE)
                ->appendQuery($this->table)
                ->appendQuery('SET ' . implode(', ', $this->update));

            if (!empty($this->wheres)) {
                $this->appendQuery($this->getWheres());
            }
        } else if (!empty($this->wheres = $this->delete)) {
            $this
                ->resetQuery()
                ->appendQuery(self::DELETE)
                ->appendQuery($this->table)
                ->appendQuery($this->getWheres());
        }

        return implode(' ', $this->query);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $keys = array_keys($parameters);
        $keys = array_map(function ($key) {
            return ':' . $key;
        }, $keys);

        $this->parameters = array_combine($keys, $parameters);

        return $this;
    }

    public function setParameter($name, $value)
    {
        $this->parameters[':' . $name] = $value;
        return $this;
    }

    public function execute()
    {
        $statement = $this->db->connection->prepare($this->getQueryString());

        if (!empty($this->parameters)) {
            $statement->execute($this->parameters);
        } else {
            $statement->execute();
        }

        return $statement;
    }

    private function getWheres()
    {
        return 'WHERE ' . implode(' AND ', $this->wheres);
    }

    private function resetQuery()
    {
        $this->query = null;
        return $this;
    }

    private function appendQuery($string) {
        $this->query[] = $string;
        return $this;
    }
}