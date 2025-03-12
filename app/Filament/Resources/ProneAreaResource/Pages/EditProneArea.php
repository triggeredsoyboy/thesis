<?php

namespace App\Filament\Resources\ProneAreaResource\Pages;

use App\Filament\Resources\ProneAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProneArea extends EditRecord
{
    protected static string $resource = ProneAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
