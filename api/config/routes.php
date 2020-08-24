<?php

use Slim\App;

return static function (App $app) {
    $app->get('/', 'App\Http\Controllers\HomeController:test');

    $app->group('/v1', function (\Slim\Routing\RouteCollectorProxy $group) {
       $group->group('/auth', function (\Slim\Routing\RouteCollectorProxy $group) {
           $group->post('/join', 'App\Http\Actions\V1\Auth\Join\RequestAction:handle');
           $group->post('/confirm', 'App\Http\Actions\V1\Auth\Join\ConfirmAction:handle');
       });
    });
};