<?php

namespace App\Filament\Resources\ProneAreaResource\Pages;

use App\Filament\Resources\ProneAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProneAreas extends ListRecords
{
    protected static string $resource = ProneAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
