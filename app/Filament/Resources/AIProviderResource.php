<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AIProviderResource\Pages;
use App\Models\AIProvider;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AIProviderResource extends Resource
{
    protected static ?string $model = AIProvider::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';
    
    protected static ?string $navigationLabel = 'AI Providers';
    
    protected static string | \UnitEnum | null $navigationGroup = 'AI Configuration';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    
                Select::make('type')
                    ->options([
                        'gemini' => 'Google Gemini',
                        'openai' => 'OpenAI',
                        'openrouter' => 'OpenRouter',
                        'claude' => 'Anthropic Claude',
                    ])
                    ->required()
                    ->reactive(),
                    
                TextInput::make('api_key')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->revealable(),
                    
                TextInput::make('model')
                    ->label('Model Name')
                    ->placeholder('e.g., gemini-1.5-pro, gpt-4')
                    ->required(),
                    
                TextInput::make('base_url')
                    ->label('Base URL (Optional)')
                    ->url()
                    ->placeholder('Leave empty for default'),
                    
                KeyValue::make('settings')
                    ->label('Additional Settings')
                    ->keyLabel('Setting Name')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
                    
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                    
                Toggle::make('is_default')
                    ->label('Default Provider')
                    ->default(false),
                    
                TextInput::make('priority')
                    ->numeric()
                    ->default(0)
                    ->helperText('Higher priority providers are tried first'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                    
                TextColumn::make('type')
                    ->badge(),
                    
                TextColumn::make('model'),
                
                ToggleColumn::make('is_active')
                    ->label('Active'),
                    
                ToggleColumn::make('is_default')
                    ->label('Default'),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (AIProvider $record): string => static::getUrl('edit', ['record' => $record])),
                Action::make('test_connection')
                    ->label('Test Connection')
                    ->icon('heroicon-o-signal')
                    ->action(function (AIProvider $record) {
                        // TODO: Implement connection test
                        \Filament\Notifications\Notification::make()
                            ->title('Connection Test functionality coming soon')
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAIProviders::route('/'),
            'create' => Pages\CreateAIProvider::route('/create'),
            'edit' => Pages\EditAIProvider::route('/{record}/edit'),
        ];
    }
}
