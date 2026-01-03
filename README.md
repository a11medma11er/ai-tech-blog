# 🤖 AI Tech Blog

> **Professional AI-Powered Blog Platform with Advanced Content Generation**

A cutting-edge Laravel + Filament v4 blogging platform that leverages AI to automatically discover trending tech topics and generate high-quality technical articles in multiple languages.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v4.4-orange.svg)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://www.php.net)

---

## ✨ Key Features

### 🎯 AI-Powered Content Generation
- **Multi-Provider AI Support**: Integrate with Gemini, OpenAI, Claude, and OpenRouter
- **Automated Trend Discovery**: AI scans tech landscape for trending topics
- **Smart Article Generation**: Creates SEO-optimized, professional technical content
- **Multi-Language Support**: Generate articles in English or Arabic
- **Task Queue System**: Manage and track content generation tasks

### 🛠️ Professional Dashboard
- **AI Provider Management**: Configure and manage multiple AI providers
- **Pipeline Control Center**: Monitor and control article generation workflow
- **Real-time Statistics**: Track pending, running, completed, and failed tasks
- **Flexible Configuration**: Customizable settings for trends, word count, and more

### 📝 Content Management
- **Rich Text Editor**: Advanced TinyMCE integration
- **Category System**: Organize content efficiently
- **SEO Optimization**: Built-in meta tags and optimization
- **Draft/Publish Workflow**: Review before publishing
- **Source Tracking**: Track article sources and inspirations

### 🔒 Enterprise Features
- **Encrypted API Keys**: Secure storage with Laravel encryption
- **Multi-User Support**: Role-based access control
- **Activity Logging**: Track all AI operations
- **Error Handling**: Robust retry mechanisms
- **Scalable Architecture**: Clean service-oriented design

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL)

### Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/ai-tech-blog.git
cd ai-tech-blog

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed AI providers (optional)
php artisan db:seed --class=AIProviderSeeder

# Build assets
npm run build

# Start the server
php artisan serve
```

Visit `http://localhost:8000/admin` to access the dashboard.

---

## ⚙️ Configuration

### 1. Environment Variables

Add to your `.env` file:

```env
# Gemini AI (Default Provider)
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-2.0-flash-exp

# OpenAI (Optional)
OPENAI_API_KEY=your_openai_key_here
OPENAI_MODEL=gpt-4

# Pipeline Defaults
AI_TRENDS_COUNT=5
AI_MIN_WORDS=800
AI_MAX_WORDS=1500
AI_CONTENT_LANGUAGE=en
```

### 2. AI Provider Setup

#### Via Dashboard (Recommended)
1. Navigate to **Admin → AI System → AI Providers**
2. Click **Create** and fill in:
   - Name: e.g., "Gemini Pro"
   - Type: Select provider (Gemini/OpenAI/etc.)
   - API Key: Your API key
   - Model: Model name
3. Set as **Active** and **Default**

#### Via Seeder
```bash
php artisan db:seed --class=AIProviderSeeder
```

This creates:
- ✅ Gemini (Active, Default)
- OpenAI (Inactive example)
- OpenRouter (Inactive example)

---

## 📖 Usage Guide

### Generating Articles

#### Method 1: Dashboard (Recommended)

1. **Navigate to AI Pipeline Control**
   - Go to `Admin → AI Configuration → AI Pipeline Control`

2. **Configure Generation**
   - Select AI Provider
   - Choose Language (English/Arabic)
   - Set Number of Trends (1-10)
   - Set Target Word Count

3. **Execute Pipeline**
   - Click **Fetch New Trends** to discover trending topics
   - Click **Process Pending Tasks** to generate articles
   - Monitor progress in real-time

4. **Review & Publish**
   - Go to **AI System → AI Tasks** to view generated tasks
   - Click **View Post** to review articles
   - Edit and publish from **Posts** section

#### Method 2: Command Line

```bash
# Full pipeline (fetch trends + generate articles)
php artisan ai:pipeline

# Fetch trends only
php artisan ai:pipeline --trends-only

# Process existing tasks only
php artisan ai:pipeline --process-only

# Custom configuration
php artisan ai:pipeline --trends=3 --words=1000
```

---

## 🏗️ Architecture

### Tech Stack
```
Frontend:
├── Filament v4.4 (Admin Panel)
├── TailwindCSS (Styling)
├── Livewire v3.7 (Reactivity)
└── TinyMCE (Rich Editor)

Backend:
├── Laravel 11.x (Framework)
├── SQLite/MySQL (Database)
├── Service Layer (Business Logic)
└── Factory Pattern (AI Providers)

AI Integration:
├── Google Gemini API
├── OpenAI API Support
├── Extensible Provider System
└── Multi-language Prompting
```

### Service Architecture

```
AIOrchestratorService (Main Controller)
    ├── AIProviderFactory (Provider Selection)
    │   ├── GeminiTrendSearchService
    │   ├── GeminiContentGenerator
    │   ├── OpenAITrendSearchService (Extensible)
    │   └── OpenAIContentGenerator (Extensible)
    └── Task Management
        ├── Create Tasks
        ├── Process Tasks
        └── Retry Failed Tasks
```

---

## 📊 Database Schema

### Core Tables

**`ai_providers`** - AI service configurations
```
- id, name, type, api_key (encrypted)
- model, base_url, settings (json)
- is_active, is_default, priority
```

**`ai_tasks`** - Generation task queue
```
- id, task_type, payload (json)
- status (pending/running/completed/failed)
- result (json), error_message
- scheduled_at, started_at, completed_at
```

**`pipeline_settings`** - Global configurations
```
- key, value, type, group, description
```

**`posts`** - Generated articles
```
- title, slug, content, category_id
- source_url, is_published, published_at
- featured_image, meta_description
```

---

## 🎨 Dashboard Features

### 1. AI Providers Management
- Add/Edit/Delete providers
- Test API connections
- Set default provider
- Configure model parameters

### 2. AI Pipeline Control
- **Provider Selection**: Choose active provider
- **Language Selection**: English or Arabic
- **Trends Configuration**: Set count (1-10)
- **Word Count**: Customize article length
- **Quick Actions**: Fetch Trends, Process Tasks
- **Live Stats**: Real-time task monitoring

### 3. AI Tasks Monitor
- View all generation tasks
- Filter by status/type
- Retry failed tasks
- View generated posts
- Detailed payload/result inspection

### 4. Pipeline Settings
- Default trends count
- Default word count
- Auto-publish toggle
- Featured image settings

---

## 🔧 Customization

### Adding New AI Provider

1. **Create Service Classes**
```php
// app/Services/ClaudeTrendSearchService.php
class ClaudeTrendSearchService implements TrendSearchServiceInterface
{
    public function searchTrends(int $count = 5): array
    {
        // Implementation
    }
}

// app/Services/ClaudeContentGenerator.php
class ClaudeContentGenerator implements ContentGeneratorInterface
{
    public function generateArticle(array $trendData): array
    {
        // Implementation
    }
}
```

2. **Update Factory**
```php
// app/Services/AIProviderFactory.php
public static function makeTrendSearch(AIProvider $provider)
{
    return match($provider->type) {
        'claude' => new ClaudeTrendSearchService($provider),
        // ...
    };
}
```

3. **Add to Dashboard**
- Create provider in **AI Providers**
- Set type to 'claude'
- Configure API key and model

### Customizing Article Prompts

Edit `app/Services/GeminiContentGenerator.php`:
```php
private function buildArticlePrompt(array $trendData): string
{
    // Customize prompt structure
    // Add your requirements
}
```

---

## 📸 Screenshots

### Dashboard Overview
![Dashboard](docs/screenshots/dashboard.png)

### AI Pipeline Control
![Pipeline Control](docs/screenshots/pipeline-control.png)

### Task Management
![Tasks](docs/screenshots/tasks.png)

---

## 🐛 Troubleshooting

### Common Issues

**Issue: "API Key Invalid"**
```bash
# Check your API key in .env or dashboard
# Verify provider is active
# Test connection in AI Providers
```

**Issue: "Tasks Stuck in Pending"**
```bash
# Process tasks manually
php artisan ai:pipeline --process-only

# Or via dashboard
AI Pipeline Control → Process Pending Tasks
```

**Issue: "Slug Duplicate Error"**
- Fixed automatically with unique slug generation
- Articles get numbered suffixes (-1, -2, etc.)

---

## 🚀 Deployment

### Production Checklist

```bash
# Optimize for production
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build

# Set environment
APP_ENV=production
APP_DEBUG=false

# Database
php artisan migrate --force

# Queue worker (recommended)
php artisan queue:work --tries=3
```

### Environment Security
- ✅ Encrypt API keys
- ✅ Use HTTPS
- ✅ Enable rate limiting
- ✅ Regular backups
- ✅ Monitor API usage

---

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- **Laravel Team** - Amazing framework
- **Filament Team** - Beautiful admin panel
- **Google Gemini** - Powerful AI API
- **OpenAI** - AI innovation

---

**Built with ❤️ using Laravel, Filament, and AI**

*Last Updated: January 2026*
