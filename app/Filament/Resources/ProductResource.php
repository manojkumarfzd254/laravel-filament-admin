<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
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
                Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2) // 2 columns grid
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
                            ]),
                        Forms\Components\Grid::make(2) // Another 2 columns grid
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('part_number')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull(), // Full width for the editor
                        Forms\Components\Grid::make(3) // 3 columns for numeric fields
                            ->schema([
                                Forms\Components\TextInput::make('mrp')
                                    ->label('MRP')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('buying_price')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('selling_price')
                                    ->required()
                                    ->numeric(),

                            ]),
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
                            ->saveRelationshipsUsing(function ($component, $state, $record) {
                                foreach ($state as $filePath) {
                                    $record->images()->create([
                                        'path' => $filePath,
                                    ]);
                                }
                            }),
                    ]),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buying_price')
                    ->numeric()
                    ->sortable(),
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
