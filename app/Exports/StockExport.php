<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockExport implements FromQuery, WithHeadings, WithMapping, WithTitle, WithStyles
{
    public function query()
    {
        return Product::query()->with('category');
    }

    public function headings(): array
    {
        return [
            ['STOCK REPORT'],
            ['Generated at: ' . now()->format('d-m-Y H:i:s')],
            [''],
            [
                'Product Name',
                'Category',
                'Stock',
                'Purchase Price',
                'Selling Price',
                'Description',
            ],
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->category->name,
            $product->stock,
            $product->purchase_price,
            $product->selling_price,
            $product->description,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true]],
            'A' => ['width' => 30],
            'B' => ['width' => 20],
            'C' => ['width' => 15],
            'D' => ['width' => 20],
            'E' => ['width' => 20],
            'F' => ['width' => 40],
        ];
    }

    public function title(): string
    {
        return 'Stock Report';
    }
}