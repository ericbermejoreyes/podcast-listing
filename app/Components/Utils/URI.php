<?php
namespace Components\Utils;

class URI
{
    public function match($pattern, $uri, &$parameters = [])
    {
        $parameters = [];
        $pattern = preg_split('~\/~', strtolower($pattern), -1,PREG_SPLIT_NO_EMPTY);
        $uri = preg_split('~\/~', strtolower($uri), -1, PREG_SPLIT_NO_EMPTY);

        if (count($pattern) !== count($uri)) {
            return false;
        }

        foreach ($pattern as $key => $segment) {
            if ($segment[0] === ':') {
                $parameters[str_replace(':', '', $segment)] = $uri[$key];
            } else if ($segment !== $uri[$key]) {
                return false;
            }
        }

        return true;
    }
}