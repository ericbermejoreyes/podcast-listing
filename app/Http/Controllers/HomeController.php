<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('spreadsheet.index');
    }

    public function loadGUI(Request $request)
    {
        if ($request->ajax()) {
            $genres = $request->get('genres');
            $genreGroup = [];


            usort($genres, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            $letter = null;
            foreach ($genres as $genre) {
                if ($letter === null || stripos($genre['name'], $letter) !== 0) {
                    $letter = substr($genre['name'], 0, 1);
                    $letter = strtoupper($letter);
                }

                $genreGroup[$letter][] = $genre;
            }

            $data = [
                'genreGroup' => $genreGroup
            ];

            return view('spreadsheet.gui', $data);
        }

        return '';
    }

    public function saveSpreadsheet(Request $request)
    {
        if ($request->ajax()) {
            $confDir = '../config/config.json';
            $spreadsheetId = $request->get('spreadsheet_id');

            if (is_file($confDir)) {
                $config = json_decode(file_get_contents($confDir), true);
                $config['_google']['spreadsheetId'] = $spreadsheetId;

                file_put_contents($confDir, json_encode($config, JSON_PRETTY_PRINT));
            }
        }

        return 'success';
    }
}