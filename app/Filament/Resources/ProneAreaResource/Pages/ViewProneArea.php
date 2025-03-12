<?php

namespace App\Filament\Resources\ProneAreaResource\Pages;

use App\Filament\Resources\ProneAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProneArea extends ViewRecord
{
    protected static string $resource = ProneAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
