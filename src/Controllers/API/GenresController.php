<?php
namespace Controllers\API;

use Components\Controller\Controller;
use Components\Http\JsonResponse;
use Components\Http\Request;;
use Modules\Genres;

class GenresController extends Controller
{
    public function getGenres(Request $request)
    {
        $module = new Genres();

        $filters = [];

        foreach($module::FIELDS as $field)
        {
            if (($value = $request->request->get($field)) !== null) {
                $filters[$field] = $value;
            }
        }

        $genres = $module->findGenres($filters);

        $response = new JsonResponse($genres);

        return $response;
    }

    public function putGenres(Request $request)
    {
        $genres = $request->query->get('genres');
        $module = new Genres();

        foreach ($genres as $genre) {
            if ($module->genreExists($genre['tokenId'])) {
                $module->updateGenre($genre['tokenId'], $genre);
            } else {
                $module->addGenre($genre);
            }
        }

        $response = new JsonResponse();

        return $response;
    }
}