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

    public function delivery_partner(){
        return view("app.delivery_partners");
    }
    public function delivery_user(){
        return view("app.delivery_user");
    }
    public function delivery_customer(){
        return view("app.delivery_customer");
    }
}
