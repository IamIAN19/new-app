<?php

namespace App\Http\Controllers;

use App\Exports\CashReportExport;
use App\Exports\DepartamentalReportExport;
use App\Exports\DisbursementReportExport;
use App\Exports\JournalReportExport;
use App\Exports\LedgerReportExport;
use App\Exports\PurchasesExportReport;
use App\Exports\SlspReportExport;
use App\Models\AccountSub;
use App\Models\AccountTitle;
use App\Models\Company;
use App\Models\Department;
use App\Models\Invoice;
use App\Models\InvoicesOtherExpenses;
use App\Models\InvoiceSub;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportsController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
    public function index(){
        $company = Company::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();

        return view('reports.index', compact('company', 'departments'));
    }

    public function fetchContent( Request $request)
    {
        if($request->ajax()){

            $request->validate([
                'company' => 'required',
                'type' => 'required',
                'customfilter' => 'required'
            ]);

            $date       = explode('-', request()->input('customfilter'));
            $from       = Carbon::parse(trim($date[0]))->format('Y-m-d');
            $to         = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $from;
            $company    = $request->company;
            $department = $request->department;

            switch( $request->type ){
                case 'ledger':
                    $html = $this->reportService->getLedgerReport($from, $to, $company, $department);
                    break;
                case 'slsp':
                    $html = $this->reportService->getSlspReport($from, $to, $company, $department);
                    break;
                case 'disbursement':
                    $html = $this->reportService->getDepartmentalReports($from, $to, $company, $department, $request->type);
                    break;
                case 'purchases':
                    $html = $this->reportService->getDepartmentalReports($from, $to, $company, $department, $request->type);
                    break;
                case 'cash':
                    $html = $this->reportService->getDepartmentalReports($from, $to, $company, $department, $request->type);
                    break;
                case 'journal':
                    $html = $this->reportService->getGeneralJournalReports($from, $to, $company, $department, $request->type);
                    break;
                default:
                    $html = view('reports.ledger')->render();
            }

            return response()->json(['html' => $html], 200);
        }

        return abort(403, 'Unauthorized!');
    }

    public function exportSlspReport(Request $request)
    {
        $company_name = Company::select('name')->find($request->company)->name;

        $departments = json_decode($request->departments);

        return Excel::download(new SlspReportExport($request->from, $request->to, $request->company, $company_name, $departments), 'slsp_report.xlsx');
    }

    public function exportDepartamentalReport(Request $request)
    {
        $company_name = Company::select('name')->find($request->company)->name;

        $departments = json_decode($request->departments);

        return Excel::download(new DepartamentalReportExport($request->from, $request->to, $request->company, $company_name, $departments), $request->report_type.'_report.xlsx');
    }

    public function exportGeneralJournalReport(Request $request)
    {
        $company_name = Company::select('name')->find($request->company)->name;

        $departments = json_decode($request->departments);

        return Excel::download(new JournalReportExport($request->from, $request->to, $request->company, $company_name, $departments), 'general_journal_report.xlsx');
    }

    public function export(Request $request)
    {
        $company_name = Company::select('name')->find($request->company)->name;

        $departments = json_decode($request->departments);

        return Excel::download(new LedgerReportExport($request->from, $request->to, $request->company, $company_name, $departments), 'general_ledger_report.xlsx');
    }
}
