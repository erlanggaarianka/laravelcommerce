<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    public function list()
    {
        // Logic to list transaction types
        return view('transaction-type.list');
    }

    public function create()
    {
        // Logic to show create form
        return view('transaction-type.create-edit', ['id' => null]);
    }

    public function edit($id)
    {
        // Logic to show edit form
        return view('transaction-type.create-edit', ['id' => $id]);
    }
}
