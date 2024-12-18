<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockOutResource\Pages;
use App\Models\StockOut;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockExport;
use App\Exports\StockInExport;
use App\Exports\StockOutExport;

class StockOutResource extends Resource
{
    protected static ?string $model = StockOut::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product) {
                                $set('selling_price', $product->selling_price);
                            }
                        }
                    }),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($get('selling_price')) {
                            $total = $state * $get('selling_price');
                            $set('total_amount', $total);
                        }
                        
                        // Check stock availability
                        if ($get('product_id')) {
                            $product = Product::find($get('product_id'));
                            if ($product && $state > $product->stock) {
                                Notification::make()
                                    ->title('Insufficient stock')
                                    ->body("Available stock: {$product->stock}")
                                    ->danger()
                                    ->send();
                            }
                        }
                    }),
                TextInput::make('selling_price')
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
                Tables\Columns\TextColumn::make('selling_price')
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
                SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('date')
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
                ])
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
            'index' => Pages\ListStockOuts::route('/'),
            'create' => Pages\CreateStockOut::route('/create'),
            'edit' => Pages\EditStockOut::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('date', today())->count();
    }

    protected function beforeCreate(): void
    {
        // Check if stock is sufficient
        $product = Product::find($this->data['product_id']);
        if ($product->stock < $this->data['quantity']) {
            Notification::make()
                ->title('Insufficient stock')
                ->body("Available stock: {$product->stock}")
                ->danger()
                ->send();
                
            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        // Update stock in product
        $stockOut = $this->record;
        $product = Product::find($stockOut->product_id);
        $product->stock -= $stockOut->quantity;
        $product->save();
    }

    protected function afterDelete(): void
    {
        // Update stock in product after delete
        $stockOut = $this->record;
        $product = Product::find($stockOut->product_id);
        $product->stock += $stockOut->quantity;
        $product->save();
    }
}