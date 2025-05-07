<?php

namespace App\Exports;

use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JournalReportExport implements FromQuery, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    use Exportable;

    protected $start, $end, $company;
    protected $data;
    protected $companyName = '';
    protected $source = 'From  Department';
    protected $departments;
    protected float $totalDebit = 0;
    protected float $totalCredit = 0;

    public function __construct($start, $end, $company, $companyName, $departments)
    {
        $this->start = $start;
        $this->end = $end;
        $this->company = $company;
        $this->companyName = $companyName;
        $this->departments = $departments;
    }

    public function query()
    {
        return DB::table('invoices_other_expenses as ioe')
            ->join('invoices as inv', 'inv.id', '=', 'ioe.invoice_id')
            ->join('account_titles as at', 'at.id', '=', 'ioe.account_title_id')
            ->leftJoin('invoice_subs as sub', 'sub.invoice_other_expenses_id', '=', 'ioe.id')
            ->leftJoin('account_sub as a_sub', 'a_sub.id', '=', 'sub.account_sub_id')
            ->selectRaw('
                DATE_FORMAT(inv.created_at, "%b %e") as date,
                IF(ioe.has_child = 1, CONCAT(at.code, "-", a_sub.code), at.code) as account_code,
                IF(ioe.has_child = 1, a_sub.name, at.title) as account_title,
                inv.voucher_no as ref_no,
                "manual" as jv_no,
                IF(ioe.has_child = 1, sub.debit, ioe.debit) as debit,
                IF(ioe.has_child = 1, sub.credit, ioe.credit) as credit
            ')
            ->whereBetween('inv.created_at', [$this->start, $this->end])
            ->where('inv.company_id', $this->company)
            ->whereIn('inv.department_id', $this->departments)
            ->orderBy('inv.created_at');
    }

    public function headings(): array
    {
        return ['Date', 'Account Code', 'Account Title', 'Ref No', 'JV No', 'Debit', 'Credit'];
    }

    public function map($row): array
    {
        $this->totalDebit += $row->debit;
        $this->totalCredit += $row->credit;

        return [
            Carbon::parse($row->date)->format('M j'),
            $row->account_code,
            $row->account_title,
            $row->ref_no,
            '',
            number_format($row->debit, 2),
            number_format($row->credit, 2),
        ];
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
                $sheet->setCellValue('A2', 'General Journal Report');

                $dateRange = strtoupper(date('F j', strtotime($this->start)) . ' TO ' . date('F j', strtotime($this->end)));
                $sheet->setCellValue('A3', "Date: {$dateRange}, Source: {$this->source}");

                // Merge and center the first 3 rows across all columns (A to H)
                foreach ([1, 2, 3] as $row) {
                    $sheet->mergeCells("A{$row}:G{$row}");
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                }

                // Bold the headings row
                $sheet->getStyle('A4:G4')->getFont()->setBold(true);

                // Optionally, auto-size columns
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $lastRow = $sheet->getHighestRow() + 1;

                // Add Grand Total label and values
                $sheet->setCellValue("E{$lastRow}", 'GRAND TOTAL:');
                $sheet->setCellValue("F{$lastRow}", $this->totalDebit);
                $sheet->setCellValue("G{$lastRow}", $this->totalCredit);

                // Bold the total row
                $sheet->getStyle("F{$lastRow}:H{$lastRow}")->getFont()->setBold(true);
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
