<?php

namespace App\Services;

use App\Models\AccountSub;
use App\Models\AccountTitle;
use App\Models\Company;
use App\Models\Department;
use App\Models\Invoice;
use App\Models\InvoicesOtherExpenses;
use App\Models\InvoiceSub;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class ReportService
{
    public function getSlspReport($from, $to, $company, $departments){
        $start = $from;
        $end = $to;
        
        $invoices = Invoice::with(['supplierData', 'category'])
            ->whereBetween('created_at', [$start, $end])
            ->where('company_id', $company)
            ->whereIn('department_id', $departments)
            ->get();
    
        // Group by supplier TIN and category ID
        $grouped = $invoices->groupBy(function ($invoice) {
            return $invoice->tin . '|' . $invoice->sales_category_id;
        });
    
        // Convert grouped results into a paginated collection
        $groupedArray = $grouped->map(function ($group) {
            $invoice = $group->first();
    
            $net = $group->sum('vat_tax_amount');
            $zero = $group->sum('vat_zero_rated');
            $exempt = $group->sum('vat_exempt');
            $vat = $net * 0.12;
            $total = $net + $zero + $exempt + $vat;
    
            return [
                'tin' => $invoice->supplierData->tin,
                'classification' => $invoice->supplierData->classification,
                'payee' => $invoice->supplierData->name,
                'address' => $invoice->supplierData->address,
                'category' => $invoice->category->name,
                'net' => $net,
                'zero' => $zero,
                'exempt' => $exempt,
                'vat' => $vat,
                'total' => $total,
            ];
        })->values();
    
        // Paginate grouped results
        $perPage = 50;
        $page = request()->get('page', 1);
        $pagedGrouped = new LengthAwarePaginator(
            $groupedArray->forPage($page, $perPage),
            $groupedArray->count(),
            $perPage,
            $page
        );

        return view('reports.slsp', compact('pagedGrouped', 'from', 'to', 'company', 'departments'))->render();
    }

    public function getDepartmentalReports($from, $to, $company, $departments, $report_type){

        $invoices = Invoice::with([
            'invoiceOthers.accountTitle',
            'invoiceOthers.invoiceSubs.accountSub',
        ])
        ->whereBetween('created_at', [$from, $to])
        ->where('company_id', $company)
        ->whereIn('department_id', $departments)
        ->paginate(100); // paginate for performance

        return view('reports.departamental', compact('invoices','from', 'to', 'company', 'departments', 'report_type'))->render();
    }

    public function getGeneralJournalReports($from, $to, $company, $departments){

        // $invoices = DB::table('invoices_other_expenses as io')
        //     ->join('invoices as i', 'i.id', '=', 'io.invoice_id')
        //     ->join('account_titles as at', 'at.id', '=', 'io.account_title_id')
        //     ->whereBetween('i.created_at', [$from, $to])
        //     ->where('i.company_id', $company)
        //     ->whereIn('i.department_id', $departments)
        //     ->select(
        //         'i.created_at',
        //         'i.voucher_no',
        //         'at.title as account_title',
        //         'at.code as account_title_code',
        //         'io.debit',
        //         'io.credit'
        //     )
        //     ->orderBy('i.created_at')
        //     ->paginate(100); // adjust page size for performance

        $parentRows = DB::table('invoices_other_expenses as ioe')
            ->join('account_titles as at', 'ioe.account_title_id', '=', 'at.id')
            ->join('invoices as inv', 'ioe.invoice_id', '=', 'inv.id')
            ->where('ioe.has_child', 0)
            ->whereBetween('inv.created_at', [$from, $to])
            ->where('inv.company_id', $company)
            ->whereIn('inv.department_id', $departments)
            ->select([
                DB::raw('DATE_FORMAT(inv.created_at, "%b %e") as date'),
                'at.code as account_code',
                'at.title as account_title',
                'inv.voucher_no as ref_no',
                DB::raw('"manual" as jv_no'),
                'ioe.debit',
                'ioe.credit',
            ]);

        $childRows = DB::table('invoice_subs as isub')
            ->join('account_sub as sub', 'isub.account_sub_id', '=', 'sub.id')
            ->join('invoices_other_expenses as ioe', 'isub.invoice_other_expenses_id', '=', 'ioe.id')
            ->join('account_titles as at', 'ioe.account_title_id', '=', 'at.id')
            ->join('invoices as inv', 'ioe.invoice_id', '=', 'inv.id')
            ->where('ioe.has_child', 1)
            ->whereBetween('inv.created_at', [$from, $to])
            ->where('inv.company_id', $company)
            ->whereIn('inv.department_id', $departments)
            ->select([
                DB::raw('DATE_FORMAT(inv.created_at, "%b %e") as date'),
                DB::raw('CONCAT(at.code, "-", sub.code) as account_code'), // 👈 here
                'sub.name as account_title',
                'inv.voucher_no as ref_no',
                DB::raw('"manual" as jv_no'),
                'isub.debit',
                'isub.credit',
            ]);

        $unionQuery = $parentRows->unionAll($childRows);

        $invoices = DB::query()
            ->fromSub($unionQuery, 'records')
            ->orderBy('date')
            ->get();
        
        return view('reports.general_journal', compact('invoices','from', 'to', 'company', 'departments'))->render();
    }

    public function getLedgerReport($from, $to, $company, $departments){

        $invoice_ids = Invoice::where('company_id', $company)
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('department_id', $departments)
            ->pluck('id')
            ->toArray();

        // Preload all account subs and map them by ID
        $accountSubsMap = AccountSub::select('id', 'name', 'account_title_id')->get()->keyBy('id');

        // Parent-level totals (no children)
        $parentAccounts = InvoicesOtherExpenses::select(
                'account_title_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->where('has_child', 0)
            ->whereIn('invoice_id', $invoice_ids)
            // ->whereBetween('created_at', [$from, $to])
            ->groupBy('account_title_id')
            ->get()
            ->keyBy('account_title_id');

        // Child (subs) level totals
        $childSubs = InvoiceSub::select(
                'invoice_subs.account_sub_id',
                DB::raw('SUM(invoice_subs.debit) as total_debit'),
                DB::raw('SUM(invoice_subs.credit) as total_credit')
            )
            ->join('invoices_other_expenses', 'invoice_subs.invoice_other_expenses_id', '=', 'invoices_other_expenses.id')
            ->whereIn('invoices_other_expenses.invoice_id', $invoice_ids)
            // ->whereBetween('invoices_other_expenses.created_at', [$from, $to])
            ->groupBy('invoice_subs.account_sub_id')
            ->get()
            ->map(function ($sub) use ($accountSubsMap) {
                $accountSub = $accountSubsMap[$sub->account_sub_id];
                $sub->account_title_id = $accountSub->account_title_id;
                $sub->account_sub = $accountSub;
                return $sub;
            })
            ->groupBy('account_title_id');

        $allTitleIds = collect($parentAccounts->keys())->merge($childSubs->keys())->unique();

        $accountTitles = AccountTitle::with('accountSubs')
            ->whereIn('id', $allTitleIds)
            ->orderBy('code')
            ->get();

        return view('reports.ledger', compact('accountTitles', 'parentAccounts', 'childSubs', 'from', 'to', 'company', 'departments'))->render();
    }

    // private function getDisbursementReport($from, $to, $company){

    //     $invoices = Invoice::with([
    //         'invoiceOthers.accountTitle',
    //         'invoiceOthers.invoiceSubs.accountSub',
    //     ])
    //     ->whereBetween('created_at', [$from, $to])
    //     ->where('company_id', $company)
    //     ->whereIn('department_id', [3,4])
    //     ->paginate(100); // paginate for performance

    //     return view('reports.disbursement', compact('invoices','from', 'to', 'company'))->render();
    // }

    // private function getCashReport($from, $to, $company){
    //     // Get first the departments to use in condition
    //     $sale_id = Department::where('name', 'Sales')->select('id')->first()->id;

    //     $invoices = Invoice::with([
    //         'invoiceOthers.accountTitle',
    //         'invoiceOthers.invoiceSubs.accountSub',
    //     ])
    //     ->whereBetween('created_at', [$from, $to])
    //     ->where('company_id', $company)
    //     ->where('department_id',  $sale_id)
    //     ->paginate(100); // paginate for performance

    //     return view('reports.cash', compact('invoices','from', 'to', 'company'))->render();
    // }

    // private function getPurchasesReport($from, $to, $company){

    //     $invoices = Invoice::with([
    //         'invoiceOthers.accountTitle',
    //         'invoiceOthers.invoiceSubs.accountSub',
    //     ])
    //     ->whereBetween('created_at', [$from, $to])
    //     ->where('company_id', $company)
    //     ->whereIn('department_id', [2])
    //     ->paginate(100); // paginate for performance

    //     return view('reports.purchases', compact('invoices','from', 'to', 'company'))->render();
    // }

    // public function exportDisbursementReport(Request $request)
    // {
    //     $company_name = Company::select('name')->find($request->company)->name;

    //     return Excel::download(new DisbursementReportExport($request->from, $request->to, $request->company, $company_name), 'disbursement_report.xlsx');
    // }

    // public function exportPurchasesReport( Request $request )
    // {
    //     $company_name = Company::select('name')->find($request->company)->name;

    //     return Excel::download(new PurchasesExportReport($request->from, $request->to, $request->company, $company_name), 'cash_report.xlsx');
    // }

    // public function exportCashReport(Request $request)
    // {
    //     $company_name = Company::select('name')->find($request->company)->name;

    //     return Excel::download(new CashReportExport($request->from, $request->to, $request->company, $company_name), 'cash_report.xlsx');
    // }
}
