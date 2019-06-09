<?php
namespace Modules;

class Genres extends Module
{
    protected $fields = [
       'id',
       'tokenId',
       'name',
       'created'
    ];

    public function getOrderedList(array $filters)
    {
        $query = $this->createQuery('g');
        $parameters = [];

        $query
            ->select();

        foreach ($filters as $field => $value) {
            if (in_array($field, $this->fields, true)) {
                $query->addWhere("g.$field = :$field");
                $parameters[$field] = $value;
            }
        }

        $query
            ->setParameters($parameters)
            ->orderBy('g.name');

        return $query->getIterator();
    }

    public function add(array $data)
    {
        $query = $this->createQuery();

        $query
            ->insert($data)
            ->execute();

        return $this;
    }

    public function update($tokenId, array $data)
    {
        $query = $this->createQuery();

        $query
            ->update($data)
            ->where('tokenId = :tokenId')
            ->setParameter('tokenId', $tokenId)
            ->execute();

        return $this;
    }

    public function exists($tokenId)
    {
        $query = $this->createQuery('g');

        $stmt = $query
            ->select('COUNT(g.id)')
            ->where('g.tokenId = :tokenId')
            ->setParameter('tokenId', $tokenId)
            ->execute();

        $count = $stmt->fetch(\PDO::FETCH_COLUMN);

        return intval($count) > 0;
    }
}