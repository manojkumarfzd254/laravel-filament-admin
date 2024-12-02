<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';

    // public static function getPermissionPrefixes(): array
    // {
    //     return [
    //         'view',
    //         // 'view_any',
    //         'create',
    //         'update',
    //         'delete',
    //         // 'delete_any',
    //         // 'publish'
    //     ];
    // }
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->label('Name')
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->label('Email Address')
                ->unique(ignoreRecord: true), // Ensures email uniqueness

            Forms\Components\TextInput::make('password')
                ->password()
                ->label('Password')
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord), // Required only on create

            Forms\Components\TextInput::make('phone_number')
                ->label('Phone Number')
                ->tel()
                ->maxLength(15),

            Forms\Components\Textarea::make('address')
                ->label('Address')
                ->maxLength(500),

            Forms\Components\DatePicker::make('dob')
                ->label('Date of Birth'),

            Forms\Components\Select::make('roles')
                ->searchable()
                ->relationship('roles', 'name') // Define relationship if you have it
                ->options(Role::all()->pluck('name', 'id')->toArray())
                ->preload()
                ->label('Roles'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('phone')
                ->label('Phone Number')
                ->searchable()
                ->sortable(),

                Tables\Columns\BadgeColumn::make('roles.name')
                ->label('Roles')
                ->colors([
                    'primary'  => fn ($state): bool => $state === 'Admin',
                    'secondary' => fn ($state): bool => $state === 'Technician',
                    'success' => fn ($state): bool => $state === 'Manager',
                    // Add custom color logic if needed
                ])
                ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                ->sortable(),

            Tables\Columns\TextColumn::make('dob')
                ->label('Date of Birth')
                ->date('F j, Y')
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime('F j, Y H:i')
                ->sortable(),
        ])
        ->filters([
            // You can define filters here if needed
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
