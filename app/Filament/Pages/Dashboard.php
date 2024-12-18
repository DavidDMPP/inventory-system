<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | array
    {
        return 4;
    }

    public function getWidgets(): array
    {
        return [
            \Filament\Widgets\StatsOverviewWidget::class,
        ];
    }
}