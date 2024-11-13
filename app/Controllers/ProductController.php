<?php

namespace App\Controllers;

use App\Models\User;
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
}
