<?php

namespace App\Filament\Pages;

use App\Models\PipelineSetting;
use Filament\Actions\Action;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PipelineSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pipeline Settings';
    protected static string | \UnitEnum | null $navigationGroup = 'AI Configuration';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'default_trends_count' => PipelineSetting::get('default_trends_count', 3),
            'default_min_words' => PipelineSetting::get('default_min_words', 500),
            'auto_publish' => PipelineSetting::get('auto_publish', false),
            'enable_featured_image' => PipelineSetting::get('enable_featured_image', true),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('default_trends_count')
                    ->label('Default Trends Count')
                    ->numeric()
                    ->default(3)
                    ->required(),
                    
                TextInput::make('default_min_words')
                    ->label('Default Minimum Words')
                    ->numeric()
                    ->default(500)
                    ->step(100)
                    ->required(),
                    
                Toggle::make('auto_publish')
                    ->label('Auto Publish Posts')
                    ->helperText('If enabled, posts will be published immediately after generation.')
                    ->default(false),
                    
                Toggle::make('enable_featured_image')
                    ->label('Generate Featured Images')
                    ->default(true),
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        PipelineSetting::set('default_trends_count', $data['default_trends_count'], 'integer');
        PipelineSetting::set('default_min_words', $data['default_min_words'], 'integer');
        PipelineSetting::set('auto_publish', $data['auto_publish'], 'boolean');
        PipelineSetting::set('enable_featured_image', $data['enable_featured_image'], 'boolean');
        
        Notification::make()
            ->title('Settings Saved')
            ->success()
            ->send();
    }
    
    public function getView(): string
    {
        return 'filament.pages.pipeline-settings';
    }
}
