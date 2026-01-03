<?php

namespace App\Filament\Resources\AITasks\Pages;

use App\Filament\Resources\AITasks\AITaskResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAITask extends ViewRecord
{
    protected static string $resource = AITaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
