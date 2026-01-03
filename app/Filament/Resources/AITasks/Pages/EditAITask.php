<?php

namespace App\Filament\Resources\AITasks\Pages;

use App\Filament\Resources\AITasks\AITaskResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAITask extends EditRecord
{
    protected static string $resource = AITaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
