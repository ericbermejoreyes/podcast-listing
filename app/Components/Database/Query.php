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
    private $paginate;
    private $join;
    private $count = false;

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
            return '\'' . addslashes(strip_tags($value)) . '\'';
        }, $values);
        return $this;
    }

    public function update(array $values)
    {
        foreach ($values as $key => $value) {
            $this->update[] = $key . ' = ' . '\'' . addslashes(strip_tags($value)) . '\'';
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

    public function getSql()
    {
        if (!empty($this->select)) {
            $this
                ->resetQuery()
                ->appendQuery(self::SELECT)
                ->appendQuery($this->select)
                ->appendQuery('FROM ' . $this->tableAs);

            if ($this->join !== null) {
                $this->appendQuery(implode(' ', $this->join));
            }

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

        if ($this->paginate !== null) {
            $this->appendQuery($this->paginate);
        }

        if ($this->count === true) {
            return 'SELECT COUNT(*) FROM (' . implode(' ', $this->query) . ') AS count';
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

    public function join($table, $alias, $on)
    {
        $this->join[] = "JOIN $table AS $alias";
        $this->join[] = "ON $on";

        return $this;
    }

    public function leftJoin($table, $alias, $on)
    {
        $this->join[] = "LEFT JOIN $table AS $alias";
        $this->join[] = "ON $on";

        return $this;
    }

    public function rightJoin($table, $alias, $on)
    {
        $this->join[] = "RIGHT JOIN $table AS $alias";
        $this->join[] = "ON $on";

        return $this;
    }

    public function innerJoin($table, $alias, $on)
    {
        $this->join[] = "INNER JOIN $table AS $alias";
        $this->join[] = "ON $on";

        return $this;
    }

    public function execute()
    {
        $statement = $this->db->connection->prepare($this->getSql());

        if (!empty($this->parameters)) {
            $statement->execute($this->parameters);
        } else {
            $statement->execute();
        }

        return $statement;
    }

    public function count()
    {
        $this->count = true;

        return $this;
    }

    public function paginate($page = 1, $maxResult = 20)
    {
        $page = $page < 0 ? 0 : $page - 1;
        $this->paginate = sprintf('LIMIT %d OFFSET %d', $maxResult, ($maxResult * $page));

        return $this;
    }

    public function getResult()
    {
        $stmt = $this->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOneOrNullResult()
    {
        $stmt = $this->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return empty($result) ? null : $result;
    }

    public function getIterator()
    {
        return $this->execute();
    }

    public function lastId()
    {
        if ($this->insert === null) {
            return null;
        }

        return $this->db->connection->lastInsertId();
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