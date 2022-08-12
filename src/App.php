<?php

namespace Kykurniawan\Metime;

use Kykurniawan\Metime\Exceptions\ConfigurationException;
use Kykurniawan\Metime\Exceptions\PageNotFoundException;
use Kykurniawan\Metime\Exceptions\RouteException;

class App
{
    private bool $debug = true;
    private array $routers = [];
    private ?string $baseUrl = null;
    private string $actionKey = "_";
    private string $defaultAction = "home";
    private RequestInterface $request;
    private ResponseInterface $response;
    private array $middlewares = [];
    private $errorCallback = null;

    /**
     * Set application debug
     * 
     * If true, all exception will showed
     * 
     * Please set this to false when app running in production
     * 
     * @param bool $debug Show debug
     * @return Kykurniawan\Metime\App Application instance
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Add on error callback function
     * 
     * If set, you must handle all exception on your hand.
     * This will ignore application debug
     * 
     * @param callable $callback On error callback
     * @return void
     */
    public function onError(callable $callback)
    {
        $this->errorCallback = $callback;
    }

    /**
     * Set base url for application
     * 
     * This is required when you want to running the application
     * 
     * @param string $baseUrl Application base url
     * @return Kykurniawan\Metime\App Application instance
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * Get application base url
     * 
     * @return string Current application base url
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set action key
     * 
     * Action key is a query param in your application url that serves to determine what action should be executed.
     * 
     * e.g., action=home or action=about
     * 
     * If not set, default action key is underscore (_).
     * 
     * @param string $actionKey Action key
     * @return Kykurniawan\Metime\App Application instance
     */
    public function setActionKey(string $actionKey)
    {
        $this->actionKey = $actionKey;
        return $this;
    }

    /**
     * Get action key
     * 
     * @return string Current action key
     */
    public function getActionKey()
    {
        return $this->actionKey;
    }

    /**
     * Set default action
     * 
     * This will be the redirect destination when the app is accessed without the action parameter in the url
     * 
     * If not set, default value is "home". You must create route with action called "home" in your router object.
     * 
     * @param string $defaultAction Default action
     * @return Kykurniawan\Metime\App Application Instance
     */
    public function setDefaultAction(string $defaultAction)
    {
        $this->defaultAction = $defaultAction;
        return $this;
    }

    /**
     * Get default action
     * 
     * @return string Default action
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * Add router object
     * 
     * When creating the object router. You must add it to the application instance so that incoming requests can be handled by your route action.
     * 
     * @param Kykurniawan\Metime\Router $router Router object
     * @return Kykurniawan\Metime\App Application instance
     */
    public function addRouter(Router $router)
    {
        array_push($this->routers, $router);
        return $this;
    }

    /**
     * Add application level middlewawre
     * 
     * If you want to have middleware run at the application level, add it here.
     * 
     * @param callable $middleware Middleware
     * @return Kykurniawan\Metime\App Application instance
     */
    public function addMiddleware(callable $middleware)
    {
        array_push($this->middlewares, $middleware);
        return $this;
    }

    /**
     * Run the Metime application
     * 
     * @return void
     */
    public function run()
    {
        $this->createInitialRequestAndResponse();

        try {
            $this->ensureAppHasValidConfigs();
            $this->ensureUrlHasAction();
            $this->runAppMiddlewares();

            foreach ($this->routers as $router) {
                $this->runRouterMiddlewares($router->middlewares());
                $this->runRouter($router);
            }
        } catch (\Throwable $th) {
            if ($this->errorCallback !== null) {
                $callback = $this->errorCallback;
                $callback($th, $this->request, $this->response);
            } else {
                if ($th instanceof PageNotFoundException) {
                    http_response_code(404);
                    echo $th->getMessage();
                    exit();
                }
                if ($this->debug) {
                    http_response_code(500);
                    throw $th;
                } else {
                    $protocol = (isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : "HTTP/1.0");
                    header($protocol . " 500 Internal Server Error");
                    $GLOBALS["http_response_code"] = 500;
                }
            }
        }
    }

    /**
     * Get current action
     * 
     * @return string Current action to handle
     */
    public function getCurrentAction()
    {
        return $_GET[$this->actionKey];
    }

    /**
     * Get current request method
     * 
     * @param bool $upper Return in uppercase or lowercase
     * @return string Current request method
     */
    public function getCurrentMethod(bool $upper = true)
    {
        $method = $_SERVER["REQUEST_METHOD"];
        return $upper ? strtoupper($method) : strtolower($method);
    }

    /**
     * Collect all the actions of the router object that have been added to the application instance
     * 
     * @return array Action list
     */
    public function collectActions()
    {
        $actions = [];
        foreach ($this->routers as $router) {
            array_push($actions, ...$router->actions());
        }

        return $actions;
    }

    /**
     * Ensure url has action parameter
     * 
     * If no action parameter in url, this will redirect and add action url with default action
     */
    private function ensureUrlHasAction()
    {
        $get = $_GET;

        if (!isset($get[$this->actionKey]) || !$get[$this->actionKey]) {
            $get[$this->actionKey] = $this->defaultAction;
            $queryString = http_build_query($get);
            header("Location:" . $this->baseUrl . "?" . $queryString);
            exit();
        }
    }

    /**
     * Ensure app has valid configuration
     * 
     * This will check configuration value before run the application
     */
    private function ensureAppHasValidConfigs()
    {
        if ($this->baseUrl === null) {
            throw new ConfigurationException("Base URL for application must be set");
        }

        if (filter_var($this->baseUrl, FILTER_VALIDATE_URL) === false) {
            throw new ConfigurationException("Invalid base URL");
        }

        if (preg_match("/^[a-zA-Z0-9_\-]+$/", $this->actionKey) == (0 || false)) {
            throw new ConfigurationException("Action key: $this->actionKey is invalid. Only alphanumeric, hyphens, and underscores are allowed.");
        }
    }

    /**
     * Run route handler
     * 
     * @param callable $handler Route handler
     * @return void
     */
    private function runHandler($handler)
    {
        $result = $handler($this->request, $this->response);
        return $this->sendResponse($result);
    }

    /**
     * Send response as result of request
     * 
     * @param string $response Response string
     * @return void
     */
    private function sendResponse($response)
    {
        header("X-Powered-By: kykurniawan/metime");

        if (is_string($response) || is_numeric($response) || is_bool($response)) {
            echo $response;
            return;
        }
        exit();
    }

    /**
     * Create initial request and response
     * 
     * @return void
     */
    private function createInitialRequestAndResponse()
    {
        $this->request = new Request($this);
        $this->response = new Response($this);
    }

    /**
     * Run application middlewares
     * 
     * @return void
     */
    private function runAppMiddlewares()
    {
        foreach ($this->middlewares as $middleware) {
            $middlewareResult = $middleware($this->request, $this->response);
            if ($middlewareResult instanceof RequestInterface) {
                $this->request = $middlewareResult;
            } else {
                $this->sendResponse($middlewareResult);
                exit();
            }
        }
    }

    /**
     * Run router middlewares
     * 
     * @param array $routerMiddlewares Router middlewares
     * @return void
     */
    private function runRouterMiddlewares($routeMiddlewares)
    {
        foreach ($routeMiddlewares as $middleware) {
            $middlewareResult = $middleware($this->request, $this->response);
            if ($middlewareResult instanceof RequestInterface) {
                $this->request = $middlewareResult;
            } else {
                $this->sendResponse($middlewareResult);
                exit();
            }
        }
    }

    /**
     * Run action middlewares
     * 
     * @param array $actionMiddlewares Action middlewares
     * @return void
     */
    private function runActionMiddlewares($actionMiddlewares)
    {
        foreach ($actionMiddlewares as $middleware) {
            $middlewareResult = $middleware($this->request, $this->response);
            if ($middlewareResult instanceof RequestInterface) {
                $this->request = $middlewareResult;
            } else {
                $this->sendResponse($middlewareResult);
                exit();
            }
        }
    }

    /**
     * Run router actions
     * 
     * @param Kykurniawan\Metime\Router $router Router object
     * @return never
     */
    private function runRouter(Router $router)
    {
        foreach ($router->actions() as $action) {

            if (preg_match("/^[a-zA-Z0-9_.\-]+$/", $action->action) == (0 || false)) {
                throw new RouteException("Action: $action->action is invalid. Only alphanumeric, hyphens, dot, and underscores are allowed.");
            }

            $actionMatch = $action->action === $this->getCurrentAction();
            $methodAllowed = $action->method === "*" || strtoupper($action->method) === $this->getCurrentMethod();

            if ($actionMatch && $methodAllowed) {
                $this->runActionMiddlewares($action->middlewares);
                $this->runHandler($action->handler);
                exit();
            }
        }
        throw new PageNotFoundException("Action " . $this->getCurrentMethod(false) . ":" . $this->getCurrentAction() . " not found.");
    }
}
