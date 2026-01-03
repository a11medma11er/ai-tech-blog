<?php

namespace App\Filament\Resources\AITasks;

use App\Filament\Resources\AITasks\Pages\CreateAITask;
use App\Filament\Resources\AITasks\Pages\EditAITask;
use App\Filament\Resources\AITasks\Pages\ListAITasks;
use App\Filament\Resources\AITasks\Pages\ViewAITask;
use App\Filament\Resources\AITasks\Schemas\AITaskForm;
use App\Filament\Resources\AITasks\Schemas\AITaskInfolist;
use App\Filament\Resources\AITasks\Tables\AITasksTable;
use App\Models\AITask;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AITaskResource extends Resource
{
    protected static ?string $model = AITask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $recordTitleAttribute = 'task_type';
    
    protected static ?string $navigationLabel = 'AI Tasks';
    
    protected static ?string $modelLabel = 'AI Task';
    
    protected static ?string $pluralModelLabel = 'AI Tasks';
    
    protected static string|UnitEnum|null $navigationGroup = 'AI System';
    
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return AITaskForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AITaskInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AITasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAITasks::route('/'),
            'create' => CreateAITask::route('/create'),
            'view' => ViewAITask::route('/{record}'),
            'edit' => EditAITask::route('/{record}/edit'),
        ];
    }
}
