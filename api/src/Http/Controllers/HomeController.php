<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function hello()
    {
        return $this->jsonResponse('Hello, nana');
    }
}