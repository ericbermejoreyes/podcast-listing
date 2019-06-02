<?php
namespace Controllers\API;

use Components\Http\JsonResponse;
use Components\Http\Request;
use Modules\Podcasts;

class PodcastsController
{
    public function getPodcasts(Request $request)
    {
        $podcastModule = new Podcasts();

        $filters = $request->request->all();

        $iterator = $podcastModule->search($filters);

        $podcasts = [];

        while ($podcast = $iterator->fetch(\PDO::FETCH_ASSOC)) {
            $podcasts[] = $podcast;
        }

        $response = new JsonResponse($podcasts);

        return $response;
    }

    public function putPodcasts(Request $request)
    {
        $podcasts = $request->query->get('podcasts');
        $podcastModule = new Podcasts();

        foreach ($podcasts as $podcast) {
            if ($podcastModule->exists($podcast['tokenId'])) {
                $podcastModule->update($podcast['tokenId'], $podcast);
            } else {
                $podcastModule->add($podcast);
            }
        }

        $response = new JsonResponse([
            'result' => 'ok'
        ]);

        return $response;
    }
}