<?php
use Slim\App;
use App\Middlewares\AfterMiddlewareJson;
//use App\Middleware\BeforeMiddleware;


return function (App $app) {
    $app->addBodyParsingMiddleware();       
   
    $app->add(new AfterMiddlewareJson());
    //$app->add(AfterMiddlewareJson::class);
    //$app->add(new BeforeMiddleware());
    // $app->add(BeforeMiddleware::class);  equivalente
    
};