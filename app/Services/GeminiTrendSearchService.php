<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Services\Interfaces\TrendSearchServiceInterface;
use App\Models\AIProvider;

class GeminiTrendSearchService implements TrendSearchServiceInterface
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
     * البحث عن أحدث الاتجاهات التقنية
     */
    public function searchTrends(int $count = 5): array
    {
        try {
            $topics = config('ai-services.trends.topics');
            $topicsString = implode(', ', $topics);

            $prompt = $this->buildSearchPrompt($topicsString, $count);
            
            $response = $this->callGeminiAPI($prompt);
            
            $allTrends = $this->parseResponse($response);
            
            // تحديد العدد بالضبط - قص النتائج للعدد المطلوب فقط
            $trends = array_slice($allTrends, 0, $count);
            
            return $trends;
            
        } catch (\Exception $e) {
            Log::error('Trend search failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * بناء prompt للبحث عن الاتجاهات
     */
    private function buildSearchPrompt(string $topics, int $count): string
    {
        return <<<PROMPT
You are a tech trends analyst. Your task is to identify the top {$count} trending topics in the following areas: {$topics}.

Please provide EXACTLY {$count} trending topics that are:
1. Currently popular and relevant (as of 2026)
2. Interesting for a technical blog audience
3. Have enough depth for a detailed article
4. Cover different aspects of technology

For each trend, provide:
- Title: A catchy, SEO-friendly title (max 80 characters)
- Topic: The main subject area
- Keywords: 3-5 relevant keywords
- Description: A brief 2-3 sentence description
- Source URL: A credible source URL (use real, existing URLs from tech news sites)

Format your response as a JSON array with this structure:
[
  {
    "title": "...",
    "topic": "...",
    "keywords": ["...", "...", "..."],
    "description": "...",
    "source_url": "..."
  }
]

IMPORTANT: Return ONLY the JSON array, no additional text or markdown formatting.
PROMPT;
    }

    /**
     * استدعاء Gemini API
     */
    private function callGeminiAPI(string $prompt): string
    {
        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(60)
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
     * تحليل استجابة API واستخراج الاتجاهات
     */
    private function parseResponse(string $response): array
    {
        // تنظيف الاستجابة من markdown formatting
        $response = trim($response);
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```$/m', '', $response);
        $response = trim($response);

        $trends = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse JSON response: ' . json_last_error_msg());
        }

        if (!is_array($trends)) {
            throw new \Exception('Response is not an array');
        }

        // التحقق من صحة البيانات
        foreach ($trends as $trend) {
            if (!isset($trend['title']) || !isset($trend['topic']) || !isset($trend['keywords'])) {
                throw new \Exception('Invalid trend data structure');
            }
        }

        return $trends;
    }

    /**
     * التحقق من صحة API Key
     */
    public function validateApiKey(): bool
    {
        if (empty($this->apiKey)) {
            return false;
        }

        try {
            $this->callGeminiAPI('Test connection');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
