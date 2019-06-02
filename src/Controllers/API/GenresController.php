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
        $genreModule = new Genres();

        $filters = $request->request->all();

        $genres = $genreModule->find($filters);

        $response = new JsonResponse($genres);

        return $response;
    }

    public function putGenres(Request $request)
    {
        $genres = $request->query->get('genres');
        $genreModule = new Genres();

        foreach ($genres as $genre) {
            if ($genreModule->exists($genre['tokenId'])) {
                $genreModule->update($genre['tokenId'], $genre);
            } else {
                $genreModule->add($genre);
            }
        }

        $response = new JsonResponse();

        return $response;
    }
}