<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function list()
    {
        // Logic to list inventory items
        return view('inventory.list');
    }

    public function adjust()
    {
        // Logic to show create inventory form
        return view('inventory.create-edit', ['id' => null]);
    }
}
