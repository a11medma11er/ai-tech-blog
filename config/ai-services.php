<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Services Configuration
    |--------------------------------------------------------------------------
    |
    | هنا يتم تكوين إعدادات خدمات الذكاء الاصطناعي المستخدمة في النظام
    |
    */

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash-exp'),
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 8000),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => 'https://api.openai.com/v1',
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 4000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Trend Search Settings
    |--------------------------------------------------------------------------
    */
    'trends' => [
        'topics' => [
            'Artificial Intelligence',
            'Machine Learning',
            'Software Development',
            'Web Development',
            'Cloud Computing',
            'DevOps',
            'Cybersecurity',
        ],
        'count' => env('AI_TRENDS_COUNT', 5),
        'search_depth' => env('AI_SEARCH_DEPTH', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Generation Settings
    |--------------------------------------------------------------------------
    */
    'content' => [
        'language' => env('AI_CONTENT_LANGUAGE', 'en'),
        'min_words' => env('AI_MIN_WORDS', 800),
        'max_words' => env('AI_MAX_WORDS', 1500),
        'include_code_examples' => env('AI_INCLUDE_CODE', true),
        'seo_optimized' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'enabled' => env('AI_RATE_LIMIT_ENABLED', true),
        'delay_between_requests' => env('AI_DELAY_SECONDS', 2), // ثانية
        'max_retries' => env('AI_MAX_RETRIES', 3),
    ],
];
