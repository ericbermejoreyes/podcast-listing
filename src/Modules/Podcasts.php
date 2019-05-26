<?php
namespace Modules;

class Podcasts extends Module
{
    const FIELDS = [
        'id',
        'tokenId',
        'genreId',
        'host',
        'title',
        'email',
        'description',
        'note',
        'created',
        'updated'
    ];

    public function findPodcasts(array $filters = null)
    {
        $query = $this->createQuery('p');

        $query->select();

        foreach ($filters as $key => $value) {
            $query->addWhere('p.' . $key . ' = :' .$key);
        }

        if ($filters !== null) {
            $query->setParameters($filters);
        }

        $stmt = $query->execute();

        $podcasts = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $podcasts[] = $row;
        }

        return [
            'total' => count($podcasts),
            'podcasts' => $podcasts
        ];
    }

    public function addPodcast(array $data)
    {
        $query = $this->createQuery();

        $query
            ->insert($data)
            ->execute();

        return $this;
    }

    public function updatePodcast($tokenId, array $data)
    {
        $query = $this->createQuery();

        $query
            ->update($data)
            ->where('tokenId = :tokenId')
            ->setParameter('tokenId', $tokenId)
            ->execute();

        return $this;
    }

    public function podcastExists($tokenId)
    {
        $query = $this->createQuery('p');

        $stmt = $query
            ->select('COUNT(p.id)')
            ->where('p.tokenId = :tokenId')
            ->setParameter('tokenId', $tokenId)
            ->execute();

        $count = $stmt->fetch(\PDO::FETCH_COLUMN);

        return intval($count) > 0;
    }
}