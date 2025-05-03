<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){

        $company_count = Company::count();
        $total_invoice = Invoice::count();
        $total_invoice_today = Invoice::whereDate('created_at', Carbon::now()->toDateString())->count();

        return view('dashboard', compact('company_count', 'total_invoice' , 'total_invoice_today' ));
    }
}
