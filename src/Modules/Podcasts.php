<?php
namespace Modules;

class Podcasts extends Module
{
    protected $fields = [
        'id',
        'tokenId',
        'host',
        'title',
        'email',
        'description',
        'note',
        'created',
        'updated',
        'starred'
    ];

    private $genres;

    public function __construct()
    {
        parent::__construct();

        $this->genres = new GenresPodcasts();
    }

    public function search(array $filters)
    {
        $parameters = [];

        $query = $this->createQuery('p');
        $query
            ->select('DISTINCT p.*')
            ->join('genres_podcasts', 'gp', 'p.id = gp.podcastId');

        foreach ($filters as $key => $value) {
            if (in_array($key, $this->fields, true) && !empty($value)) {
                $query->addWhere("p.$key = :$key");
                $parameters[$key] = $value;
            }
        }

        if (isset($filters['genreId']))
        {
            $query->addWhere("gp.genreId = :genreId");
            $parameters['genreId'] = $filters['genreId'];
        }

        if (isset($filters['search'])) {
            $ors[] = "p.host LIKE :search";
            $ors[] = "p.title LIKE :search";
            $ors[] = "p.email LIKE :search";
            $ors[] = "p.description LIKE :search";
            $ors[] = "p.note LIKE :search";

            $query->addWhere('(' . implode(' OR ', $ors) . ')');

            $parameters['search'] = "%" . $filters['search'] . "%";
        }

        if (isset($filters['page'])) {
            $query->paginate($filters['page']);
        } else {
            $query->paginate();
        }

        if ($filters !== null) {
            $query->setParameters($parameters);
        }

        return $query->getIterator();
    }

    public function add(array $data)
    {
        $query = $this->createQuery();

        $fields = [];

        foreach ($this->fields as $field) {
            if (in_array($field, array_keys($data), true)) {
                $fields[$field] = $data[$field];
            }
        }

        $query
            ->insert($fields)
            ->execute();


        $this->genres->add(['genreId' => $data['genreId'], 'podcastId' => $query->lastId()]);

        return $this;
    }

    public function update($tokenId, array $data)
    {
        $query = $this->createQuery();

        $podcastId = $this->findOne(['tokenId' => $tokenId])['id'];

        $fields = [];

        foreach ($this->fields as $field) {
            if (in_array($field, array_keys($data), true)) {
                $fields[$field] = $data[$field];
            }
        }

        $query
            ->update($fields)
            ->where('tokenId = :tokenId')
            ->setParameter('tokenId', $tokenId)
            ->execute();

        if (isset($data['genreId'])) {
            $this->genres->add(['genreId' => $data['genreId'], 'podcastId' => $podcastId]);
        }

        return $this;
    }

    public function exists($tokenId)
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