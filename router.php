<?php

use App\Middleware\CookieMiddleware;


return function($app)
{
    $app->post('/login', \User::class . ':login')->add(new CookieMiddleware);

    $app->get('/teste', \EventsController::class . ':teste');
    $app->post('/create', \EventsController::class . ':create');
    $app->post('/update/{id}', \EventsController::class . ':update');
    $app->delete('/event/{id}', \EventsController::class . ':delete');
    $app->get('/events/{year}/{month}', \EventsController::class . ':list');
};
