<?php

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use App\Auth;

$uri = $_SERVER['REQUEST_URI'];

$method = $_SERVER['REQUEST_METHOD'];

if($_SERVER['HTTP_HOST'] == "localhost") {
    $uri = str_replace('/simple_orm', "", $uri);
}

$collector = new RouteCollector();

App\Session::start();



$collector->get('/', function() {
    $controller = new App\Controllers\HomeController();
    $controller->index();
});

$collector->get('/home', function() {
    $controller = new App\Controllers\HomeController();
    $controller->index();
});

$dispatcher =  new Dispatcher($collector->getData());