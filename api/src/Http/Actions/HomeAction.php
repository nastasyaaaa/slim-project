<?php

namespace App\Http\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeAction extends Action
{
    public function handle(Request $request): Response
    {
        return $this->jsonResponse(['name' => 'Nana', 'age' => 20]);
    }
}