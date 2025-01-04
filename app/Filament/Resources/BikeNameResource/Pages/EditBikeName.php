<?php

namespace App\Filament\Resources\BikeNameResource\Pages;

use App\Filament\Resources\BikeNameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBikeName extends EditRecord
{
    protected static string $resource = BikeNameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
