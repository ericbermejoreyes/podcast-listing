<?php
namespace Components\Http;

use Components\Collection\DataCollection;

class Request
{
    private $method;
    private $URI;

    public $request;
    public $query;
    public $attributes;
    public $headers;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->URI = preg_replace('~^/(' . BASE_DIR . '/?)~', '/', $_SERVER['REDIRECT_URL']);
        $this->headers = getallheaders();

        $this->request = new DataCollection($_GET);
        $this->attributes = new DataCollection();

        $content = file_get_contents('php://input');
        $parsedContent = [];

        if (!empty($content)) {
            if (!($parsedContent = json_decode($content, true))) {
                parse_str($content, $parsedContent);
            }
        }

        $this->query = array_merge($_POST, $parsedContent);
        $this->query = new DataCollection($this->query);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getURI()
    {
        return $this->URI;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function get($key)
    {
        if ($this->request->get($key) !== null) {
            return $this->request->get($key);
        }

        if ($this->query->get($key) !== null) {
            return $this->query->get($key);
        }

        if ($this->attributes->get($key) !== null) {
            return $this->attributes->get($key);
        }

        return null;
    }
}