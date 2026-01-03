<?php

namespace App\Filament\Resources\AITasks\Tables;

use App\Models\AITask;
use App\Services\AIOrchestratorService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class AITasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('task_type')
                    ->label('Task Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fetch_trends' => 'info',
                        'generate_article' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('status')
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
                    })
                    ->sortable(),
                    
                TextColumn::make('payload.title')
                    ->label('Title')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                    
                TextColumn::make('scheduled_at')
                    ->label('Scheduled At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                    
                TextColumn::make('started_at')
                    ->label('Started At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('completed_at')
                    ->label('Completed At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'running' => 'Running',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                    
                SelectFilter::make('task_type')
                    ->label('Task Type')
                    ->options([
                        'fetch_trends' => 'Fetch Trends',
                        'generate_article' => 'Generate Article',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('View'),
                    
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (AITask $record): bool => $record->status === 'failed')
                    ->requiresConfirmation()
                    ->action(function (AITask $record) {
                        $orchestrator = app(AIOrchestratorService::class);
                        $success = $orchestrator->retryFailedTask($record->id);
                        
                        if ($success) {
                            Notification::make()
                                ->title('Task retried successfully')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Retry failed')
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Action::make('view_post')
                    ->label('View Post')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn (AITask $record): bool => 
                        $record->status === 'completed' && 
                        isset($record->result['post_id'])
                    )
                    ->url(fn (AITask $record): string => 
                        \App\Filament\Resources\Posts\PostResource::getUrl('edit', [
                            'record' => $record->result['post_id']
                        ])
                    ),
                    
                EditAction::make()
                    ->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ]);
    }
}
