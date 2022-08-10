<?php

namespace Kykurniawan\Metime;

class Router
{
    private array $actions = [];

    public function __construct()
    {
        // To be implemented latter
    }

    public function get($action, ...$handlers)
    {
        $this->pushAction("get", $action, $handlers);
    }

    public function actions()
    {
        return $this->actions;
    }

    private function pushAction($method, $action, $handlers)
    {
        array_push($this->actions, (object)[
            "method" => $method,
            "action" => $action,
            "handlers" => $handlers,
        ]);
    }
}
