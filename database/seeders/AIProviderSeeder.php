<?php

namespace Database\Seeders;

use App\Models\AIProvider;
use Illuminate\Database\Seeder;

class AIProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing providers
        AIProvider::truncate();

        // Create default Gemini provider
        AIProvider::create([
            'name' => 'Gemini AI (Default)',
            'type' => 'gemini',
            'api_key' => env('GEMINI_API_KEY', ''),
            'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'settings' => [
                'temperature' => 0.7,
                'max_tokens' => 8000,
            ],
            'is_active' => true,
            'is_default' => true,
            'priority' => 10,
            'description' => 'Default Google Gemini AI provider for content generation',
        ]);

        // Create example OpenAI provider (inactive by default)
        AIProvider::create([
            'name' => 'OpenAI GPT-4',
            'type' => 'openai',
            'api_key' => '', // User should add their own key
            'model' => 'gpt-4',
            'base_url' => 'https://api.openai.com/v1',
            'settings' => [
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ],
            'is_active' => false,
            'is_default' => false,
            'priority' => 5,
            'description' => 'OpenAI GPT-4 for high-quality content generation',
        ]);

        // Create example OpenRouter provider (inactive by default)
        AIProvider::create([
            'name' => 'OpenRouter',
            'type' => 'openrouter',
            'api_key' => '', // User should add their own key
            'model' => 'anthropic/claude-3-opus',
            'base_url' => 'https://openrouter.ai/api/v1',
            'settings' => [
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ],
            'is_active' => false,
            'is_default' => false,
            'priority' => 3,
            'description' => 'OpenRouter for accessing multiple AI models',
        ]);

        $this->command->info('✅ Created 3 AI Providers (1 active, 2 inactive examples)');
    }
}
