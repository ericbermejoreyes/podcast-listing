<?php
namespace Modules;

use Components\Database\Query;

abstract class Module
{
    protected $fields;

    private $db;
    private $tbl;

    public function __construct() {
        global $db;
        $this->db = $db->getConnection();
        $this->setTableName();
    }

    protected function createQuery($alias = '')
    {
        return new Query($this->tbl, $alias, $this->db);
    }

    private function setTableName()
    {
        $this->tbl = get_class($this);
        $this->tbl = explode('\\', $this->tbl);
        $this->tbl = end($this->tbl);

        $this->tbl = preg_split('~(?=[A-Z])~', $this->tbl, -1, PREG_SPLIT_NO_EMPTY);
        $this->tbl = array_map('strtolower', $this->tbl);

        $this->tbl = implode('_', $this->tbl);

        return $this;
    }

    public function find(array $filters = null)
    {
        $parameters = [];

        $alias = $this->tbl[0];

        $query = $this->createQuery($alias);
        $query
            ->select();

        foreach ($filters as $key => $value) {
            if (in_array($key, $this->fields, true) && !empty($value)) {
                $query->addWhere("$alias.$key = :$key");
                $parameters[$key] = $value;
            }
        }

        if ($filters !== null) {
            $query->setParameters($parameters);
        }

        return $query->getResult();
    }

    public function findOne(array $filters = null)
    {
        $parameters = [];

        $alias = $this->tbl[0];

        $query = $this->createQuery($alias);
        $query
            ->select();

        foreach ($filters as $key => $value) {
            if (in_array($key, $this->fields, true) && !empty($value)) {
                $query->addWhere("$alias.$key = :$key");
                $parameters[$key] = $value;
            }
        }

        if ($filters !== null) {
            $query->setParameters($parameters);
        }

        return $query->getOneOrNullResult();
    }
}