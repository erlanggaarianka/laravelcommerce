<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function list()
    {
        return view('payment-method.list');
    }

    public function create()
    {
        return view('payment-method.create-edit', ['id' => null]);
    }

    public function edit($id)
    {
        return view('payment-method.create-edit', ['id' => $id]);
    }
}
