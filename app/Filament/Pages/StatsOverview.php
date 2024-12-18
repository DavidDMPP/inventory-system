<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('Total number of products')
                ->descriptionIcon('heroicon-m-box')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            
            Stat::make('Low Stock Products', Product::where('stock', '<', 10)->count())
                ->description('Products with stock < 10')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            
            Stat::make('Today\'s Stock In', StockIn::whereDate('date', today())->count())
                ->description('Stock in transactions today')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'),
            
            Stat::make('Today\'s Stock Out', StockOut::whereDate('date', today())->count())
                ->description('Stock out transactions today')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('warning'),
        ];
    }
}