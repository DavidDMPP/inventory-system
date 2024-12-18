<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Support\Carbon;

class StockChart extends ChartWidget
{
    protected static ?string $heading = 'Stock Movement';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(function ($days) {
            return Carbon::now()->subDays($days)->format('Y-m-d');
        });

        $stockIn = $days->mapWithKeys(function ($day) {
            return [$day => StockIn::whereDate('date', $day)->sum('quantity')];
        })->values();

        $stockOut = $days->mapWithKeys(function ($day) {
            return [$day => StockOut::whereDate('date', $day)->sum('quantity')];
        })->values();

        return [
            'datasets' => [
                [
                    'label' => 'Stock In',
                    'data' => $stockIn,
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Stock Out',
                    'data' => $stockOut,
                    'borderColor' => '#EF4444',
                ],
            ],
            'labels' => $days->map(fn ($day) => Carbon::parse($day)->format('d M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}