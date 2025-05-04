<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function list()
    {
        // Logic to list products
        return view('products.list');
    }

    public function create()
    {
        // Logic to show product creation form
        return view('products.create-edit', ['id' => null]);
    }

    public function edit($id)
    {
        // Logic to show product edit form
        return view('products.create-edit', ['id' => $id]);
    }
}
