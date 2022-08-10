<?php

namespace Kykurniawan\Metime;

interface Middleware
{
    public function do(Request $request, Response $response, callable $continue);
}
