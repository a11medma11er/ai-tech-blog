<?php

namespace App\Services;

use App\Models\AITask;
use App\Models\Post;
use App\Models\Category;
use App\Models\AIProvider;
use App\Services\Interfaces\TrendSearchServiceInterface;
use App\Services\Interfaces\ContentGeneratorInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AIOrchestratorService
{
    private TrendSearchServiceInterface $trendSearchService;
    private ContentGeneratorInterface $contentGenerator;

    public function __construct(
        TrendSearchServiceInterface $trendSearchService,
        ContentGeneratorInterface $contentGenerator
    ) {
        $this->trendSearchService = $trendSearchService;
        $this->contentGenerator = $contentGenerator;
    }

    public function setProvider(AIProvider $provider): self
    {
        $this->trendSearchService = AIProviderFactory::makeTrendSearch($provider);
        $this->contentGenerator = AIProviderFactory::makeContentGenerator($provider);
        return $this;
    }

    /**
     * البحث عن الاتجاهات وإنشاء المهام
     */
    public function fetchTrends(int $count = null, string $language = 'en'): array
    {
        try {
            $count = $count ?? config('ai-services.trends.count', 5);
            
            Log::info("Starting trend search for {$count} topics...");
            
            // البحث عن الاتجاهات
            $trends = $this->trendSearchService->searchTrends($count);
            
            Log::info("Found " . count($trends) . " trends, creating tasks...");
            
            $taskIds = [];
            
            // إنشاء مهمة لكل اتجاه
            foreach ($trends as $trend) {
                // Ensure trend has required fields
                if (!isset($trend['title'])) {
                    continue;
                }

                // إضافة اللغة المطلوبة إلى payload المهمة
                $trend['language'] = $language;

                $task = AITask::create([
                    'task_type' => 'generate_article',
                    'payload' => $trend,
                    'status' => 'pending',
                    'scheduled_at' => now(),
                ]);
                
                $taskIds[] = $task->id;
                
                Log::info("Created task #{$task->id} for: {$trend['title']} (Language: {$language})");
            }
            
            return [
                'created' => count($taskIds),
                'task_ids' => $taskIds
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch trends: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * معالجة مهمة واحدة وإنشاء مقال
     */
    public function processTask(int $taskId): bool
    {
        $task = AITask::find($taskId);
        
        if (!$task) {
            Log::error("Task #{$taskId} not found");
            return false;
        }
        
        if ($task->status !== 'pending') {
            Log::warning("Task #{$taskId} is not pending (status: {$task->status})");
            return false;
        }
        
        try {
            // تحديث حالة المهمة إلى running
            $task->markAsRunning();
            
            Log::info("Processing task #{$taskId}: {$task->payload['title']}");
            
            // قراءة اللغة من payload المهمة
            $language = $task->payload['language'] ?? 'en';
            config(['ai-services.content.language' => $language]);
            
            Log::info("Generating article in language: {$language}");
            
            // إنشاء المقال
            $articleData = $this->contentGenerator->generateArticle($task->payload);
            
            // الحصول على أو إنشاء تصنيف
            $category = $this->getOrCreateCategory($task->payload['topic']);
            
            // التأكد من أن الـ slug فريد
            $uniqueSlug = $this->ensureUniqueSlug($articleData['slug']);
            
            // حفظ المقال في قاعدة البيانات
            $post = Post::create([
                'title' => $articleData['title'],
                'slug' => $uniqueSlug,
                'content' => $articleData['content'],
                'category_id' => $category->id,
                'source_url' => $task->payload['source_url'] ?? null,
                'is_published' => false, // مسودة للمراجعة
                'published_at' => null,
            ]);
            
            Log::info("Created post #{$post->id}: {$post->title}");
            
            // حفظ النتيجة في المهمة
            $result = [
                'post_id' => $post->id,
                'post_title' => $post->title,
                'post_slug' => $post->slug,
                'meta_description' => $articleData['meta_description'] ?? null,
                'featured_image_prompt' => $articleData['featured_image_prompt'] ?? null,
                'estimated_reading_time' => $articleData['estimated_reading_time'] ?? null,
            ];
            
            $task->markAsCompleted($result);
            
            Log::info("Task #{$taskId} completed successfully");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Task #{$taskId} failed: " . $e->getMessage());
            $task->markAsFailed($e->getMessage());
            return false;
        }
    }

    /**
     * معالجة جميع المهام المعلقة
     */
    public function processPendingTasks(int $limit = null): array
    {
        $query = AITask::pending()->orderBy('scheduled_at');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        $tasks = $query->get();
        
        $results = [
            'total' => $tasks->count(),
            'successful' => 0,
            'failed' => 0,
            'task_ids' => [],
        ];
        
        foreach ($tasks as $task) {
            $success = $this->processTask($task->id);
            
            if ($success) {
                $results['successful']++;
            } else {
                $results['failed']++;
            }
            
            $results['task_ids'][] = $task->id;
        }
        
        return $results;
    }

    /**
     * الحصول على أو إنشاء تصنيف
     */
    private function getOrCreateCategory(string $topicName): Category
    {
        // البحث عن تصنيف موجود
        $category = Category::where('name', $topicName)->first();
        
        if ($category) {
            return $category;
        }
        
        // إنشاء تصنيف جديد
        return Category::create([
            'name' => $topicName,
            'slug' => \Illuminate\Support\Str::slug($topicName),
            'description' => "Articles about {$topicName}",
            'is_active' => true,
            'color' => $this->getRandomColor(),
        ]);
    }

    /**
     * الحصول على لون عشوائي للتصنيف
     */
    private function getRandomColor(): string
    {
        $colors = [
            '#3B82F6', // blue
            '#10B981', // green
            '#F59E0B', // amber
            '#EF4444', // red
            '#8B5CF6', // purple
            '#EC4899', // pink
            '#06B6D4', // cyan
        ];
        
        return $colors[array_rand($colors)];
    }

    /**
     * التأكد من أن الـ slug فريد
     */
    private function ensureUniqueSlug(string $slug): string
    {
        $originalSlug = $slug;
        $counter = 1;
        
        // التحقق إذا كان الـ slug موجود
        while (Post::where('slug', $slug)->exists()) {
            // إضافة رقم للـ slug
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * إنشاء صورة مميزة (اختياري)
     */
    public function generateFeaturedImage(string $prompt): ?string
    {
        return $this->contentGenerator->generateFeaturedImage($prompt);
    }

    /**
     * الحصول على إحصائيات المهام
     */
    public function getTaskStatistics(): array
    {
        return [
            'total' => AITask::count(),
            'pending' => AITask::pending()->count(),
            'running' => AITask::running()->count(),
            'completed' => AITask::completed()->count(),
            'failed' => AITask::failed()->count(),
        ];
    }

    /**
     * إعادة معالجة مهمة فاشلة
     */
    public function retryFailedTask(int $taskId): bool
    {
        $task = AITask::find($taskId);
        
        if (!$task || $task->status !== 'failed') {
            return false;
        }
        
        // إعادة تعيين الحالة إلى pending
        $task->update([
            'status' => 'pending',
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ]);
        
        // معالجة المهمة
        return $this->processTask($taskId);
    }
}
