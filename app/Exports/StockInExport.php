<?php

namespace App\Exports;

use App\Models\StockIn;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class StockInExport implements FromQuery, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : null;
        $this->endDate = $endDate ? Carbon::parse($endDate) : null;
    }

    public function query()
    {
        return StockIn::query()
            ->with('product')
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->orderBy('date', 'desc');
    }

    public function headings(): array
    {
        return [
            ['STOCK IN REPORT'],
            [
                'Period: ' . 
                ($this->startDate ? $this->startDate->format('d-m-Y') : 'All Time') . 
                ' to ' . 
                ($this->endDate ? $this->endDate->format('d-m-Y') : 'Present')
            ],
            ['Generated at: ' . now()->format('d-m-Y H:i:s')],
            [''],
            [
                'Date',
                'Invoice Number',
                'Product Name',
                'Quantity',
                'Purchase Price',
                'Total Amount',
                'Notes'
            ],
        ];
    }

    public function map($stockIn): array
    {
        return [
            $stockIn->date->format('d-m-Y'),
            $stockIn->invoice_number,
            $stockIn->product->name,
            $stockIn->quantity,
            $stockIn->purchase_price,
            $stockIn->quantity * $stockIn->purchase_price,
            $stockIn->notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            3 => ['font' => ['italic' => true]],
            5 => ['font' => ['bold' => true]],
            'A' => ['width' => 15],
            'B' => ['width' => 20],
            'C' => ['width' => 30],
            'D' => ['width' => 15],
            'E' => ['width' => 20],
            'F' => ['width' => 20],
            'G' => ['width' => 30],
        ];
    }

    public function title(): string
    {
        return 'Stock In Report';
    }
}