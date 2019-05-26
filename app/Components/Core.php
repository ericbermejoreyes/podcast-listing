<?php
namespace Components;

use Components\Database\ConnectionManager;
use Components\Router\Router;
use Components\Http\Request;



// global variables
$config = null;
$db = null;



class Core
{
    const PATH_TO_CONFIG = ROOT_DIR . '/app/config/config.json';

    private $router;

    public function __construct()
    {
        try {
            $this->init();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    private function loadConfig()
    {
        global $config;

        if (file_exists(self::PATH_TO_CONFIG)) {
            $config = json_decode(file_get_contents(self::PATH_TO_CONFIG), true);
        }
    }

    private function init()
    {
        $this->loadConfig();

        global $db;
        global $config;

        $dbConf = $config['database'];

        $db = new ConnectionManager($dbConf['driver'], $dbConf['host'], $dbConf['port'], $dbConf['database'], $dbConf['username'], $dbConf['password']);

        $request = new Request();
        $this->router = new Router($request);
    }
}