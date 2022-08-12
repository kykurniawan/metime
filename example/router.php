<?php

use Kykurniawan\Metime\RequestInterface as Req;
use Kykurniawan\Metime\ResponseInterface as Res;
use Kykurniawan\Metime\Router;

require_once "../vendor/autoload.php";

$router = new Router;

$mid = function (Req $req, Res $res) {
    return $req;
};

$router->get("home", function (Req $req, Res $res) {
    $url = $req->app()->getBaseUrl() . "?_=hello";
    return $res->markdown("# Halaman Home\n[Ke halaman hello]($url)")->send();
}, [$mid]);

$router->get("hello", function (Req $req, Res $res) {
    $url = $req->app()->getBaseUrl() . "?_=home";
    return $res->markdown("# Halaman Hello\n[Ke halaman home]($url)")->send();
});

return $router;
