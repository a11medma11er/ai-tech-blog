<?php

namespace App\Filament\Resources\AITasks\Pages;

use App\Filament\Resources\AITasks\AITaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAITasks extends ListRecords
{
    protected static string $resource = AITaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
