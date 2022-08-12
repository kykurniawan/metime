<?php

use Kykurniawan\Metime\App;

require_once "../vendor/autoload.php";

$router = require_once "./router.php";

$app = new App;

$app->setDebug(true);
$app->setBaseUrl("http://127.0.0.1/metime/example/");
$app->setActionKey("_");
$app->setDefaultAction("home");
$app->addRouter($router);
$app->run();
