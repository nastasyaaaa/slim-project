<?php

use Slim\App;

return static function (App $app) {
    $app->get('/', 'App\Http\Controllers\HomeController:test');
};