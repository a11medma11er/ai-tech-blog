<?php

namespace App\Filament\Resources\AITasks\Schemas;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AITaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('task_type')
                    ->label('Task Type')
                    ->options([
                        'fetch_trends' => 'Fetch Trends',
                        'generate_article' => 'Generate Article',
                    ])
                    ->required(),
                    
                CodeEditor::make('payload')
                    ->label('Task Payload')
                    ->json()
                    ->required(),
                    
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'running' => 'Running',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->required(),
                    
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled At'),
                    
                Textarea::make('error_message')
                    ->label('Error Message')
                    ->disabled()
                    ->rows(3),
            ]);
    }
}
