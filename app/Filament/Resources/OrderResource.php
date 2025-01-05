<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\BikeName;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-check';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Customer Details')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('address')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])->columns(3),
                                Section::make('Order Description')
                                    ->schema([
                                        RichEditor::make('description')
                                    ]),
                            ])
                            ->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make('Product Details')
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->relationship('product', 'name', fn($query) => $query->select(['id', 'name', 'part_number'])) // Ensure 'part_number' is available
                                            ->searchable(['name', 'part_number'])
                                            ->columnSpan(1)
                                            ->preload()
                                            ->required()
                                            ->getOptionLabelFromRecordUsing(fn($record) => "({$record->part_number}) - {$record->name}")
                                            ->reactive()  // Make the field reactive
                                            ->afterStateUpdated(function ($state, $set) {
                                                // If the selected product ID is valid, get the mrp
                                                if ($state) {
                                                    $product = \App\Models\Product::find($state); // Load the product by ID
                                                    $set('mrp', $product ? $product->mrp : 0); // Set the MRP or 0 if the product is not found
                                                } else {
                                                    $set('mrp', 0); // Set MRP to 0 if no product is selected
                                                }
                                            }),

                                        Forms\Components\TextInput::make('mrp')
                                            ->required()
                                            ->prefix('INR')
                                            ->disabled()
                                            ->dehydrated()
                                            ->reactive()
                                            ->numeric(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->required()
                                            ->prefix('QTY')
                                            ->numeric()
                                            ->reactive() // Make the field reactive
                                            ->afterStateUpdated(function ($state, $get, $set) {
                                                $mrp = $get('mrp'); // Get the MRP
                                                $quantity = $state; // Get the entered quantity
                                                $amount = $mrp * $quantity; // Calculate amount
                                                $set('amount', $amount); // Set the calculated amount
                                            }),
                                        Forms\Components\TextInput::make('discount')
                                            ->prefix('INR')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, $get, $set) {
                                                $amount = $get('amount'); // Get the current amount
                                                $discount = $state; // Get the entered discount value
                                                $newAmount = $amount - $discount; // Subtract discount from the amount
                                                $set('amount', $newAmount > 0 ? $newAmount : 0); // Ensure the amount doesn't go negative
                                            }),
                                        Forms\Components\TextInput::make('amount')
                                            ->required()
                                            ->prefix('INR')
                                            ->disabled()
                                            ->dehydrated()
                                            ->reactive()
                                            ->numeric(),
                                        Forms\Components\Select::make('status')
                                            ->searchable()
                                            ->options([
                                                'success' => 'Success',
                                                'pending' => 'Pending',
                                                'canceled' => 'Canceled',
                                            ])
                                            ->required(),
                                    ]),
                               
                            ])
                            ->columnSpan(1)
                    ])
                    ->columns(3)
            ])->columns(0);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->formatStateUsing(function($state) {
                        return '#' . $state;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->numeric()
                    ->sortable(),
                
                    Tables\Columns\BadgeColumn::make('quantity')
                    ->label('Stocks')
                    ->colors([
                        'success' => fn ($state) => $state <= 5,     // Red badge for stock <= 5
                        'success' => fn ($state) => $state > 5 && $state <= 10, // Yellow badge for stock > 5 and <= 10
                        'success' => fn ($state) => $state > 10,   // Green badge for stock > 10
                    ])
                    ->formatStateUsing(function ($state) {
                        return $state . ' units'; // Append 'units' for better readability
                    }),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->money('inr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->money('inr')
                    ->sortable(),
                    Tables\Columns\BadgeColumn::make('status')
                    ->label('Operation')
                    ->colors([
                        'success' => 'success', 
                        'danger' => 'canceled', 
                        'warning' => 'pending', 
                    ])
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state); // Capitalize the state for display
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
