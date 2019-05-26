<?php
namespace Components\Collection;

class DataCollection
{
    private $bag = [];

    public function __construct(array $array = [])
    {
        $this->bag = $array;
    }

    public function all()
    {
        return $this->bag;
    }

    public function get($key)
    {
        return isset($this->bag[$key]) ? $this->bag[$key] : null;
    }

    public function set($key, $value)
    {
        $this->bag[$key] = $value;
        return $this;
    }
}