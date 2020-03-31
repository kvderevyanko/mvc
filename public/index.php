<?php

session_start();

require dirname(__DIR__) . '/vendor/autoload.php';

//Обработка ошибок
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


$router = new Core\Router();

//Маршрут по умолчанию
$router->add('', ['controller' => 'Tasks', 'action' => 'index']);
$router->add('{controller}/{action}');
    
$router->dispatch($_SERVER['QUERY_STRING']);
