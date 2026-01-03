<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Services\Interfaces\ContentGeneratorInterface;
use App\Models\AIProvider;

class GeminiContentGenerator implements ContentGeneratorInterface
{
    private ?string $apiKey;
    private ?string $baseUrl;
    private ?string $model;

    public function __construct(?AIProvider $provider = null)
    {
        if ($provider) {
            $this->apiKey = $provider->api_key;
            $this->baseUrl = $provider->base_url ?? config('ai-services.gemini.base_url');
            $this->model = $provider->model;
        } else {
            $this->apiKey = config('ai-services.gemini.api_key');
            $this->baseUrl = config('ai-services.gemini.base_url');
            $this->model = config('ai-services.gemini.model');
        }
    }

    /**
     * إنشاء مقال تقني احترافي
     */
    public function generateArticle(array $trendData): array
    {
        try {
            $prompt = $this->buildArticlePrompt($trendData);
            
            $response = $this->callGeminiAPI($prompt);
            
            $article = $this->parseArticleResponse($response);
            
            // إضافة slug تلقائي
            $article['slug'] = Str::slug($article['title']);
            
            return $article;
            
        } catch (\Exception $e) {
            Log::error('Article generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * بناء prompt لإنشاء المقال
     */
    private function buildArticlePrompt(array $trendData): string
    {
        $title = $trendData['title'];
        $topic = $trendData['topic'];
        $keywords = implode(', ', $trendData['keywords']);
        $description = $trendData['description'];
        
        $minWords = config('ai-services.content.min_words');
        $maxWords = config('ai-services.content.max_words');
        $includeCode = config('ai-services.content.include_code_examples');
        $language = config('ai-services.content.language', 'en');
        
        // تحديد تعليمات اللغة بشكل واضح وصريح
        if ($language === 'ar') {
            $languageInstruction = 'Write the ENTIRE article in professional, clear, Modern Standard Arabic (الفصحى)';
            $titleNote = 'العنوان يجب أن يكون بالعربية';
            $contentNote = 'المحتوى الكامل يجب أن يكون بالعربية الفصحى';
        } else {
            $languageInstruction = 'Write in professional, clear English';
            $titleNote = 'Title should be in English';
            $contentNote = 'Full content should be in English';
        }

        return <<<PROMPT
You are a professional tech blogger and content writer. Write a comprehensive, SEO-optimized technical article about: "{$title}"

Topic: {$topic}
Keywords to include: {$keywords}
Context: {$description}

CRITICAL LANGUAGE REQUIREMENT:
{$languageInstruction}
{$titleNote}
{$contentNote}

Requirements:
1. Write between {$minWords}-{$maxWords} words
2. Use a catchy, engaging title (you can improve the provided title)
3. Structure with proper H2 and H3 headings
4. IMPORTANT: {$languageInstruction} - ALL text including title, content, headings, and meta description
5. Include practical insights and real-world applications
6. Make it SEO-friendly with natural keyword integration
7. Add a compelling introduction and conclusion
{$this->getCodeExampleRequirement($includeCode)}

Format your response as JSON with this EXACT structure:
{
  "title": "The final article title IN THE SPECIFIED LANGUAGE",
  "content": "Full HTML content with <h2>, <h3>, <p>, <ul>, <ol>, <code>, <pre> tags IN THE SPECIFIED LANGUAGE",
  "meta_description": "SEO meta description (max 160 characters) IN THE SPECIFIED LANGUAGE",
  "featured_image_prompt": "A detailed prompt for AI image generation (this can be in English)",
  "estimated_reading_time": 5
}

IMPORTANT: 
- Return ONLY the JSON object, no additional text or markdown formatting
- The content should be in HTML format with proper tags
- Use <h2> for main sections and <h3> for subsections
- Make the content engaging and valuable for readers
- LANGUAGE: {$languageInstruction}
PROMPT;
    }

    /**
     * الحصول على متطلبات أمثلة الكود
     */
    private function getCodeExampleRequirement(bool $includeCode): string
    {
        if ($includeCode) {
            return "8. Include relevant code examples where appropriate (use <pre><code> tags)";
        }
        return "";
    }

    /**
     * استدعاء Gemini API
     */
    private function callGeminiAPI(string $prompt): string
    {
        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        // إضافة تأخير للـ rate limiting
        if (config('ai-services.rate_limit.enabled')) {
            sleep(config('ai-services.rate_limit.delay_between_requests'));
        }

        $response = Http::timeout(120) // وقت أطول لإنشاء المحتوى
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => config('ai-services.gemini.temperature'),
                    'maxOutputTokens' => config('ai-services.gemini.max_tokens'),
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception('Gemini API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid response structure from Gemini API');
        }

        return $data['candidates'][0]['content']['parts'][0]['text'];
    }

    /**
     * تحليل استجابة المقال
     */
    private function parseArticleResponse(string $response): array
    {
        // تنظيف الاستجابة
        $response = trim($response);
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```$/m', '', $response);
        $response = trim($response);

        $article = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse article JSON: ' . json_last_error_msg());
        }

        // التحقق من الحقول المطلوبة
        $requiredFields = ['title', 'content', 'meta_description', 'featured_image_prompt'];
        foreach ($requiredFields as $field) {
            if (!isset($article[$field]) || empty($article[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        return $article;
    }

    /**
     * إنشاء صورة مميزة (placeholder - يمكن تطويره لاحقاً)
     */
    public function generateFeaturedImage(string $prompt): ?string
    {
        // TODO: تكامل مع DALL-E أو Gemini Image Generation
        // حالياً سنعيد null وسنستخدم الـ prompt فقط
        Log::info('Featured image prompt: ' . $prompt);
        return null;
    }
}
