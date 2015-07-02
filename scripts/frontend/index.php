<?php

$router = app('route');

$router->setRoute(function($route){
    $route->path = 'posts';
    $route->controller = 'posts/posts';
    $route->method  = 'index';
    $route->parameters = array('int' , 'string');
    return $route; // stdClass
});

// http://sitename.com
// http://sitename.com/home
$route->setRoute(function($route){
    $route->paths  = array('' , 'home');
    $route->controller = 'Main/Home';
    return $route;
});

$route->setRoute('about-us' , 'Contact/AboutUs');

//$route->notFound('Error/NotFound');