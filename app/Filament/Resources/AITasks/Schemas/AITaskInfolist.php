<?php

namespace App\Filament\Resources\AITasks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AITaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('task_type')
                    ->label('Task Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fetch_trends' => 'info',
                        'generate_article' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fetch_trends' => 'Fetch Trends',
                        'generate_article' => 'Generate Article',
                        default => $state,
                    }),

                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'running' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'running' => 'heroicon-o-arrow-path',
                        'completed' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'running' => 'Running',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        default => $state,
                    }),

                TextEntry::make('payload')
                    ->label('Task Payload')
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->columnSpanFull(),

                TextEntry::make('scheduled_at')
                    ->label('Scheduled At')
                    ->dateTime('Y-m-d H:i:s'),

                TextEntry::make('started_at')
                    ->label('Started At')
                    ->dateTime('Y-m-d H:i:s')
                    ->placeholder('Not started yet'),

                TextEntry::make('completed_at')
                    ->label('Completed At')
                    ->dateTime('Y-m-d H:i:s')
                    ->placeholder('Not completed yet'),

                TextEntry::make('result')
                    ->label('Result')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null)
                    ->columnSpanFull()
                    ->hidden(fn ($record) => !$record->result),

                TextEntry::make('error_message')
                    ->label('Error Message')
                    ->color('danger')
                    ->columnSpanFull()
                    ->hidden(fn ($record) => !$record->error_message),

                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->columns(2);
    }
}
