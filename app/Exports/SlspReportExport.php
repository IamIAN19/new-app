<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SlspReportExport implements FromCollection, WithHeadings, WithChunkReading, WithEvents, WithStyles
{
    protected $start, $end, $company;
    protected $data;
    protected $companyName = '';
    protected $source = '';
    protected $departments;

    public function __construct($start, $end, $company, $companyName, $departments)
    {
        $this->start = $start;
        $this->end = $end;
        $this->company = $company;
        $this->companyName = $companyName;
        $this->departments = $departments;
    
    }

    public function collection()
    {
        $invoices = Invoice::with(['supplierData', 'category'])
            ->whereBetween('added_date', [$this->start, $this->end])
            ->where('company_id', $this->company)
            ->whereIn('department_id', $this->departments)
            ->get();
        
        // Group by supplier TIN and category ID
        $grouped = $invoices->groupBy(function ($invoice) {
            return $invoice->tin . '|' . $invoice->sales_category_id;
        });
        
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
        
        // Save grouped data to $this->data
        $this->data = $groupedArray;
        
        // Calculate grand total
        $grandTotal = $this->data->sum('total');
        
        // Append a grand total row
        $this->data->push([
            'tin' => '',
            'classification' => '',
            'payee' => '',
            'address' => '',
            'category' => '',
            'net' => '',
            'zero' => '',
            'exempt' => '',
            'vat' => 'GRAND TOTAL:',
            'total' => number_format($grandTotal, 2),
        ]);
        
        return $this->data;
    }
    
    public function headings(): array
    {
        return ['TIN. NO.', 'CLASSIFICATION', 'PAYEE', 'ADDRESS', 'CATEGORY', 'NET OF VAT', 'ZERO RATED', 'EXEMPT', 'INPUT VAT(12%)', 'TOTAL INVOICE'];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {   
        $sourceData = Department::whereIn('id', $this->departments)->select('name')->get()->pluck('name')->toArray();
    
        $this->source = implode(', ',$sourceData);

        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert custom rows at the top
                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue('A1', $this->companyName);
                $sheet->setCellValue('A2', 'SLSP Report');

                $dateRange = strtoupper(date('F j', strtotime($this->start)) . ' TO ' . date('F j', strtotime($this->end)));
                $sheet->setCellValue('A3', "Date: {$dateRange}");
                $sheet->setCellValue('A4', "Source: {$this->source}");

                // Merge and center the first 3 rows across all columns (A to H)
                foreach ([1, 2, 3, 4] as $row) {
                    $sheet->mergeCells("A{$row}:J{$row}");
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                }

                // Bold the headings row
                $sheet->getStyle('A5:J5')->getFont()->setBold(true);

                // Optionally, auto-size columns
                foreach (range('A', 'J') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            5 => ['font' => ['bold' => true]], // Header row
        ];
    }
}
