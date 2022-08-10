<?php

namespace Kykurniawan\Metime;

class Request
{
    public function method(bool $upper = false)
    {
        $method = $_SERVER["REQUEST_METHOD"];
        return $upper ? strtoupper($method) : strtolower($method);
    }
}
