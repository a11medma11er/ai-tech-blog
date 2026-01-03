<?php

namespace App\Services;

use App\Models\AIProvider;
use App\Services\Interfaces\ContentGeneratorInterface;
use App\Services\Interfaces\TrendSearchServiceInterface;

class AIProviderFactory
{
    public static function makeTrendSearch(?AIProvider $provider = null): TrendSearchServiceInterface
    {
        if (!$provider) {
            // Default behavior (use config)
            return new GeminiTrendSearchService();
        }

        return match ($provider->type) {
            'gemini' => new GeminiTrendSearchService($provider),
            // 'openai' => new OpenAITrendSearchService($provider),
            // 'openrouter' => new OpenRouterTrendSearchService($provider),
            default => new GeminiTrendSearchService($provider),
        };
    }

    public static function makeContentGenerator(?AIProvider $provider = null): ContentGeneratorInterface
    {
        if (!$provider) {
            return new GeminiContentGenerator();
        }

        return match ($provider->type) {
            'gemini' => new GeminiContentGenerator($provider),
            // 'openai' => new OpenAIContentGenerator($provider),
            default => new GeminiContentGenerator($provider),
        };
    }
}
