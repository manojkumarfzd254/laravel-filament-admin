<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-s-home-modern';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name', fn($query) => $query->select(['id', 'name', 'part_number'])) // Ensure 'part_number' is available
                            ->searchable(['name', 'part_number'])
                            ->columnSpan(1)
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn($record) => "({$record->part_number}) - {$record->name}"),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->prefixicon('heroicon-s-square-3-stack-3d')
                            ->columnSpan(1)
                            ->numeric(),
                        Forms\Components\Radio::make('operation')
                            ->options([
                                'add' => 'Add',
                                'remove' => 'Remove',
                            ])
                            ->inline() // Display options inline
                            ->required()
                            ->default('add') // Default operation
                            ->columnSpan(2),
                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('operation')
                    ->label('Operation')
                    ->colors([
                        'success' => 'add',    // Green badge for "add"
                        'danger' => 'remove',  // Red badge for "remove"
                    ])
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state); // Capitalize the state for display
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListStocks::route('/'),
            // 'create' => Pages\CreateStock::route('/create'),
            // 'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
