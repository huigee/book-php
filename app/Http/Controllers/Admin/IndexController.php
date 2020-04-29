<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return view('admin/index');
    }

    public function welcome()
    {
        return view('admin/welcome');
    }

    public function toLogin()
    {
        return view('admin/login');
    }


    public function toMember()
    {
        return view('admin/member');
    }
}