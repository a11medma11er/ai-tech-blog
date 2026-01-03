<?php

namespace Database\Seeders;

use App\Models\AITask;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Seeder;

class AITaskDemoSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء تصنيف
        $category = Category::firstOrCreate(
            ['slug' => 'artificial-intelligence'],
            [
                'name' => 'Artificial Intelligence',
                'description' => 'Articles about AI and Machine Learning',
                'is_active' => true,
                'color' => '#3B82F6',
            ]
        );

        // إنشاء مقال تجريبي
        $post = Post::create([
            'title' => 'The Rise of AI Agents in 2026: Transforming Software Development',
            'slug' => 'the-rise-of-ai-agents-in-2026',
            'content' => '<h2>Introduction</h2>
<p>Artificial Intelligence agents are revolutionizing the way we approach software development and business automation in 2026. These intelligent systems are no longer just tools—they are becoming collaborative partners in the development process.</p>

<h2>What Are AI Agents?</h2>
<p>AI agents are autonomous software entities that can perceive their environment, make decisions, and take actions to achieve specific goals. Unlike traditional software, they can learn from experience and adapt to new situations.</p>

<h3>Key Characteristics</h3>
<ul>
<li><strong>Autonomy:</strong> They operate independently without constant human intervention</li>
<li><strong>Reactivity:</strong> They respond to changes in their environment</li>
<li><strong>Proactivity:</strong> They take initiative to achieve goals</li>
<li><strong>Social Ability:</strong> They interact with other agents and humans</li>
</ul>

<h2>Impact on Software Development</h2>
<p>AI agents are transforming software development in several ways:</p>

<h3>1. Code Generation and Review</h3>
<p>Modern AI agents can generate high-quality code, review pull requests, and suggest improvements based on best practices and project-specific patterns.</p>

<h3>2. Automated Testing</h3>
<p>They can create comprehensive test suites, identify edge cases, and even fix bugs automatically.</p>

<h3>3. Documentation</h3>
<p>AI agents excel at generating and maintaining documentation, ensuring it stays up-to-date with code changes.</p>

<h2>Business Automation</h2>
<p>Beyond development, AI agents are streamlining business processes through intelligent automation of repetitive tasks, data analysis, and decision-making support.</p>

<h2>Conclusion</h2>
<p>As we progress through 2026, AI agents are becoming indispensable tools in the software development lifecycle. Their ability to learn, adapt, and collaborate makes them powerful allies in building better software faster.</p>',
            'category_id' => $category->id,
            'source_url' => 'https://example.com/ai-trends-2026',
            'is_published' => false,
            'published_at' => null,
        ]);

        // إنشاء مهمة مكتملة
        AITask::create([
            'task_type' => 'generate_article',
            'payload' => [
                'title' => 'The Rise of AI Agents in 2026',
                'topic' => 'Artificial Intelligence',
                'keywords' => ['AI Agents', 'Automation', 'Machine Learning', 'Software Development'],
                'description' => 'Exploring how AI agents are transforming software development and business automation in 2026.',
                'source_url' => 'https://example.com/ai-trends-2026',
            ],
            'status' => 'completed',
            'result' => [
                'post_id' => $post->id,
                'post_title' => $post->title,
                'post_slug' => $post->slug,
                'meta_description' => 'Discover how AI agents are revolutionizing software development and business automation in 2026.',
                'featured_image_prompt' => 'A futuristic illustration of AI agents collaborating with human developers, digital art style, blue and purple color scheme',
                'estimated_reading_time' => 3,
            ],
            'scheduled_at' => now()->subMinutes(10),
            'started_at' => now()->subMinutes(9),
            'completed_at' => now()->subMinutes(5),
        ]);

        // إنشاء مهمة معلقة
        AITask::create([
            'task_type' => 'generate_article',
            'payload' => [
                'title' => 'Quantum Computing Breakthroughs in 2026',
                'topic' => 'Quantum Computing',
                'keywords' => ['Quantum', 'Computing', 'Technology'],
                'description' => 'Latest advances in quantum computing technology.',
                'source_url' => 'https://example.com/quantum-2026',
            ],
            'status' => 'pending',
            'scheduled_at' => now(),
        ]);

        // إنشاء مهمة فاشلة
        AITask::create([
            'task_type' => 'generate_article',
            'payload' => [
                'title' => 'Blockchain Evolution',
                'topic' => 'Blockchain',
                'keywords' => ['Blockchain', 'Web3'],
                'description' => 'The evolution of blockchain technology.',
                'source_url' => 'https://example.com/blockchain',
            ],
            'status' => 'failed',
            'error_message' => 'API rate limit exceeded. Please retry later.',
            'scheduled_at' => now()->subHour(),
            'started_at' => now()->subMinutes(55),
            'completed_at' => now()->subMinutes(50),
        ]);

        $this->command->info('✅ تم إنشاء بيانات تجريبية بنجاح!');
        $this->command->info('📊 المهام: 3 (1 مكتملة، 1 معلقة، 1 فاشلة)');
        $this->command->info('📝 المقالات: 1');
    }
}
