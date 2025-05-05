<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashReportExport implements FromCollection, WithHeadings, WithChunkReading, WithEvents, WithStyles
{
    protected $start, $end, $company;
    protected $data;
    protected $companyName = '';
    protected $source = 'From Sales Department';
    protected $saleId;

    public function __construct($start, $end, $company, $companyName, $saleId)
    {
        $this->start = $start;
        $this->end = $end;
        $this->company = $company;
        $this->companyName = $companyName;
        $this->saleId = $saleId;
    }

    public function collection()
    {
        $this->data = Invoice::with(['invoiceOthers.accountTitle', 'invoiceOthers.invoiceSubs.accountSub'])
            ->whereBetween('created_at', [$this->start, $this->end])
            ->where('company_id', $this->company)
            ->where('department_id', $this->saleId)
            ->get()
            ->flatMap(function ($invoice) {
                return collect($invoice->invoiceOthers)->flatMap(function ($expense) use ($invoice) {
                    if ($expense->has_child) {
                        return collect($expense->invoiceSubs)->map(function ($sub) use ($invoice, $expense) {
                            return [
                                $invoice->created_at->format('Y-m-d'),
                                $invoice->voucher_no,
                                $invoice->code,
                                $expense->accountTitle->code.'-'.$sub->accountSub->code,
                                $sub->accountSub->name,
                                $sub->particulars,
                                $sub->debit,
                                $sub->credit,
                            ];
                        });
                    } else {
                        return [[
                            $invoice->created_at->format('Y-m-d'),
                            $invoice->voucher_no,
                            $invoice->code,
                            $expense->accountTitle->code,
                            $expense->accountTitle->title,
                            $expense->particulars,
                            $expense->debit,
                            $expense->credit,
                        ]];
                    }
                });
            });

        // Add Grand Total row
        $totalDebit = $this->data->sum(6); // debit column index
        $totalCredit = $this->data->sum(7); // credit column index

        $this->data->push([
            '', '', '', '', '', 'GRAND TOTAL:',
            number_format($totalDebit, 2),
            number_format($totalCredit, 2),
        ]);

        return $this->data;
    }

    public function headings(): array
    {
        return ['DATE', 'REF. NO.', 'SEQUENCE NO.', 'ACCOUNT CODE', 'ACCOUNT TITLE', 'PARTICULARS', 'DEBIT', 'CREDIT'];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert custom rows at the top
                $sheet->insertNewRowBefore(1, 3);

                $sheet->setCellValue('A1', $this->companyName);
                $sheet->setCellValue('A2', 'Cash Report');

                $dateRange = strtoupper(date('F j', strtotime($this->start)) . ' TO ' . date('F j', strtotime($this->end)));
                $sheet->setCellValue('A3', "Date: {$dateRange}, Source: {$this->source}");

                // Merge and center the first 3 rows across all columns (A to H)
                foreach ([1, 2, 3] as $row) {
                    $sheet->mergeCells("A{$row}:H{$row}");
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                }

                // Bold the headings row
                $sheet->getStyle('A4:H4')->getFont()->setBold(true);

                // Optionally, auto-size columns
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
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
