<?php
namespace Components\Http;

use Components\Collection\DataCollection;

class Response
{
    const PATH_TO_VIEW = ROOT_DIR . '/public/views';

    protected $content;
    protected $responseCode;

    public $headers;

    public function __construct($content = null, $responseCode = 200)
    {
        $this->responseCode = $responseCode;
        if ($content !== null) {
            $this->setContent($content);
        }
        $this->headers = new DataCollection(["Content-Type" => "text/html"]);
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function send()
    {
        $this->setHeaders();
        ob_start();
        echo $this->content;
        ob_flush();
        flush();
        die();
    }

    private function setHeaders()
    {
        http_response_code($this->responseCode);

        foreach ($this->headers->all() as $name => $value) {
            header(implode(': ', [$name, $value]));
        }

        return $this;
    }
}