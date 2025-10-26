# News Aggregator Backend

A robust Laravel-based news aggregator that fetches articles from multiple news sources (NewsAPI, The Guardian, and The New York Times) and provides a comprehensive RESTful API for accessing and filtering articles.

## Features

### Core Functionality
- 🔄 **Multi-Source Data Aggregation**: Fetches articles from NewsAPI, The Guardian, and The New York Times
- 📊 **Automated Data Collection**: Scheduled command runs hourly to fetch latest articles
- 🔍 **Advanced Search & Filtering**: Search by keyword, filter by date range, category, source, and author
- 👤 **User Preferences**: Personalized article feeds based on user-selected sources, categories, and authors
- 🔐 **Authentication**: Secure API authentication using Laravel Sanctum
- 📱 **RESTful API**: Clean, well-documented API endpoints

### Technical Highlights
- ✨ **SOLID Principles**: Clean architecture with interface-based design
- 🎯 **DRY & KISS**: Reusable services and straightforward implementation
- 💉 **Dependency Injection**: Proper service container bindings
- 🗄️ **Database Optimization**: Indexed queries for better performance
- 🔄 **Data Deduplication**: Prevents duplicate articles using URL uniqueness
- 📝 **Comprehensive Logging**: Error tracking and debugging support

## Architecture

### Design Patterns

1. **Interface Segregation**: `NewsFetcherInterface` ensures consistent implementation across different news sources
2. **Service Layer**: Business logic separated from controllers
3. **Repository Pattern**: Models handle data access
4. **Dependency Injection**: Services registered in service provider
5. **Command Pattern**: Artisan command for fetching articles

### Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── FetchNewsArticles.php      # Artisan command for fetching articles
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── ArticleController.php   # Article endpoints
│           ├── AuthController.php      # Authentication
│           ├── SourceController.php    # News sources
│           └── UserPreferenceController.php  # User preferences
├── Interfaces/
│   └── NewsFetcherInterface.php       # Contract for news fetchers
├── Models/
│   ├── Article.php                    # Article model
│   ├── Source.php                     # News source model
│   ├── User.php                       # User model
│   └── UserPreference.php             # User preferences model
└── Services/
    ├── Guardian/
    │   └── GuardianAPIFetcher.php     # Guardian API implementation
    ├── NewsAPI/
    │   └── NewsAPIFetcher.php         # NewsAPI implementation
    ├── NYTimes/
    │   └── NYTimesFetcher.php         # NY Times API implementation
    └── NewsAggregatorService.php      # Main aggregator service
```

## Installation & Setup

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL or PostgreSQL
- API Keys for:
  - [NewsAPI](https://newsapi.org/)
  - [The Guardian](https://open-platform.theguardian.com/)
  - [New York Times](https://developer.nytimes.com/)

### Step 1: Install Dependencies

```bash
composer install
```

### Step 2: Environment Configuration

Create `.env` file from example:
```bash
cp .env.example .env
```

Update the following in your `.env`:

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

# The Guardian API Configuration
GUARDIAN_BASE_URL=https://content.guardianapis.com
GUARDIAN_API_KEY=your_guardian_api_key_here

# New York Times API Configuration
NYT_BASE_URL=https://api.nytimes.com/svc/search/v2
NYT_API_KEY=your_nyt_api_key_here
```

### Step 3: Generate Application Key

```bash
php artisan key:generate
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Seed Database

Seed the news sources:
```bash
php artisan db:seed --class=SourceSeeder
```

Or seed everything:
```bash
php artisan db:seed
```

### Step 6: Fetch Initial Articles

Fetch articles from all sources:
```bash
php artisan news:fetch
```

Fetch from specific sources:
```bash
php artisan news:fetch --sources=newsapi,guardian
```

Fetch with date range and search:
```bash
php artisan news:fetch --from=2025-10-01 --to=2025-10-25 --q=technology
```

### Step 7: Start the Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`

### Step 8: Setup Scheduled Tasks (Production)

For automated article fetching, add to your cron:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or run the scheduler manually:
```bash
php artisan schedule:work
```

## API Endpoints

See [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) for complete API documentation.

### Quick Reference

**Public Endpoints:**
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login
- `GET /api/articles` - Get articles with filtering
- `GET /api/articles/{id}` - Get single article
- `GET /api/sources` - Get all news sources

**Protected Endpoints (Require Authentication):**
- `GET /api/auth/user` - Get current user
- `POST /api/auth/logout` - Logout
- `GET /api/preferences` - Get user preferences
- `PUT /api/preferences` - Update preferences
- `GET /api/articles/personalized/feed` - Get personalized articles

## Usage Examples

### 1. Register and Login

```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### 2. Search Articles

```bash
# Search by keyword
curl http://localhost:8000/api/articles?q=technology

# Filter by date range and source
curl "http://localhost:8000/api/articles?from=2025-10-01&to=2025-10-25&source=newsapi,guardian"

# Filter by category
curl http://localhost:8000/api/articles?category=Business
```

### 3. Set User Preferences

```bash
curl -X PUT http://localhost:8000/api/preferences \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "preferences": {
      "sources": ["newsapi", "guardian"],
      "categories": ["Technology", "Science"],
      "authors": ["Jane Smith"]
    }
  }'
```

### 4. Get Personalized Feed

```bash
curl http://localhost:8000/api/articles/personalized/feed \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Command Reference

### Fetch News Articles

```bash
# Fetch from all sources
php artisan news:fetch

# Fetch from specific sources
php artisan news:fetch --sources=newsapi,guardian,nytimes

# Fetch with date range
php artisan news:fetch --from=2025-10-01 --to=2025-10-25

# Fetch with search query
php artisan news:fetch --q="artificial intelligence"

# Fetch with category (for NewsAPI)
php artisan news:fetch --category=technology
```

## Database Schema

### Tables

**sources**
- `id` - Primary key
- `key` - Unique source identifier (newsapi, guardian, nytimes)
- `name` - Display name
- `base_url` - API base URL
- `meta` - JSON metadata

**articles**
- `id` - Primary key
- `source_id` - Foreign key to sources
- `source_article_id` - Original article ID from source
- `title` - Article title
- `description` - Short description
- `content` - Full content
- `url` - Unique article URL
- `url_to_image` - Image URL
- `published_at` - Publication date
- `author_name` - Author name
- `category` - Article category
- `raw` - JSON raw data from source
- `is_active` - Active status

**users**
- `id` - Primary key
- `name` - User name
- `email` - Unique email
- `password` - Hashed password

**user_preferences**
- `id` - Primary key
- `user_id` - Foreign key to users
- `preferences` - JSON preferences object

## SOLID Principles Implementation

### Single Responsibility Principle (SRP)
- Each news fetcher class handles only one source
- Controllers handle only HTTP concerns
- Services handle business logic
- Models handle data access

### Open/Closed Principle (OCP)
- Easy to add new news sources by implementing `NewsFetcherInterface`
- No need to modify existing code when adding sources

### Liskov Substitution Principle (LSP)
- All news fetchers can be used interchangeably through the interface
- NewsAggregatorService works with any implementation of NewsFetcherInterface

### Interface Segregation Principle (ISP)
- NewsFetcherInterface is minimal and focused
- Clients depend only on methods they use

### Dependency Inversion Principle (DIP)
- High-level NewsAggregatorService depends on NewsFetcherInterface abstraction
- Concrete implementations injected via service container

## Testing

Run tests:
```bash
php artisan test
```

## Performance Considerations

1. **Database Indexing**: Indexed columns for faster queries (published_at, category, source_id)
2. **Pagination**: All list endpoints return paginated results
3. **Command Scheduling**: `withoutOverlapping()` prevents concurrent fetches
4. **Deduplication**: URL uniqueness constraint prevents duplicate articles
5. **Eager Loading**: Relations loaded efficiently to prevent N+1 queries

## Troubleshooting

### Articles Not Fetching
- Verify API keys in `.env`
- Check API rate limits
- Review `storage/logs/laravel.log` for errors

### Database Connection Errors
- Verify database credentials in `.env`
- Ensure database exists
- Check database server is running

### Authentication Issues
- Ensure `php artisan key:generate` was run
- Check Sanctum is properly configured
- Verify token is included in Authorization header

## Future Enhancements

- [ ] Add more news sources
- [ ] Implement caching layer
- [ ] Add article bookmarking
- [ ] Add email notifications for personalized feed
- [ ] Add admin dashboard
- [ ] Implement full-text search with Elasticsearch
- [ ] Add article sentiment analysis
- [ ] Add API rate limiting per user

## Contributing

1. Follow PSR-12 coding standards
2. Write tests for new features
3. Update documentation
4. Follow SOLID principles

## License

This project is open-sourced software licensed under the MIT license.

## API Keys

Get your free API keys:
- **NewsAPI**: https://newsapi.org/register
- **The Guardian**: https://open-platform.theguardian.com/access/
- **New York Times**: https://developer.nytimes.com/get-started

## Support

For issues, questions, or contributions, please refer to the project repository.

