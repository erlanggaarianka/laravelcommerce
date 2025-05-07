<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function view()
    {
        // Logic to show report view
        return view('reports.view');
    }
}
