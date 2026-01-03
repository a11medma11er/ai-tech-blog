<?php

namespace App\Filament\Resources\AIProviderResource\Pages;

use App\Filament\Resources\AIProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAIProviders extends ListRecords
{
    protected static string $resource = AIProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
