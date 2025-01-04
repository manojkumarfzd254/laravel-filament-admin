<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\BikeName;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Image;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([ 
                        Group::make()
                            ->schema([
                                Section::make('Product Details')
                                    ->schema([
                                        Forms\Components\Grid::make(2) // Another 2 columns grid
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('part_number')
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\RichEditor::make('description')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Product Images')
                                    ->schema([
                                        Forms\Components\FileUpload::make('product_images')
                                            ->label('Product Images')
                                            ->multiple()
                                            ->image()
                                            ->directory('product-images') // Directory where images are stored
                                            ->maxFiles(10)
                                            ->maxSize(2048)
                                            ->required()
                                    ]),
                            ])
                            ->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make('Prices')
                                    ->schema([
                                        Forms\Components\TextInput::make('mrp')
                                            ->label('MRP')
                                            ->required()
                                            ->prefix('INR')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('buying_price')
                                            ->required()
                                            ->prefix('INR')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('selling_price')
                                            ->required()
                                            ->prefix('INR')
                                            ->numeric(),
                                    ]),
                                Section::make('Associations')
                                    ->schema([
                                            Forms\Components\Select::make('brand_id')
                                                ->relationship('brand', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                            Forms\Components\Select::make('category_id')
                                                ->relationship('category', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                            Forms\Components\Select::make('compatible_bike_ids')
                                                ->label('Compatible Bikes')
                                                ->options(BikeName::where('status',1)->get()->pluck('name', 'id'))
                                                ->searchable()
                                                ->multiple()
                                    ])
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
                Tables\Columns\ImageColumn::make('product_images'),
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('part_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mrp')
                    ->numeric()
                    ->money('INR')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('selling_price')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('buying_price')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
