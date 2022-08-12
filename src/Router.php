<?php

namespace Kykurniawan\Metime;

class Router
{
    private $actions = [];
    private $middlewares = [];

    /**
     * Add get route
     * 
     * @param string $action Action name
     * @param callable $handler Action handler
     * @param array $middlewares Action middleware
     * @return void
     */
    public function get(string $action, callable $handler, array $middlewares = [])
    {
        $this->pushAction("get", $action, $handler, $middlewares);
    }

    /**
     * Add post route
     * 
     * @param string $action Action name
     * @param callable $handler Action handler
     * @param array $middlewares Action middleware
     * @return void
     */
    public function post(string $action, callable $handler, array $middlewares = [])
    {
        $this->pushAction("post", $action, $handler, $middlewares);
    }

    /**
     * Add put route
     * 
     * @param string $action Action name
     * @param callable $handler Action handler
     * @param array $middlewares Action middleware
     * @return void
     */
    public function put(string $action, callable $handler, array $middlewares = [])
    {
        $this->pushAction("put", $action, $handler, $middlewares);
    }

    /**
     * Add patch route
     * 
     * @param string $action Action name
     * @param callable $handler Action handler
     * @param array $middlewares Action middleware
     * @return void
     */
    public function patch(string $action, callable $handler, array $middlewares = [])
    {
        $this->pushAction("patch", $action, $handler, $middlewares);
    }

    /**
     * Add delete route
     * 
     * @param string $action Action name
     * @param callable $handler Action handler
     * @param array $middlewares Action middleware
     * @return void
     */
    public function delete(string $action, callable $handler, array $middlewares = [])
    {
        $this->pushAction("delete", $action, $handler, $middlewares);
    }

    /**
     * Add any method route
     * 
     * @param string $action Action name
     * @param callable $handler Action handler
     * @param array $middlewares Action middleware
     * @return void
     */
    public function any(string $action, callable $handler, array $middlewares = [])
    {
        $this->pushAction("*", $action, $handler, $middlewares);
    }

    /**
     * Get action list
     * 
     * @return array Action list
     */
    public function actions()
    {
        return $this->actions;
    }

    /**
     * Add route level middleware
     * 
     * @param callable $middleware Route level middleware
     */
    public function addMiddleware(callable $middleware)
    {
        array_push($this->middlewares, $middleware);
        return $this;
    }

    /**
     * Get route level middleware list
     * 
     * @return array Route level middleware list
     */
    public function middlewares()
    {
        return $this->middlewares;
    }

    /**
     * Push action to action list
     * 
     * @param string $metdhod Action method
     * @param string $action Action name
     * @param callable $handler Action handler
     * @param array $middlewares Action middlewares
     * @return void
     */
    private function pushAction($method, $action, $handler, $middlewares)
    {
        array_push($this->actions, (object)[
            "method" => $method,
            "action" => $action,
            "handler" => $handler,
            "middlewares" => $middlewares
        ]);
    }
}
