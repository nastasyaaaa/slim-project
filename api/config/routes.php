<?php

use Slim\App;
use App\Http\Actions\HomeAction;

return static function (App $app) {
    $app->get('/', 'App\Http\Controllers\HomeController:test');
};