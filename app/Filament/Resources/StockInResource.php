<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockInResource\Pages;
use App\Models\StockIn;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockExport;
use App\Exports\StockInExport;
use App\Exports\StockOutExport;

class StockInResource extends Resource
{
    protected static ?string $model = StockIn::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($get('purchase_price')) {
                            $total = $state * $get('purchase_price');
                            $set('total_amount', $total);
                        }
                    }),
                TextInput::make('purchase_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($get('quantity')) {
                            $total = $state * $get('quantity');
                            $set('total_amount', $total);
                        }
                    }),
                TextInput::make('total_amount')
                    ->disabled()
                    ->dehydrated(false)
                    ->prefix('Rp'),
                DatePicker::make('date')
                    ->required()
                    ->default(now()),
                TextInput::make('invoice_number')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('notes')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('date')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn($query) => $query->whereDate('date', '>=', $data['from'])
                            )
                            ->when(
                                $data['until'],
                                fn($query) => $query->whereDate('date', '<=', $data['until'])
                            );
                    })
            ])
            ->headerActions([]) // Kosongkan header actions
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockIns::route('/'),
            'create' => Pages\CreateStockIn::route('/create'),
            'edit' => Pages\EditStockIn::route('/{record}/edit'),
        ];
    }

    protected function afterCreate(): void
    {
        // Update stock in product
        $stockIn = $this->record;
        $product = Product::find($stockIn->product_id);
        $product->stock += $stockIn->quantity;
        $product->save();
    }

    protected function afterDelete(): void
    {
        // Update stock in product after delete
        $stockIn = $this->record;
        $product = Product::find($stockIn->product_id);
        $product->stock -= $stockIn->quantity;
        $product->save();
    }
}