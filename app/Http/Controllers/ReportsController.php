<?php

namespace App\Http\Controllers;

use App\Exports\CashReportExport;
use App\Exports\DisbursementReportExport;
use App\Exports\LedgerReportExport;
use App\Models\AccountSub;
use App\Models\AccountTitle;
use App\Models\Company;
use App\Models\Department;
use App\Models\Invoice;
use App\Models\InvoicesOtherExpenses;
use App\Models\InvoiceSub;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function index(){
        $company = Company::select('id', 'name')->get();

        return view('reports.index', compact('company'));
    }

    public function fetchContent( Request $request)
    {
        if($request->ajax()){

            $request->validate([
                'company' => 'required',
                'type' => 'required',
                'customfilter' => 'required'
            ]);

            $date  = explode('-', request()->input('customfilter'));
            $from  = Carbon::parse(trim($date[0]))->format('Y-m-d');
            $company = $request->company;
            $to    = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $from;

            switch( $request->type ){
                case 'ledger':
                    $html = $this->getLedgerReport($from, $to, $company);
                    break;
                case 'slsl':
                    $html = view('reports.slsl')->render();
                    break;
                case 'disbursement':
                    $html = $this->getDisbursementReport($from, $to, $company);
                    break;
                case 'cash':
                    $html = $this->getCashReport($from, $to, $company);
                    break;
                default:
                    $html = view('reports.ledger')->render();
            }

            return response()->json(['html' => $html], 200);
        }

        return abort(403, 'Unauthorized!');
    }

    private function getDisbursementReport($from, $to, $company){
        // Get first the departments to use in condition
        $sale_id = Department::where('name', 'Sales')->select('id')->first()->id;

        $invoices = Invoice::with([
            'invoiceOthers.accountTitle',
            'invoiceOthers.invoiceSubs.accountSub',
        ])
        ->whereBetween('created_at', [$from, $to])
        ->where('company_id', $company)
        ->where('department_id', '!=', $sale_id)
        ->paginate(100); // paginate for performance

        return view('reports.disbursement', compact('invoices','from', 'to', 'company'))->render();
    }

    public function exportDisbursementReport(Request $request)
    {
        $company_name = Company::select('name')->find($request->company)->name;

        // Get first the departments to use in condition
        $sale_id = Department::where('name', 'Sales')->select('id')->first()->id;

        return Excel::download(new DisbursementReportExport($request->from, $request->to, $request->company, $company_name, $sale_id), 'disbursement_report.xlsx');
    }

    private function getCashReport($from, $to, $company){
        // Get first the departments to use in condition
        $sale_id = Department::where('name', 'Sales')->select('id')->first()->id;

        $invoices = Invoice::with([
            'invoiceOthers.accountTitle',
            'invoiceOthers.invoiceSubs.accountSub',
        ])
        ->whereBetween('created_at', [$from, $to])
        ->where('company_id', $company)
        ->where('department_id',  $sale_id)
        ->paginate(100); // paginate for performance

        return view('reports.cash', compact('invoices','from', 'to', 'company'))->render();
    }

    public function exportCashReport(Request $request)
    {
        $company_name = Company::select('name')->find($request->company)->name;

        // Get first the departments to use in condition
        $sale_id = Department::where('name', 'Sales')->select('id')->first()->id;

        return Excel::download(new CashReportExport($request->from, $request->to, $request->company, $company_name, $sale_id), 'cash_report.xlsx');
    }

    private function getLedgerReport($from, $to, $company){

        // Get all invoice within the company
        $invoice_ids = Invoice::where('company_id', $company)->select('id')->get()->pluck('id')->toArray();
       
        // Preload all account subs and map them by ID
        $accountSubsMap = AccountSub::select('id', 'name', 'account_title_id')->get()->keyBy('id');

        // 1. Get parent (no-child) totals
        $parentAccounts = InvoicesOtherExpenses::select('account_title_id', DB::raw('SUM(amount) as total_amount'))
            ->where('has_child', 0)
            ->whereIn('invoice_id', $invoice_ids)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('account_title_id')
            ->pluck('total_amount', 'account_title_id');

        // 2. Get child totals (with grouping and mapping)
        $childSubs = InvoiceSub::select(
                'invoice_subs.account_sub_id',
                DB::raw('SUM(invoice_subs.amount) as total_amount')
            )
            ->join('invoices_other_expenses', 'invoice_subs.invoice_other_expenses_id', '=', 'invoices_other_expenses.id')
            ->whereIn('invoices_other_expenses.invoice_id', $invoice_ids)
            ->whereBetween('invoices_other_expenses.created_at', [$from, $to])
            ->groupBy('invoice_subs.account_sub_id')
            ->get()
            ->map(function ($sub) use ($accountSubsMap) {
                $accountSub = $accountSubsMap[$sub->account_sub_id];
                $sub->account_title_id = $accountSub->account_title_id;
                $sub->account_sub = $accountSub;
                return $sub;
            })
            ->groupBy('account_title_id');

        // 3. Get all used account_title_ids
        $allTitleIds = collect($parentAccounts->keys())->merge($childSubs->keys())->unique();

        // 4. Load only needed account titles
        $accountTitles = AccountTitle::with('accountSubs')
            ->whereIn('id', $allTitleIds)
            ->orderBy('code')
            ->get();

        return view('reports.ledger', compact('accountTitles', 'parentAccounts', 'childSubs', 'from', 'to', 'company'))->render();
    }

    public function export(Request $request)
    {
        // Get all invoice within the company
        $invoice_ids = Invoice::where('company_id', $request->input('company'))->select('id')->get()->pluck('id')->toArray();

        // You can reuse the same logic from your main report method:
        $from = $request->input('from') ?? now()->startOfMonth();
        $to = $request->input('to') ?? now()->endOfMonth();

       // Preload all account subs and map them by ID
       $accountSubsMap = AccountSub::select('id', 'name', 'account_title_id')->get()->keyBy('id');

       // 1. Get parent (no-child) totals
       $parentAccounts = InvoicesOtherExpenses::select('account_title_id', DB::raw('SUM(amount) as total_amount'))
           ->where('has_child', 0)
           ->whereBetween('created_at', [$from, $to])
           ->whereIn('invoice_id', $invoice_ids)
           ->groupBy('account_title_id')
           ->pluck('total_amount', 'account_title_id');

       // 2. Get child totals (with grouping and mapping)
       $childSubs = InvoiceSub::select(
               'invoice_subs.account_sub_id',
               DB::raw('SUM(invoice_subs.amount) as total_amount')
           )
           ->join('invoices_other_expenses', 'invoice_subs.invoice_other_expenses_id', '=', 'invoices_other_expenses.id')
           ->whereBetween('invoices_other_expenses.created_at', [$from, $to])
           ->whereIn('invoices_other_expenses.invoice_id', $invoice_ids)
           ->groupBy('invoice_subs.account_sub_id')
           ->get()
           ->map(function ($sub) use ($accountSubsMap) {
               $accountSub = $accountSubsMap[$sub->account_sub_id];
               $sub->account_title_id = $accountSub->account_title_id;
               $sub->account_sub = $accountSub;
               return $sub;
           })
           ->groupBy('account_title_id');

       // 3. Get all used account_title_ids
       $allTitleIds = collect($parentAccounts->keys())->merge($childSubs->keys())->unique();

       // 4. Load only needed account titles
       $accountTitles = AccountTitle::with('accountSubs')
           ->whereIn('id', $allTitleIds)
           ->orderBy('code')
           ->get();

        return Excel::download(new LedgerReportExport($accountTitles, $parentAccounts, $childSubs), 'ledger-report.xlsx');
    }
}
