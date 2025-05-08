<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){

        $companies = Company::withCount([
            'invoices as total_invoices',
            'invoices as invoices_today' => function ($query) {
                $query->whereDate('created_at', today());
            }
        ])->get();

        return view('dashboard', compact('companies'));
    }
}
