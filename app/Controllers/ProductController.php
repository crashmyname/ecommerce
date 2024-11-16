<?php

namespace App\Controllers;

use App\Models\User;
use PDO;
use Support\BaseController;
use Support\Request;
use Support\Validator;
use Support\View;
use Support\CSRFToken;
use Support\DataTables;

class ProductController extends BaseController
{
    // Controller logic here
    public function products(Request $request)
    {
        if(Request::isAjax()){
            $data = User::all();
            return DataTables::of($data)
            ->make(true);
        }
    }

    public function index()
    {
        $count = User::count();
        $user = User::all();
        return view('home/home',['count'=>$count,'data'=>'ini adalah home','user' => $user],'navbar/navbar');
    }
}
