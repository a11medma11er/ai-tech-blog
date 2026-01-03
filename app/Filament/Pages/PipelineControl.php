<?php

namespace App\Filament\Pages;

use App\Models\AIProvider;
use App\Models\AITask;
use App\Services\AIOrchestratorService;
use Filament\Actions\Action;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

use Livewire\Attributes\Computed;

class PipelineControl extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationLabel = 'AI Pipeline Control';
    protected static string | \UnitEnum | null $navigationGroup = 'AI Configuration';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'provider_id' => AIProvider::default()->first()?->id ?? AIProvider::active()->first()?->id,
            'trends_count' => 3,
            'min_words' => 500,
            'language' => 'en',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('provider_id')
                    ->label('Select AI Provider')
                    ->options(AIProvider::active()->pluck('name', 'id'))
                    ->required()
                    ->helperText('Choose which AI provider to use for this run.'),
                    
                Select::make('language')
                    ->label('Content Language')
                    ->options([
                        'en' => 'English',
                        'ar' => 'العربية',
                    ])
                    ->default('en')
                    ->required()
                    ->helperText('Select the language for generated articles.'),
                    
                TextInput::make('trends_count')
                    ->label('Number of Trends')
                    ->numeric()
                    ->default(3)
                    ->minValue(1)
                    ->maxValue(10)
                    ->required(),
                    
                TextInput::make('min_words')
                    ->label('Target Word Count')
                    ->numeric()
                    ->default(500)
                    ->step(100)
                    ->required(),
            ])
            ->columns(3)
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetch_trends')
                ->label('Fetch New Trends')
                ->icon('heroicon-o-magnifying-glass')
                ->action(function () {
                    $this->runPipeline('fetch_trends');
                })
                ->requiresConfirmation()
                ->color('primary'),
                
            Action::make('process_pending')
                ->label('Process Pending Tasks')
                ->icon('heroicon-o-cpu-chip')
                ->action(function () {
                    $this->runPipeline('process_pending');
                })
                ->requiresConfirmation()
                ->color('warning'),
        ];
    }

    public function runPipeline(string $mode = 'all')
    {
        $data = $this->form->getState();
        $provider = AIProvider::find($data['provider_id']);
        
        if (!$provider) {
            Notification::make()
                ->title('Error')
                ->body('Selected AI Provider not found.')
                ->danger()
                ->send();
            return;
        }

        try {
            // override config dynamically for this run
            config(['ai-services.trends_count' => $data['trends_count']]);
            config(['ai-services.content.min_words' => $data['min_words']]);
            config(['ai-services.content.language' => $data['language'] ?? 'en']);
            
            // Initiate Service with Provider
            // Note: We need to implement setProvider in AIOrchestratorService
            $orchestrator = app(AIOrchestratorService::class);
            
            if (method_exists($orchestrator, 'setProvider')) {
                $orchestrator->setProvider($provider);
            }

            $message = '';
            
            if ($mode === 'fetch_trends' || $mode === 'all') {
                $result = $orchestrator->fetchTrends($data['trends_count'], $data['language']);
                $message .= "Fetched {$result['created']} new trends (Language: {$data['language']}). ";
            }
            
            if ($mode === 'process_pending' || $mode === 'all') {
                // Background processing is better, but for now we run simplistic
                // Or trigger the artisan command
                // Let's use the service method directly if possible, or trigger a batch
                
                // For demonstration, we'll just say tasks are queued if we had queues
                // But current implementation is synchronous loop in command
                
                // We'll call processPendingTasks logic here similarly to command
                $tasks = AITask::pending()->get();
                $count = 0;
                foreach($tasks as $task) {
                    $orchestrator->processTask($task->id);
                    $count++;
                }
                $message .= "Processed {$count} tasks.";
            }

            Notification::make()
                ->title('Pipeline Execution Successful')
                ->body($message)
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Pipeline Execution Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    #[Computed]
    public function stats()
    {
        return [
            'pending' => AITask::pending()->count(),
            'completed' => AITask::completed()->count(),
            'failed' => AITask::failed()->count(),
            'posts' => \App\Models\Post::count(),
        ];
    }
    
    public function getView(): string
    {
        return 'filament.pages.pipeline-control';
    }
}
