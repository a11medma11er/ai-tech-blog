<?php

namespace App\Console\Commands;

use App\Services\AIOrchestratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunAIPipeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:run-ai-pipeline
                            {--only-process : معالجة المهام الموجودة فقط دون البحث عن جديدة}
                            {--limit= : تحديد عدد المهام المراد معالجتها}
                            {--force : تجاوز التأكيدات}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تشغيل دورة كاملة من نظام AI Blogging Pipeline: البحث عن الاتجاهات ومعالجة المهام وإنشاء المقالات';

    private AIOrchestratorService $orchestrator;

    /**
     * Execute the console command.
     */
    public function handle(AIOrchestratorService $orchestrator)
    {
        $this->orchestrator = $orchestrator;
        
        $startTime = now();
        
        // رسالة ترحيبية
        $this->displayWelcomeBanner();
        
        // التحقق من API Key
        if (!$this->validateApiKey()) {
            return Command::FAILURE;
        }
        
        $taskIds = [];
        
        // المرحلة 1: البحث عن الاتجاهات (إذا لم يكن --only-process)
        if (!$this->option('only-process')) {
            $taskIds = $this->fetchTrendsPhase();
            
            if (empty($taskIds)) {
                $this->error('❌ فشل البحث عن الاتجاهات');
                return Command::FAILURE;
            }
        }
        
        // المرحلة 2: معالجة المهام
        $results = $this->processTasksPhase();
        
        // المرحلة 3: عرض الملخص
        $this->displaySummary($results, $startTime);
        
        return Command::SUCCESS;
    }

    /**
     * عرض رسالة الترحيب
     */
    private function displayWelcomeBanner(): void
    {
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║        🤖 AI Blogging Pipeline - نظام إنشاء المحتوى       ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * التحقق من صحة API Key
     */
    private function validateApiKey(): bool
    {
        $apiKey = config('ai-services.gemini.api_key');
        
        if (empty($apiKey)) {
            $this->error('❌ لم يتم العثور على GEMINI_API_KEY في ملف .env');
            $this->warn('💡 يرجى إضافة المفتاح في ملف .env:');
            $this->line('   GEMINI_API_KEY=your_api_key_here');
            return false;
        }
        
        $this->info('✅ تم العثور على API Key');
        return true;
    }

    /**
     * المرحلة 1: البحث عن الاتجاهات
     */
    private function fetchTrendsPhase(): array
    {
        $this->info('📊 المرحلة 1: البحث عن الاتجاهات التقنية');
        $this->line('─────────────────────────────────────────────────────────');
        
        try {
            $count = config('ai-services.trends.count', 5);
            
            $this->line("🔍 البحث عن أحدث {$count} اتجاهات في مجالات AI والبرمجيات...");
            
            $taskIds = $this->orchestrator->fetchTrends();
            
            $this->info("✅ تم إنشاء {$count} مهام جديدة");
            $this->newLine();
            
            return $taskIds;
            
        } catch (\Exception $e) {
            $this->error('❌ فشل البحث: ' . $e->getMessage());
            Log::error('Trend fetch failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * المرحلة 2: معالجة المهام
     */
    private function processTasksPhase(): array
    {
        $this->info('⚙️  المرحلة 2: معالجة المهام وإنشاء المقالات');
        $this->line('─────────────────────────────────────────────────────────');
        
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        
        if ($limit) {
            $this->line("📝 سيتم معالجة {$limit} مهام فقط");
        }
        
        // الحصول على المهام المعلقة
        $pendingCount = \App\Models\AITask::pending()->count();
        
        if ($pendingCount === 0) {
            $this->warn('⚠️  لا توجد مهام معلقة للمعالجة');
            return [
                'total' => 0,
                'successful' => 0,
                'failed' => 0,
            ];
        }
        
        $this->line("📋 عدد المهام المعلقة: {$pendingCount}");
        $this->newLine();
        
        // شريط التقدم
        $tasksToProcess = $limit ? min($limit, $pendingCount) : $pendingCount;
        $progressBar = $this->output->createProgressBar($tasksToProcess);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
        $progressBar->setMessage('جاري المعالجة...');
        
        $progressBar->start();
        
        // معالجة المهام
        $tasks = \App\Models\AITask::pending()
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();
        
        $successful = 0;
        $failed = 0;
        
        foreach ($tasks as $task) {
            $progressBar->setMessage("معالجة: {$task->payload['title']}");
            
            $success = $this->orchestrator->processTask($task->id);
            
            if ($success) {
                $successful++;
            } else {
                $failed++;
            }
            
            $progressBar->advance();
            
            // تأخير بسيط لتجنب rate limiting
            if (config('ai-services.rate_limit.enabled')) {
                sleep(1);
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        return [
            'total' => $tasksToProcess,
            'successful' => $successful,
            'failed' => $failed,
        ];
    }

    /**
     * عرض ملخص النتائج
     */
    private function displaySummary(array $results, $startTime): void
    {
        $this->info('📈 المرحلة 3: ملخص النتائج');
        $this->line('─────────────────────────────────────────────────────────');
        
        $duration = $startTime->diffForHumans(now(), true);
        
        $this->table(
            ['المؤشر', 'القيمة'],
            [
                ['إجمالي المهام المعالجة', $results['total']],
                ['المقالات المُنشأة بنجاح', "✅ {$results['successful']}"],
                ['المهام الفاشلة', $results['failed'] > 0 ? "❌ {$results['failed']}" : "✅ 0"],
                ['الوقت المستغرق', $duration],
            ]
        );
        
        $this->newLine();
        
        // عرض إحصائيات عامة
        $stats = $this->orchestrator->getTaskStatistics();
        
        $this->info('📊 إحصائيات النظام الكاملة:');
        $this->table(
            ['الحالة', 'العدد'],
            [
                ['إجمالي المهام', $stats['total']],
                ['معلقة', $stats['pending']],
                ['قيد التنفيذ', $stats['running']],
                ['مكتملة', $stats['completed']],
                ['فاشلة', $stats['failed']],
            ]
        );
        
        $this->newLine();
        
        if ($results['successful'] > 0) {
            $this->info('🎉 تم إنشاء المقالات بنجاح! يمكنك مراجعتها من لوحة Filament');
            $this->line('   👉 http://localhost:8000/admin/posts');
        }
        
        if ($results['failed'] > 0) {
            $this->warn('⚠️  بعض المهام فشلت. يمكنك مراجعتها من:');
            $this->line('   👉 http://localhost:8000/admin/ai-tasks');
        }
        
        $this->newLine();
        $this->info('✨ انتهى التنفيذ بنجاح!');
    }
}
