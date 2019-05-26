<?php
namespace Components\Router;

use Components\Http\Request;
use Components\Http\Response;
use Components\Utils\URI;

class Router
{
    const CONTROLLER = 'Controllers';
    const PATH_TO_ROUTES = ROOT_DIR . '/app/config/routing.json';

    private $routes = [];

    public function __construct(Request $request)
    {
        $this->register();
        $this->route($request);
    }


    private function register()
    {
        if (file_exists(self::PATH_TO_ROUTES)) {
            $routes = json_decode(file_get_contents(self::PATH_TO_ROUTES), true);
            $this->routes = $routes;
        }
    }

    private function route(Request $request)
    {
        $uri = new URI();

        foreach ($this->routes as $route) {
            if (strtoupper($route['method']) === $request->getMethod() && $uri->match($route['path'], $request->getURI(), $parameters)) {

                if (count($parameters) > 0) {
                    foreach ($parameters as $param => $value) {
                        $request->attributes->set($param, $value);
                    }
                }

                $controller = self::CONTROLLER . '\\' . $route['control'];
                $controller = new $controller();

                $action = $route['action'];

                if (method_exists($controller, $action)) {
                    $response = $controller->{$action}($request);

                    if ($response instanceof Response) {
                        $response->send();
                    }
                } else {
                    throw new \Exception('The method ' . $action . ' in class ' . $route['control'] . ' was not found');
                }

                die;
            }
        }

        echo 'Not Found';
    }
}