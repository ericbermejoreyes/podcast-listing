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