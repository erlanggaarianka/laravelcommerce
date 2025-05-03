<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function list()
    {
        return view('account.list');
    }

    public function create()
    {
        return view('account.create-edit');
    }

    public function edit($id)
    {
        return view('account.create-edit', ['id' => $id]);
    }
}
