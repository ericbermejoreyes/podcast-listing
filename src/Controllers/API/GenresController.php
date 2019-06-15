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

        $iterator = $genreModule->getOrderedList($filters);
        $genres = [];

        while ($genre = $iterator->fetch(\PDO::FETCH_ASSOC)) {
            $genres[] = $genre;
        }

        $response = new JsonResponse($genres);

        return $response;
    }

    public function putGenres(Request $request)
    {
        $genres = $request->get('genres');
        $genreModule = new Genres();

        if ($genres == null) {
            return new JsonResponse([
                "error" => "genres data not found in request"
            ], 400);
        }

        foreach ($genres as $genre) {
            if ($genreModule->exists($genre['tokenId'])) {
                $genreModule->update($genre['tokenId'], $genre);
            } else {
                $genreModule->add($genre);
            }
        }

        $response = new JsonResponse([
            'result' => 'ok'
        ]);

        return $response;
    }
}