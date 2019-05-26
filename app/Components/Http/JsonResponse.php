<?php
namespace Components\Http;

class JsonResponse extends Response
{
    public function __construct(array $content = null)
    {
        parent::__construct();

        if ($content !== null) {
            $this->setContent($content);
        }

        $this->headers->set('Content-Type', 'application/json');
    }

    public function setContent($content)
    {
        $this->content = json_encode($content);

        return $this;
    }
}