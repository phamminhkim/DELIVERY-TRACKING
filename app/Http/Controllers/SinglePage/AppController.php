<?php

namespace App\Http\Controllers\SinglePage;

use App\Http\Controllers\BaseController\WebBaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppController extends WebBaseController
{
    public function index(){
        return view("app.index");
    }
}
