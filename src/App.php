<?php

namespace Kykurniawan\Metime;

use Exception;

class App
{
    private array $routers = [];
    private ?string $baseUrl = null;
    private string $actionKey = "action";
    private string $defaultAction = "home";

    public function __construct()
    {
        // To be implemented latter
    }

    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function setActionKey(string $actionKey)
    {
        $this->actionKey = $actionKey;
        return $this;
    }

    public function setDefaultAction(string $action)
    {
        $this->defaultAction = $action;
        return $this;
    }

    public function addRouter(Router $router)
    {
        array_push($this->routers, $router);
        return $this;
    }

    public function run()
    {
        try {
            $this->ensureAppHasValidConfigs();
            $this->ensureUrlHasAction();

            $actions = $this->collectRouteActions();

            foreach ($actions as $action) {
                if ($action->action === $this->getCurrentAction()) {
                    foreach ($action->handlers as $handler) {
                        $request = new Request;
                        $response = new Response;
                        $handler($request, $response);
                    }
                    exit();
                }
            }

            echo "Action " . $this->getCurrentAction() . " not found.";
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    private function ensureUrlHasAction()
    {
        $get = $_GET;

        if (!isset($get[$this->actionKey]) || !$get[$this->actionKey]) {
            $get[$this->actionKey] = $this->defaultAction;
            $queryString = http_build_query($get);
            header("Location:" . $this->baseURL . "?" . $queryString);
            exit();
        }
    }

    private function ensureAppHasValidConfigs()
    {
        if ($this->baseUrl === null) {
            throw new Exception("App must have base url");
        }
    }

    private function collectRouteActions()
    {
        $actions = [];
        foreach ($this->routers as $router) {
            array_push($actions, ...$router->actions());
        }

        return $actions;
    }

    private function getCurrentAction()
    {
        return $_GET[$this->actionKey];
    }
}
