# Metime PHP

Express JS-like PHP framework

## Simple usage

```php
<?php

use Kykurniawan\Metime\App;
use Kykurniawan\Metime\RequestInterface as Req;
use Kykurniawan\Metime\ResponseInterface as Res;
use Kykurniawan\Metime\Router;

require_once "../vendor/autoload.php";

// Create router
$router = new Router;
// Add handler for home action
$router->get("home", function (Req $req, Res $res) {
    return $res->markdown("# Home Page")->send();
});

// Create application
$app = new App;
// Setup base url
$app->setBaseUrl("http://127.0.0.1/");
// Add router to the application
$app->addRouter($router);
// Run application
$app->run();
```
