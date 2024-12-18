<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
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
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class Dashboard extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = "heroicon-o-document-text";
    protected static string $view = "filament.user.pages.dashboard";
    
    public $startDate;
    public $endDate;
    public $selectedReport = "stock";

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth();
        $this->endDate = now();
        $this->form->fill([
            "selectedReport" => $this->selectedReport,
            "startDate" => $this->startDate,
            "endDate" => $this->endDate,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make("selectedReport")
                ->label("Report Type")
                ->options([
                    "stock" => "Stock Report",
                    "stock-in" => "Stock In Report",
                    "stock-out" => "Stock Out Report",
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->selectedReport = $state;
                    $this->resetTable();
                }),
            DatePicker::make("startDate")
                ->label("Start Date")
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->startDate = $state;
                    $this->resetTable();
                }),
            DatePicker::make("endDate")
                ->label("End Date")
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->endDate = $state;
                    $this->resetTable();
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters());
    }

    protected function getTableQuery(): Builder
    {
        return match($this->selectedReport) {
            "stock" => Product::query()->with("category"),
            "stock-in" => StockIn::query()
                ->with("product")
                ->when($this->startDate, fn($q) => $q->whereDate("date", ">=", $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate("date", "<=", $this->endDate)),
            "stock-out" => StockOut::query()
                ->with("product")
                ->when($this->startDate, fn($q) => $q->whereDate("date", ">=", $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate("date", "<=", $this->endDate)),
            default => Product::query(),
        };
    }

    protected function getTableColumns(): array
    {
        return match($this->selectedReport) {
            "stock" => [
                TextColumn::make("name")->sortable()->searchable(),
                TextColumn::make("category.name")->sortable()->searchable(),
                TextColumn::make("stock")->sortable(),
                TextColumn::make("purchase_price")->money("idr")->sortable(),
                TextColumn::make("selling_price")->money("idr")->sortable(),
            ],
            "stock-in" => [
                TextColumn::make("date")->date()->sortable(),
                TextColumn::make("invoice_number")->searchable(),
                TextColumn::make("product.name")->searchable(),
                TextColumn::make("quantity")->sortable(),
                TextColumn::make("purchase_price")->money("idr")->sortable(),
            ],
            "stock-out" => [
                TextColumn::make("date")->date()->sortable(),
                TextColumn::make("invoice_number")->searchable(),
                TextColumn::make("product.name")->searchable(),
                TextColumn::make("quantity")->sortable(),
                TextColumn::make("selling_price")->money("idr")->sortable(),
            ],
            default => [],
        };
    }

    protected function getTableFilters(): array
    {
        return match($this->selectedReport) {
            "stock" => [
                SelectFilter::make("category")
                    ->relationship("category", "name"),
                Filter::make("low_stock")
                    ->query(fn (Builder $query) => $query->where("stock", "<", 10)),
            ],
            "stock-in", "stock-out" => [
                SelectFilter::make("product")
                    ->relationship("product", "name"),
            ],
            default => [],
        };
    }
}
