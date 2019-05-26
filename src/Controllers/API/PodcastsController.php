<?php
namespace Controllers\API;

use Components\Http\JsonResponse;
use Components\Http\Request;
use Modules\Podcasts;

class PodcastsController
{
    public function getPodcasts(Request $request)
    {
        $module = new Podcasts();

        $filters = [];

        foreach($module::FIELDS as $field)
        {
            if (($value = $request->request->get($field)) !== null) {
                $filters[$field] = $value;
            }
        }

        $podcasts = $module->findPodcasts($filters);

        $response = new JsonResponse($podcasts);

        return $response;
    }

    public function putPodcasts(Request $request)
    {
        $podcasts = $request->query->get('podcasts');
        $module = new Podcasts();

        foreach ($podcasts as $podcast) {
            if ($module->podcastExists($podcast['tokenId'])) {
                $module->updatePodcast($podcast['tokenId'], $podcast);
            } else {
                $module->addPodcast($podcast);
            }
        }

        $response = new JsonResponse([
            'result' => 'ok'
        ]);

        return $response;
    }
}