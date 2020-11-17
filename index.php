<?php

require_once 'Base/ini.php';
use Base\Session;
use App\Controller\Index;
use Base\View;
//use App\Controller\User;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'..');
$dotenv->load();


$parts = explode('/', $_SERVER["REQUEST_URI"]);

$contrName = 'App\\Controller\\' . ucfirst($parts[1] ?? "index");
$actionName = $parts[2] . 'Action' ?? 'indexAction';

$routes = ['/login' => [\App\Controller\User::class, 'registerAction'],
    '/register' => [\App\Controller\User::class, 'registerAction']];

if (isset($routes[$_SERVER["REQUEST_URI"]])) {

    $contrName = $routes[$_SERVER["REQUEST_URI"]][0] ?? 'Index';

    $actionName = $routes[$_SERVER["REQUEST_URI"]][1]  ?? 'indexAction';
}elseif ($_SERVER["REQUEST_URI"] == '/'){
    $contrName = 'App\\Controller\\User';
    $actionName = 'registerAction';
}

Session::instance();

/** @var \Base\Controller $cotrollerName */
$cotrollerName = new $contrName;

$view = new \Base\View();

$getView = $view->setTamlatePath(__DIR__.'\\App\\View');

$cotrollerName->setView($getView);


$cotrollerName->$actionName();