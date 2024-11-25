<?php

use App\Middleware\CookieMiddleware;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;




return function($app)
{
    $app->post('/login', \User::class . ':login')->add(new CookieMiddleware);

    $app->get('/', function (Request $request, Response $response, array $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'login.html');
    });
    
    $app->get('/home', function (Request $request, Response $response, array $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'home.html');
    });

    $app->get('/teste', \EventsController::class . ':teste');
    $app->post('/create', \EventsController::class . ':create');
    $app->post('/update/{id}', \EventsController::class . ':update');
    $app->delete('/event/{id}', \EventsController::class . ':delete');
    $app->get('/event/{id}', \EventsController::class . ':view');
    $app->get('/events', \EventsController::class . ':list');
};
