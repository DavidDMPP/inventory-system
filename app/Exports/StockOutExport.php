<?php

namespace App\Exports;

use App\Models\StockOut;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class StockOutExport implements FromQuery, WithHeadings, WithMapping, WithTitle, WithStyles
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
        return StockOut::query()
            ->with('product')
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->orderBy('date', 'desc');
    }

    public function headings(): array
    {
        return [
            ['STOCK OUT REPORT'],
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
                'Selling Price',
                'Total Amount',
                'Profit',
                'Notes'
            ],
        ];
    }

    public function map($stockOut): array
    {
        $totalAmount = $stockOut->quantity * $stockOut->selling_price;
        $costAmount = $stockOut->quantity * $stockOut->product->purchase_price;
        $profit = $totalAmount - $costAmount;

        return [
            $stockOut->date->format('d-m-Y'),
            $stockOut->invoice_number,
            $stockOut->product->name,
            $stockOut->quantity,
            $stockOut->selling_price,
            $totalAmount,
            $profit,
            $stockOut->notes
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
            'G' => ['width' => 20],
            'H' => ['width' => 30],
        ];
    }

    public function title(): string
    {
        return 'Stock Out Report';
    }
}