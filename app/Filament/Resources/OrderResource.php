<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\BikeName;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
                                        Select::make('customer_id')
                                            ->searchable()
                                            ->relationship('customer', 'name')
                                            ->preload()
                                            ->createOptionModalHeading('Create a new customer')
                                            ->createOptionForm([
                                                Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->required()
                                                            ->columnSpan(1)
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('email')
                                                            ->email()
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('phone_number')
                                                            ->tel()
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('state')
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('city')
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\Textarea::make('address')
                                                            ->columnSpanFull(),
                                                    ])
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                Notification::make()
                                                    ->title('Customer created successfully')
                                                    ->success()
                                                    ->send();
                                                return Customer::create($data)->value('id');
                                            })
                                            ->reactive() // Enables reactivity
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    // Fetch customer data using the selected customer ID
                                                    $customer = Customer::find($state);

                                                    // Populate the fields with the fetched customer data
                                                    if ($customer) {
                                                        $set('email', $customer->email);
                                                        $set('phone_number', $customer->phone_number);
                                                        $set('address', $customer->address);
                                                        $set('state', $customer->address);
                                                        $set('city', $customer->address);
                                                    }
                                                }
                                            }),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->reactive()
                                            ->disabled()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('phone_number')
                                            ->tel()
                                            ->disabled()
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('state')
                                            ->disabled()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('city')
                                            ->disabled()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('address')
                                            ->required()
                                            ->disabled()
                                            ->columnSpanFull(),
                                    ])->columns(3),
                                Section::make('Order Description')
                                    ->schema([
                                        RichEditor::make('description')
                                    ]),
                            ])
                            ->columnSpan(1),
                        Group::make()
                            ->schema([
                                Section::make('Product Details')
                                    ->schema([
                                        Forms\Components\Repeater::make('products')
                                        ->relationship('products')
                                            ->schema([
                                                Forms\Components\Select::make('product_id')
                                                    ->options(function () {
                                                        $products = Product::select(['id', 'name', 'part_number'])->get();

                                                        return $products->mapWithKeys(function ($product) {
                                                            return [$product->id => $product->part_number . ' - ' . $product->name];
                                                        });
                                                    }) // Ensure 'part_number' is available
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
                                                        $totalPrice = collect($get('../../products'))
                                                            ->sum(fn($product) => $product['amount'] ?? 0);
                                                        // Update the total price field outside the repeater
                                                        $set('../../total_amount', $totalPrice);
                                                    }),
                                                Forms\Components\TextInput::make('amount')
                                                    ->required()
                                                    ->prefix('INR')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->reactive()
                                                    ->numeric()

                                            ])
                                            ->columns(4)
                                            ->label('Order Items')
                                            ->required(),
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('total_amount')
                                                    ->label('Total Price')
                                                    ->prefix('INR')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->numeric()
                                                    ->columnSpan(1)
                                                    ->reactive(),
                                                Forms\Components\Select::make('status')
                                                    ->searchable()
                                                    ->options([
                                                        'success' => 'Success',
                                                        'pending' => 'Pending',
                                                        'canceled' => 'Canceled',
                                                    ])
                                                    ->columnSpan(1)
                                                    ->required(),
                                            ])
                                    ]),

                            ])
                            ->columnSpanFull()
                    ])
                    ->columns(1)
            ])->columns(0);
    }

    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->formatStateUsing(function ($state) {
                        return '#' . $state;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.phone_number')
                    ->searchable(),
             
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->money('inr')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
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
                // Tables\Actions\EditAction::make(),
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
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
