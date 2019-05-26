<?php
namespace Components\Controller;

use Components\Template\Template;

class Controller
{
    protected function renderView($view, array $data = [])
    {
        $template = new Template($view, $data);
        $template->render();
    }

    protected function redirect($path)
    {
        header('Location: ' . $path);
    }
}