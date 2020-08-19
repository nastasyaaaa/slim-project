<?php

namespace App\Http\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends Controller
{
    public function test(Request $request, Response $response)
    {
        return $this->jsonResponse('Hello, nana');
    }
}