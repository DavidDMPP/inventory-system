<?php

namespace App\Filament\Pages\Reports;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockExport;
use App\Exports\StockInExport;
use App\Exports\StockOutExport;
use Illuminate\Contracts\View\View;
use Filament\Tables;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Carbon\Carbon;

class InventoryReport extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.reports.inventory-report';

    public $startDate;
    public $endDate;
    public $selectedReport = 'stock';
    
    protected $listeners = [
        'refreshComponent' => '$refresh'
    ];

    public function updatedStartDate($value)
    {
        $this->dispatch('refreshComponent');
    }

    public function updatedEndDate($value)
    {
        $this->dispatch('refreshComponent');
    }

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->form->fill([
            'selectedReport' => $this->selectedReport,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return match($this->selectedReport) {
            'stock' => 'Stock Report',
            'stock-in' => 'Stock In Report',
            'stock-out' => 'Stock Out Report',
            default => 'Inventory Report',
        };
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('selectedReport')
                ->label('Report Type')
                ->options([
                    'stock' => 'Stock Report',
                    'stock-in' => 'Stock In Report',
                    'stock-out' => 'Stock Out Report',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->selectedReport = $state;
                    $this->dispatch('refreshComponent');
                }),
            DatePicker::make('startDate')
                ->label('Start Date')
                ->default(now()->startOfMonth())
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->startDate = $state;
                    $this->dispatch('refreshComponent');
                }),
            DatePicker::make('endDate')
                ->label('End Date')
                ->default(now())
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->endDate = $state;
                    $this->dispatch('refreshComponent');
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return match($this->selectedReport) {
            'stock' => $this->stockTable($table),
            'stock-in' => $this->stockInTable($table),
            'stock-out' => $this->stockOutTable($table),
            default => $this->stockTable($table),
        };
    }

    private function stockTable(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('stock')
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->money('idr')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Filter::make('low_stock')
                    ->query(fn (Builder $query) => $query->where('stock', '<', 10))
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Stock Report')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return Excel::download(
                            new StockExport(), 
                            'stock-report-'.date('Y-m-d').'.xlsx'
                        );
                    })
            ]);
    }

    private function stockInTable(Table $table): Table
    {
        return $table
            ->query(
                StockIn::query()
                    ->with('product')
                    ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            )
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('invoice_number')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('notes'),
            ])
            ->filters([
                SelectFilter::make('product')
                    ->relationship('product', 'name')
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Stock In Report')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return Excel::download(
                            new StockInExport($this->startDate, $this->endDate), 
                            'stock-in-report-'.date('Y-m-d').'.xlsx'
                        );
                    })
            ]);
    }

    private function stockOutTable(Table $table): Table
    {
        return $table
            ->query(
                StockOut::query()
                    ->with('product')
                    ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            )
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('invoice_number')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('notes'),
            ])
            ->filters([
                SelectFilter::make('product')
                    ->relationship('product', 'name')
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Stock Out Report')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return Excel::download(
                            new StockOutExport($this->startDate, $this->endDate), 
                            'stock-out-report-'.date('Y-m-d').'.xlsx'
                        );
                    })
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin();
    }
}