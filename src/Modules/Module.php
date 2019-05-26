<?php
namespace Modules;

use Components\Database\Query;

abstract class Module
{
    private $db;
    private $tbl;

    public function __construct() {
        global $db;
        $this->db = $db->getConnection();
        $this->tbl = get_class($this);
        $this->tbl = explode('\\', $this->tbl);
        $this->tbl = strtolower(end($this->tbl));
    }

    protected function createQuery($alias = '')
    {
        return new Query($this->tbl, $alias, $this->db);
    }
}