<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function list()
    {
        return view('outlet.list');
    }

    public function create()
    {
        return view('outlet.create-edit', ['id' => null]);
    }

    public function edit($id)
    {
        return view('outlet.create-edit', ['id' => $id]);
    }
}
