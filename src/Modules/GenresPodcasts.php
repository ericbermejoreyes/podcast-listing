<?php
namespace Modules;

class GenresPodcasts extends Module
{
    public function add(array $data)
    {
        if (!$this->exists($data['genreId'], $data['podcastId'])) {
            $query = $this->createQuery();
            $query
                ->insert($data)
                ->execute();
        }

        return $this;
    }

    public function exists($genreId, $podcastId)
    {
        $query = $this->createQuery('gp');
        $stmt = $query
            ->select('COUNT(*)')
            ->where('gp.genreId = :genreId')
            ->addWhere('gp.podcastId = :podcastId')
            ->setParameter('genreId', $genreId)
            ->setParameter('podcastId', $podcastId)
            ->execute();

        $count = $stmt->fetch(\PDO::FETCH_COLUMN);

        return intval($count) > 0;
    }
}