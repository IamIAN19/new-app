<?php

namespace App\Exports;


use App\Models\Invoice;
use App\Models\AccountTitle;
use App\Models\InvoicesOtherExpenses;
use App\Models\InvoiceSub;
use App\Models\AccountSub;
use App\Models\Department;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerReportExport implements FromCollection, WithHeadings, WithMapping, WithChunkReading, WithEvents, WithStyles
{
    protected $company, $from, $to;
    protected $data;
    protected $companyName = '';
    protected $source = '';
    protected $departments;
    protected $grandTotalDebit = 0;
    protected $grandTotalCredit = 0;

    public function __construct($from, $to, $company, $companyName, $departments)
    {
        $this->from = $from;
        $this->to = $to;
        $this->company = $company;
        $this->companyName = $companyName;
        $this->departments = $departments;

        $this->prepareData();
    }

    public function prepareData()
    {
        $invoice_ids = Invoice::where('company_id', $this->company)
            ->whereBetween('added_date', [$this->from, $this->to])
            ->whereIn('department_id', $this->departments)
            ->pluck('id')
            ->toArray();

        $accountSubsMap = AccountSub::select('id', 'name', 'account_title_id')->get()->keyBy('id');

        // Parents
        $parentAccounts = InvoicesOtherExpenses::select(
                'account_title_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->where('has_child', 0)
            ->whereIn('invoice_id', $invoice_ids)
            ->groupBy('account_title_id')
            ->get()
            ->keyBy('account_title_id');

        // Children
        $childSubs = InvoiceSub::select(
                'invoice_subs.account_sub_id',
                DB::raw('SUM(invoice_subs.debit) as total_debit'),
                DB::raw('SUM(invoice_subs.credit) as total_credit')
            )
            ->join('invoices_other_expenses', 'invoice_subs.invoice_other_expenses_id', '=', 'invoices_other_expenses.id')
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

        // Merge
        $allTitleIds = collect($parentAccounts->keys())->merge($childSubs->keys())->unique();

        $accountTitles = AccountTitle::with('accountSubs')
            ->whereIn('id', $allTitleIds)
            ->orderBy('code')
            ->get();

        // Final rows
        $this->data = collect();

        foreach ($accountTitles as $title) {
            $parent = $parentAccounts[$title->id] ?? null;
            $childAccounts = $childSubs[$title->id] ?? collect();
        
            // Always display parent row (even if totals are zero)
            $parentDebit = $parent->total_debit ?? 0;
            $parentCredit = $parent->total_credit ?? 0;
        
            $this->data->push([
                $title->code,
                strtoupper($title->title),
                $parentDebit,
                $parentCredit,
            ]);
        
            $this->grandTotalDebit += $parentDebit;
            $this->grandTotalCredit += $parentCredit;
        
            // Now list child subs under it
            foreach ($childAccounts as $sub) {
                $this->data->push([
                    '', // Empty account code
                    '  └ ' . $sub->account_sub->name,
                    $sub->total_debit,
                    $sub->total_credit,
                ]);
        
                $this->grandTotalDebit += $sub->total_debit;
                $this->grandTotalCredit += $sub->total_credit;
            }
        }
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Account Code', 'Account Title', 'Debit', 'Credit'];
    }

    public function map($row): array
    {
        return $row;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {   
        $sourceData = Department::whereIn('id', $this->departments)->select('name')->get()->pluck('name')->toArray();
    
        $this->source = implode(',',$sourceData);

        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert custom rows at the top
                $sheet->insertNewRowBefore(1, 3);

                $sheet->setCellValue('A1', $this->companyName);
                $sheet->setCellValue('A2', 'SLSP Report');

                $dateRange = strtoupper(date('F j', strtotime($this->from)) . ' TO ' . date('F j', strtotime($this->to)));
                $sheet->setCellValue('A3', "Date: {$dateRange}, Source: {$this->source}");

                // Merge and center the first 3 rows across all columns (A to H)
                foreach ([1, 2, 3] as $row) {
                    $sheet->mergeCells("A{$row}:D{$row}");
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                }

                // Bold the headings row
                $sheet->getStyle('A4:D4')->getFont()->setBold(true);

                // Optionally, auto-size columns
                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $dataCount = $this->data->count();
                $lastRow = 4 + $dataCount;

                // Write grand total
                $sheet->setCellValue("A{$lastRow}", '');
                $sheet->setCellValue("B{$lastRow}", 'Grand Total');
                $sheet->setCellValue("C{$lastRow}", $this->grandTotalDebit);
                $sheet->setCellValue("D{$lastRow}", $this->grandTotalCredit);

                // Bold and style grand total row
                $sheet->getStyle("A{$lastRow}:D{$lastRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$lastRow}:D{$lastRow}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEFEFEF');
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            4 => ['font' => ['bold' => true]], // Header row
        ];
    }
}