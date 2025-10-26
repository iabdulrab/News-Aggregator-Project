# News Aggregator Backend

A robust Laravel-based news aggregator that fetches articles from multiple news sources (NewsAPI, The Guardian, and The New York Times) and provides a comprehensive RESTful API with advanced features including auto-fetch, user preferences, and Swagger documentation.

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

## 🌟 Features

### Core Functionality
- **Multi-Source Data Aggregation**: Fetches articles from NewsAPI, The Guardian, and The New York Times
- **Auto-Fetch on Demand**: Automatically fetches articles when search returns no results
- **Automated Data Collection**: Scheduled command runs to fetch latest articles
- **Advanced Search & Filtering**: Search by keyword, filter by date range, category, source, and author
- **User Preferences**: Personalized article feeds based on user-selected sources, categories, and authors
- **Authentication**: Secure API authentication using Laravel Sanctum
- **Swagger Documentation**: Interactive API documentation at `/api/documentation`
- **Consistent API Responses**: Standardized response structure with BaseController
- **API Resources**: Clean data transformation layer for all responses

### Technical Highlights
- ✨ **SOLID Principles**: Interface-based design with dependency injection
- 🎯 **Repository Pattern**: Clean separation between data access and business logic
- 🏗️ **Service Layer**: Business logic isolated from controllers
- 🔄 **BaseController**: Consistent response structure across all endpoints
- 💾 **Data Deduplication**: Prevents duplicate articles using URL uniqueness
- 📊 **Database Optimization**: Indexed queries for better performance
- 📝 **Comprehensive Logging**: Error tracking and debugging support
- 🧪 **Best Practices**: DRY, KISS, and clean code principles

## 📋 Table of Contents

- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Database Setup](#-database-setup)
- [Fetching Articles](#-fetching-articles)
- [API Documentation](#-api-documentation)
- [Architecture](#-architecture)
- [Auto-Fetch Feature](#-auto-fetch-feature)
- [Testing](#-testing)
- [Troubleshooting](#-troubleshooting)

## 🔧 Requirements

- PHP >= 8.1
- Composer
- MySQL 5.7+ or PostgreSQL 10+
- API Keys from:
  - [NewsAPI](https://newsapi.org/) - Free tier available
  - [The Guardian](https://open-platform.theguardian.com/) - Free tier available
  - [New York Times](https://developer.nytimes.com/) - Free tier available

## 📦 Installation

### 1. Clone & Install Dependencies

```bash
# Clone the repository
git clone <repository-url>
cd news-aggregator

# Install PHP dependencies
composer install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Environment Variables

Edit `.env` file with your settings:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_aggregator
DB_USERNAME=root
DB_PASSWORD=your_password

# News API Configuration
NEWSAPI_BASE_URL=https://newsapi.org/v2
NEWSAPI_KEY=your_newsapi_key_here

# Guardian API Configuration
GUARDIAN_BASE_URL=https://content.guardianapis.com
GUARDIAN_API_KEY=your_guardian_key_here

# NY Times API Configuration
NYT_BASE_URL=https://api.nytimes.com/svc/search/v2
NYT_API_KEY=your_nyt_key_here

# Application
APP_NAME="News Aggregator"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Swagger Configuration
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

## 🔑 Getting API Keys

### NewsAPI
1. Visit https://newsapi.org/register
2. Sign up for free account (500 requests/day)
3. Copy your API key
4. Add to `.env` as `NEWSAPI_KEY`

### The Guardian
1. Visit https://open-platform.theguardian.com/access/
2. Register for a developer key (free, unlimited)
3. Copy your API key
4. Add to `.env` as `GUARDIAN_API_KEY`

### New York Times
1. Visit https://developer.nytimes.com/get-started
2. Create an account and app
3. Enable "Article Search API"
4. Copy your API key (4,000 requests/day free)
5. Add to `.env` as `NYT_API_KEY`

## 🗄️ Database Setup

### Create Database

```sql
CREATE DATABASE news_aggregator CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Run Migrations

```bash
php artisan migrate
```

This creates the following tables:
- `users` - User accounts
- `sources` - News sources (NewsAPI, Guardian, NYTimes)
- `articles` - Fetched articles
- `user_preferences` - User preferences
- `personal_access_tokens` - API authentication tokens

### Seed Database (Optional)

```bash
php artisan db:seed
```

This creates:
- News sources (Guardian, NYTimes, NewsAPI)
- Sample test user (optional)

## 📰 Fetching Articles

### Manual Fetch

```bash
# Fetch from all sources
php artisan news:fetch

# Fetch from specific source
php artisan news:fetch --sources=guardian
php artisan news:fetch --sources=newsapi,guardian

# Fetch with date range
php artisan news:fetch --from=2025-10-01 --to=2025-10-26

# Fetch with search term
php artisan news:fetch --q=technology

# Fetch with category (NewsAPI only)
php artisan news:fetch --sources=newsapi --category=technology
```

### Automated Fetching

The command is scheduled to run hourly. To enable:

```bash
# Add to crontab (Linux/Mac)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# Or run manually
php artisan schedule:work
```

## 📚 API Documentation

### Starting the Server

```bash
php artisan serve
```

Server will start at: http://localhost:8000

### Swagger UI

Access interactive API documentation:
```
http://localhost:8000/api/documentation
```

### API Endpoints

#### **Authentication**
```http
POST   /api/auth/register     # Register new user
POST   /api/auth/login        # Login user
POST   /api/auth/logout       # Logout user (authenticated)
GET    /api/auth/user         # Get current user (authenticated)
```

#### **Articles**
```http
GET    /api/articles                    # List all articles (with filters)
GET    /api/articles/{id}               # Get single article
GET    /api/articles/personalized/feed  # Personalized feed (authenticated)
GET    /api/articles/meta/categories    # Get all categories
GET    /api/articles/meta/authors       # Get all authors
```

#### **Sources**
```http
GET    /api/sources          # List all sources
GET    /api/sources/{id}     # Get single source
```

#### **User Preferences**
```http
GET    /api/preferences      # Get user preferences (authenticated)
PUT    /api/preferences      # Update preferences (authenticated)
DELETE /api/preferences      # Reset preferences (authenticated)
```

### Query Parameters

#### Articles Endpoint (`/api/articles`)

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `q` | string | Search keyword | `?q=cryptocurrency` |
| `from` | date | Start date (YYYY-MM-DD) | `?from=2025-10-01` |
| `to` | date | End date (YYYY-MM-DD) | `?to=2025-10-26` |
| `category` | string | Filter by category | `?category=Technology` |
| `source` | string | Filter by source(s) | `?source=guardian` or `?source=guardian,nytimes` |
| `author` | string | Filter by author | `?author=Jane Smith` |
| `per_page` | integer | Items per page (max 100) | `?per_page=20` |
| `sort` | string | Sort order (asc/desc) | `?sort=asc` |

### Response Format

All API responses follow this consistent structure:

#### Success Response
```json
{
    "success": true,
    "message": "Articles retrieved successfully",
    "data": [ /* array of articles */ ]
}
```

#### Paginated Response
```json
{
    "success": true,
    "message": "Articles retrieved successfully",
    "data": [ /* array of items */ ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 191,
        "last_page": 13,
        "from": 1,
        "to": 15,
        "links": {
            "first": "http://localhost:8000/api/articles?page=1",
            "last": "http://localhost:8000/api/articles?page=13",
            "prev": null,
            "next": "http://localhost:8000/api/articles?page=2"
        }
    }
}
```

#### Error Response
```json
{
    "success": false,
    "message": "Article not found"
}
```

#### Validation Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

## 🏗️ Architecture

### Design Patterns

#### 1. **Repository Pattern**
```
Controller → Service → Repository → Model → Database
```

**Benefits:**
- Clean separation of concerns
- Easy to test and mock
- Flexible data access layer

#### 2. **Service Layer Pattern**
```php
// Example: ArticleService
class ArticleService {
    public function getArticlesWithAutoFetch(array $filters) {
        // Business logic here
        $articles = $this->articleRepository->getAllWithFilters($filters);
        
        // Auto-fetch if needed
        if (empty($articles) && isset($filters['q'])) {
            $this->autoFetch($filters);
            $articles = $this->articleRepository->getAllWithFilters($filters);
        }
        
        return $articles;
    }
}
```

#### 3. **Interface Segregation**
```php
interface NewsFetcherInterface {
    public function fetchArticles(array $params = []): array;
    public function getSourceKey(): string;
    public function transformArticle(array $article): array;
}
```

All news fetchers implement this interface ensuring consistency.

#### 4. **Dependency Injection**
```php
public function __construct(
    ArticleRepositoryInterface $articleRepository,
    NewsAggregatorService $aggregatorService
) {
    $this->articleRepository = $articleRepository;
    $this->aggregatorService = $aggregatorService;
}
```

### Project Structure

```
app/
├── Console/Commands/          # Artisan commands
│   └── FetchNewsArticles.php
├── Http/
│   ├── Controllers/Api/       # API controllers
│   │   ├── BaseController.php
│   │   ├── ArticleController.php
│   │   ├── AuthController.php
│   │   ├── SourceController.php
│   │   └── UserPreferenceController.php
│   └── Resources/             # API Resources
│       ├── UserResource.php
│       ├── ArticleResource.php
│       ├── SourceResource.php
│       └── UserPreferenceResource.php
├── Interfaces/                # Contracts
│   ├── NewsFetcherInterface.php
│   ├── UserRepositoryInterface.php
│   ├── ArticleRepositoryInterface.php
│   ├── SourceRepositoryInterface.php
│   └── UserPreferenceRepositoryInterface.php
├── Models/                    # Eloquent models
│   ├── Article.php
│   ├── Source.php
│   ├── User.php
│   └── UserPreference.php
├── Repositories/              # Data access layer
│   ├── UserRepository.php
│   ├── ArticleRepository.php
│   ├── SourceRepository.php
│   └── UserPreferenceRepository.php
└── Services/                  # Business logic
    ├── Auth/
    │   └── AuthService.php
    ├── Article/
    │   └── ArticleService.php
    ├── Source/
    │   └── SourceService.php
    ├── UserPreference/
    │   └── UserPreferenceService.php
    ├── Guardian/
    │   └── GuardianAPIFetcher.php
    ├── NewsAPI/
    │   └── NewsAPIFetcher.php
    ├── NYTimes/
    │   └── NYTimesFetcher.php
    └── NewsAggregatorService.php
```

## 🚀 Auto-Fetch Feature

### Overview
The Auto-Fetch feature automatically fetches articles from news sources when a search query returns no results from the database.

### How It Works

1. User searches for a keyword (e.g., "cryptocurrency")
2. System checks database for matching articles
3. **If no results found:**
   - Automatically fetches articles from all 3 news sources
   - Stores fetched articles in database
   - Returns newly fetched articles to user
4. **If results found:** Returns existing articles

### Usage Example

```bash
# First search (database empty)
curl "http://localhost:8000/api/articles?q=cryptocurrency"
# Returns: Articles fetched from news sources
# Header: X-Auto-Fetch: true

# Second search (articles now in database)
curl "http://localhost:8000/api/articles?q=cryptocurrency"
# Returns: Cached articles from database
# Header: (no X-Auto-Fetch header)
```

### Response Headers

When auto-fetch is triggered, the response includes:
```
X-Auto-Fetch: true
```

### Features

- ✅ **Smart Triggering**: Only activates when search returns 0 results
- ✅ **Source Filtering**: Respects source filters (`?source=guardian,nytimes`)
- ✅ **Date Filtering**: Honors date range filters (`?from=2025-10-01&to=2025-10-26`)
- ✅ **Performance**: Caches results for future searches
- ✅ **Transparency**: Logs all operations and adds response header
- ✅ **Error Handling**: Gracefully handles failed fetches

### Example Requests

```bash
# Auto-fetch with source filter
curl "http://localhost:8000/api/articles?q=bitcoin&source=newsapi,guardian"

# Auto-fetch with date range
curl "http://localhost:8000/api/articles?q=climate&from=2025-10-01&to=2025-10-26"

# Auto-fetch with all filters
curl "http://localhost:8000/api/articles?q=technology&source=guardian&from=2025-10-20"
```

## 🧪 Testing

### Test API Endpoints

```bash
# Test sources endpoint
curl http://localhost:8000/api/sources

# Test articles endpoint
curl "http://localhost:8000/api/articles?per_page=5"

# Test single article
curl http://localhost:8000/api/articles/1

# Test categories
curl http://localhost:8000/api/articles/meta/categories

# Test authors
curl http://localhost:8000/api/articles/meta/authors

# Test search with auto-fetch
curl "http://localhost:8000/api/articles?q=cryptocurrency"

# Register user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

## 🐛 Troubleshooting

### Common Issues

#### 1. "NEWSAPI_KEY is not configured"
**Solution:** Add your API key to `.env` file:
```env
NEWSAPI_KEY=your_actual_key_here
```

#### 2. cURL SSL Certificate Error (Local Development)
**Solution:** Already handled - SSL verification is disabled in local environments.

#### 3. "Required parameters are missing" (NewsAPI)
**Solution:** NewsAPI's `/everything` endpoint requires at least one parameter. A default broad search is automatically applied.

#### 4. "The requested resource could not be found" (Guardian)
**Solution:** Remove the `q=*` parameter. Guardian API works without search parameter for latest articles.

#### 5. Database Connection Error
**Solution:** 
- Verify database credentials in `.env`
- Ensure MySQL/PostgreSQL is running
- Check if database exists: `CREATE DATABASE news_aggregator;`

#### 6. Article Images Too Long Error
**Solution:** Already fixed with migration `2025_10_25_110453_increase_url_to_image_column_length.php`

### Debug Mode

Enable detailed error messages:
```env
APP_DEBUG=true
APP_ENV=local
```

View logs:
```bash
tail -f storage/logs/laravel.log
```

### Performance Optimization

#### Enable Query Caching
```php
// In config/cache.php
'default' => env('CACHE_DRIVER', 'redis'),
```

#### Database Indexing
Already optimized with indexes on:
- `articles.published_at`
- `articles.category`
- `articles.source_id`
- `articles.url` (unique)

## 📖 Additional Features

### BaseController

All controllers extend `BaseController` which provides:
- `successResponse()` - Success with data
- `successMessage()` - Success without data
- `paginatedResponse()` - Paginated data
- `errorResponse()` - Generic errors
- `notFoundResponse()` - 404 errors
- `validationErrorResponse()` - 422 validation errors
- `unauthorizedResponse()` - 401 authentication errors
- `createdResponse()` - 201 created response

### API Resources

Transform models into clean JSON responses:
- `UserResource` - User data transformation
- `ArticleResource` - Article data transformation
- `SourceResource` - Source data transformation
- `UserPreferenceResource` - Preferences transformation

### Logging

All operations are logged:
- API fetch success/failures
- Auto-fetch triggers
- Article storage operations
- Authentication attempts

Access logs:
```bash
tail -f storage/logs/laravel.log
```

## 🔐 Security

- **API Authentication**: Laravel Sanctum token-based authentication
- **Password Hashing**: Bcrypt with proper salting
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **CORS Configuration**: Configurable in `config/cors.php`
- **Rate Limiting**: Can be configured per route
- **Environment Variables**: Sensitive data in `.env` file

## 🚀 Production Deployment

### Before Deployment

1. **Optimize Application:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

2. **Set Production Environment:**
```env
APP_ENV=production
APP_DEBUG=false
```

3. **Enable SSL Verification:**
SSL verification is automatically enabled in production environments.

4. **Configure Queue Worker:**
```bash
php artisan queue:work --daemon
```

5. **Setup Cron Job:**
```bash
* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📞 Support

For issues and questions:
- Check the [Troubleshooting](#-troubleshooting) section
- Review logs in `storage/logs/laravel.log`
- Check Swagger documentation at `/api/documentation`

## ✨ Acknowledgments

- [Laravel](https://laravel.com) - The PHP framework
- [NewsAPI](https://newsapi.org) - News aggregation service
- [The Guardian](https://open-platform.theguardian.com) - Guardian API
- [New York Times](https://developer.nytimes.com) - NYT API
- [Swagger/OpenAPI](https://swagger.io) - API documentation

---

**Built with ❤️ using Laravel**
