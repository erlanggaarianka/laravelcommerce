<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function list()
    {
        // Logic to list transactions
        return view('transactions.list');
    }

    public function create()
    {
        // Logic to show create transaction form
        return view('transactions.create-edit', ['id' => null]);
    }

    public function receipt($id)
    {
        $transaction = Transaction::with(['items.product', 'user', 'outlet'])->findOrFail($id);
        $outlet = $transaction->outlet;

        return view('transactions.receipt', compact('transaction', 'outlet'));
    }
}
