# 📝 My Tech Blog

A modern, feature-rich blog management system built with Laravel 11 and Filament 4.

## ✨ Features

### 📚 Content Management
- **Posts Management** - Full CRUD operations for blog posts
- **Categories System** - Hierarchical categories with nested support
- **Rich Text Editor** - Advanced content editing with file attachments
- **Image Gallery** - Upload up to 10 images per post with drag-and-drop reordering
- **Featured Images** - Image editor with aspect ratio control and auto-resize
- **Draft System** - Save posts as drafts before publishing
- **Soft Deletes** - Restore deleted posts and categories

### 🎨 Advanced Features
- **Global Search** - Quick search across posts and categories (press `/`)
- **Statistics Dashboard** - Real-time stats widgets showing:
  - Total posts count
  - Published vs draft posts
  - Active categories count
- **Posts Chart** - Visual representation of posts published over time
- **Color-Coded Categories** - Visual differentiation with custom colors
- **Nested Categories** - Unlimited parent-child category relationships

### 🖼️ Image Management
- **Image Editor** - Crop, resize, and adjust images
- **Aspect Ratios** - Support for 16:9, 4:3, and 1:1
- **Auto-Resize** - Automatic resize to 1920x1080px
- **Size Limits** - Maximum 2MB per image
- **Gallery Support** - Multiple images with reordering

### 🔍 User Experience
- **Inline Category Creation** - Create categories while writing posts
- **Auto-Slug Generation** - Automatic URL-friendly slugs
- **Searchable Dropdowns** - Easy category selection
- **Responsive Tables** - Sortable and filterable data tables
- **Toggleable Columns** - Show/hide table columns as needed

## 🛠️ Tech Stack

- **Framework:** Laravel 11.x
- **Admin Panel:** Filament 4.x
- **Database:** SQLite (easily switchable to MySQL/PostgreSQL)
- **PHP:** 8.2+
- **Frontend:** Livewire 3.x (via Filament)

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM (for asset compilation)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd my-tech-blog
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Create admin user**
   ```bash
   php artisan make:filament-user
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

8. **Access admin panel**
   ```
   http://localhost:8000/admin
   ```

## 📊 Database Schema

### Posts Table
- `id` - Primary key
- `category_id` - Foreign key to categories
- `title` - Post title
- `slug` - URL-friendly identifier
- `content` - Post content (HTML)
- `featured_image` - Main post image
- `gallery` - JSON array of gallery images
- `is_published` - Publication status
- `published_at` - Publication timestamp
- `created_at`, `updated_at`, `deleted_at`

### Categories Table
- `id` - Primary key
- `parent_id` - Self-referencing for nested categories
- `name` - Category name
- `slug` - URL-friendly identifier
- `description` - Category description
- `color` - Hex color for UI
- `icon` - Icon identifier
- `order` - Sort order
- `is_active` - Active status
- `created_at`, `updated_at`, `deleted_at`

## 🎯 Usage

### Creating a Post
1. Navigate to **Posts** → **Create**
2. Enter title (slug auto-generates)
3. Select or create a category
4. Write content using the rich editor
5. Upload featured image and gallery images
6. Toggle "Published" when ready
7. Click **Save**

### Managing Categories
1. Navigate to **Categories**
2. Create categories with custom colors
3. Set parent categories for nesting
4. Reorder using the order field
5. View posts count per category

### Dashboard Widgets
- **Blog Stats** - Overview of posts and categories
- **Posts Chart** - Visual timeline of publications

## 🔧 Configuration

### Image Settings
Edit `PostForm.php` to customize:
- Maximum file size (default: 2MB)
- Target dimensions (default: 1920x1080)
- Aspect ratios
- Gallery limit (default: 10 images)

### Widget Settings
Widgets are located in `app/Filament/Widgets/`:
- `BlogStatsWidget.php` - Statistics cards
- `PostsChartWidget.php` - Posts timeline chart

## 📝 Development

### File Structure
```
app/
├── Filament/
│   ├── Resources/
│   │   ├── Posts/
│   │   │   ├── PostResource.php
│   │   │   ├── Schemas/
│   │   │   │   ├── PostForm.php
│   │   │   │   └── PostInfolist.php
│   │   │   └── Tables/
│   │   │       └── PostsTable.php
│   │   └── Categories/
│   │       ├── CategoryResource.php
│   │       └── ...
│   └── Widgets/
│       ├── BlogStatsWidget.php
│       └── PostsChartWidget.php
└── Models/
    ├── Post.php
    └── Category.php
```

### Adding New Features
1. Create model: `php artisan make:model ModelName -m`
2. Create resource: `php artisan make:filament-resource ModelName --generate`
3. Customize forms, tables, and infolists
4. Run migrations: `php artisan migrate`

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper database (MySQL/PostgreSQL)
- [ ] Run `php artisan optimize`
- [ ] Run `npm run build`
- [ ] Set up proper file permissions
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up SSL certificate
- [ ] Configure backup strategy

## 🤝 Contributing

This is a personal project, but suggestions and feedback are welcome!

## 📄 License

This project is open-source and available under the MIT License.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Filament](https://filamentphp.com) - Admin Panel Framework
- [Livewire](https://livewire.laravel.com) - Dynamic Interfaces

## 📧 Contact

For questions or support, please open an issue in the repository.

---

**Built with ❤️ by Ahmed Maher using Laravel & Filament**
