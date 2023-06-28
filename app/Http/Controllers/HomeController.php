<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController\WebBaseController;
use Illuminate\Http\Request;

class HomeController extends WebBaseController
{
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}
