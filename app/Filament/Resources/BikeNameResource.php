<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikeNameResource\Pages;
use App\Filament\Resources\BikeNameResource\RelationManagers;
use App\Models\BikeName;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BikeNameResource extends Resource
{
    protected static ?string $model = BikeName::class;

    protected static ?string $navigationIcon = 'heroicon-s-cube';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->required(),
                            Select::make('brand_id')
                            ->searchable()
                            ->label('Brand')
                            ->placeholder('Select a brand')
                            ->options(Brand::where('status',1)->get()->pluck('name', 'id')),
                        Forms\Components\Toggle::make('status')
                            ->required()
                            ->default(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand Name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('brand.logo')
                ->label('Brand Logo'),
                Tables\Columns\ToggleColumn::make('status')
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
            'index' => Pages\ListBikeNames::route('/'),
            'create' => Pages\CreateBikeName::route('/create'),
            'edit' => Pages\EditBikeName::route('/{record}/edit'),
        ];
    }
}
