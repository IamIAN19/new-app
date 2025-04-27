<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountTitleController extends Controller
{
    public function index(){
        return view('account.index');
    }
}
