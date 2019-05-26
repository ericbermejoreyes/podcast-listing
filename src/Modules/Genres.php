<?php
namespace Modules;

class Genres extends Module
{
    const FIELDS = [
       'id',
       'tokenId',
       'name',
       'created'
    ];

    public function findGenres(array $filters = null)
    {
        $query = $this->createQuery('g');

        $query->select();

        foreach ($filters as $key => $value) {
            $query->addWhere('g.' . $key . ' = :' .$key);
        }

        if ($filters !== null) {
            $query->setParameters($filters);
        }

        $stmt = $query->execute();

        $genres = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $genres[] = $row;
        }

        return [
            'total' => count($genres),
            'genres' => $genres
        ];
    }

    public function addGenre(array $data)
    {
        $query = $this->createQuery();

        $query
            ->insert($data)
            ->execute();

        return $this;
    }

    public function updateGenre($tokenId, array $data)
    {
        $query = $this->createQuery();

        $query
            ->update($data)
            ->where('tokenId = :tokenId')
            ->setParameter('tokenId', $tokenId)
            ->execute();

        return $this;
    }

    public function genreExists($tokenId)
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