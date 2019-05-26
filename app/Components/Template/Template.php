<?php
namespace Components\Template;

class Template
{
    const VIEW = ROOT_DIR . '/public/views';

    private $path;
    private $variables;

    public function __construct($path, array $variables = [])
    {
        $this->path = $path;
        $this->variables = $variables;
    }

    public function render ()
    {
        extract($this->variables);
        include_once self::VIEW . '/' . $this->path . '.php';
        die;
    }
}