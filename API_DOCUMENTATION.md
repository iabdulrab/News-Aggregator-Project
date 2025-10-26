# News Aggregator API Documentation

## Overview

The News Aggregator API provides endpoints to retrieve articles from multiple news sources (NewsAPI, The Guardian, and The New York Times), with advanced filtering, searching, and personalization capabilities.

## Base URL

```
http://localhost:8000/api
```

## Authentication

The API uses Laravel Sanctum for authentication. Protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {your_token}
```

---

## Public Endpoints

### Authentication

#### Register a New User

**POST** `/auth/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "1|xxxxxxxxxxx",
  "token_type": "Bearer"
}
```

#### Login

**POST** `/auth/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "2|xxxxxxxxxxx",
  "token_type": "Bearer"
}
```

---

### Articles

#### Get All Articles

**GET** `/articles`

Retrieve articles with optional filtering and search.

**Query Parameters:**
- `q` (string): Search keyword (searches title, description, content)
- `from` (date): Start date (Y-m-d format)
- `to` (date): End date (Y-m-d format)
- `category` (string): Filter by category
- `source` (string): Filter by source key (newsapi, guardian, nytimes) - comma-separated for multiple
- `author` (string): Filter by author name
- `sort` (string): Sort order (asc/desc, default: desc)
- `per_page` (int): Results per page (max 100, default: 15)
- `page` (int): Page number

**Example Request:**
```
GET /articles?q=technology&source=newsapi,guardian&from=2025-10-01&per_page=20
```

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "source_id": 1,
      "title": "Breaking News Title",
      "description": "Article description",
      "content": "Full article content...",
      "url": "https://example.com/article",
      "url_to_image": "https://example.com/image.jpg",
      "published_at": "2025-10-25 10:30:00",
      "author_name": "Jane Smith",
      "category": "Technology",
      "created_at": "2025-10-25T10:35:00.000000Z",
      "updated_at": "2025-10-25T10:35:00.000000Z",
      "source": {
        "id": 1,
        "key": "newsapi",
        "name": "NewsAPI",
        "base_url": "https://newsapi.org/v2"
      }
    }
  ],
  "first_page_url": "http://localhost:8000/api/articles?page=1",
  "from": 1,
  "last_page": 5,
  "last_page_url": "http://localhost:8000/api/articles?page=5",
  "links": [...],
  "next_page_url": "http://localhost:8000/api/articles?page=2",
  "path": "http://localhost:8000/api/articles",
  "per_page": 15,
  "prev_page_url": null,
  "to": 15,
  "total": 75
}
```

#### Get Single Article

**GET** `/articles/{id}`

**Response (200):**
```json
{
  "id": 1,
  "source_id": 1,
  "title": "Breaking News Title",
  "description": "Article description",
  "content": "Full article content...",
  "url": "https://example.com/article",
  "url_to_image": "https://example.com/image.jpg",
  "published_at": "2025-10-25 10:30:00",
  "author_name": "Jane Smith",
  "category": "Technology",
  "source": {
    "id": 1,
    "key": "newsapi",
    "name": "NewsAPI"
  }
}
```

#### Get Available Categories

**GET** `/articles/meta/categories`

**Response (200):**
```json
[
  "Technology",
  "Business",
  "Politics",
  "Sports",
  "Entertainment"
]
```

#### Get Available Authors

**GET** `/articles/meta/authors`

**Response (200):**
```json
[
  "Jane Smith",
  "John Doe",
  "Alice Johnson"
]
```

---

### Sources

#### Get All Sources

**GET** `/sources`

**Response (200):**
```json
[
  {
    "id": 1,
    "key": "newsapi",
    "name": "NewsAPI",
    "base_url": "https://newsapi.org/v2",
    "meta": {
      "description": "NewsAPI aggregates headlines from over 80,000 worldwide sources",
      "website": "https://newsapi.org"
    },
    "articles_count": 1250
  },
  {
    "id": 2,
    "key": "guardian",
    "name": "The Guardian",
    "base_url": "https://content.guardianapis.com",
    "meta": {
      "description": "News and opinions from The Guardian",
      "website": "https://www.theguardian.com"
    },
    "articles_count": 856
  }
]
```

#### Get Single Source

**GET** `/sources/{id}`

**Response (200):**
```json
{
  "id": 1,
  "key": "newsapi",
  "name": "NewsAPI",
  "base_url": "https://newsapi.org/v2",
  "articles_count": 1250
}
```

---

## Protected Endpoints (Require Authentication)

### Authentication

#### Logout

**POST** `/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

#### Get Current User

**GET** `/auth/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "email_verified_at": null,
  "created_at": "2025-10-25T10:00:00.000000Z",
  "updated_at": "2025-10-25T10:00:00.000000Z"
}
```

---

### User Preferences

#### Get User Preferences

**GET** `/preferences`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "preferences": {
    "sources": ["newsapi", "guardian"],
    "categories": ["Technology", "Business"],
    "authors": ["Jane Smith", "John Doe"]
  },
  "created_at": "2025-10-25T10:00:00.000000Z",
  "updated_at": "2025-10-25T10:00:00.000000Z"
}
```

#### Update User Preferences

**PUT** `/preferences`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "preferences": {
    "sources": ["newsapi", "guardian", "nytimes"],
    "categories": ["Technology", "Science"],
    "authors": ["Jane Smith"]
  }
}
```

**Response (200):**
```json
{
  "message": "Preferences updated successfully",
  "preference": {
    "id": 1,
    "user_id": 1,
    "preferences": {
      "sources": ["newsapi", "guardian", "nytimes"],
      "categories": ["Technology", "Science"],
      "authors": ["Jane Smith"]
    }
  }
}
```

#### Reset User Preferences

**DELETE** `/preferences`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Preferences reset successfully"
}
```

---

### Personalized Feed

#### Get Personalized Articles

**GET** `/articles/personalized/feed`

Returns articles filtered by user's saved preferences (sources, categories, authors).

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page` (int): Results per page (max 100, default: 15)
- `page` (int): Page number

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Personalized Article Title",
      "description": "Article matching your preferences",
      "source": {
        "key": "newsapi",
        "name": "NewsAPI"
      }
    }
  ],
  "total": 50
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "message": "Invalid request parameters"
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

### 422 Validation Error
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 500 Internal Server Error
```json
{
  "message": "Server error occurred"
}
```

---

## Rate Limiting

API endpoints are rate-limited to prevent abuse. Default limits:
- Public endpoints: 60 requests per minute
- Authenticated endpoints: 100 requests per minute

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

---

## Best Practices

1. **Use Pagination**: Always use pagination for large result sets
2. **Filter Wisely**: Combine filters to get more relevant results
3. **Cache Responses**: Cache article lists when appropriate
4. **Handle Errors**: Implement proper error handling
5. **Store Tokens Securely**: Never expose API tokens in frontend code

---

## Example Usage

### JavaScript (Fetch API)

```javascript
// Login
const login = async () => {
  const response = await fetch('http://localhost:8000/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      email: 'john@example.com',
      password: 'password123'
    })
  });
  const data = await response.json();
  return data.token;
};

// Get articles with filters
const getArticles = async (token) => {
  const response = await fetch('http://localhost:8000/api/articles?q=technology&per_page=20', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  return await response.json();
};

// Update preferences
const updatePreferences = async (token) => {
  const response = await fetch('http://localhost:8000/api/preferences', {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      preferences: {
        sources: ['newsapi', 'guardian'],
        categories: ['Technology'],
        authors: []
      }
    })
  });
  return await response.json();
};
```

### cURL Examples

```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'

# Get articles with search
curl http://localhost:8000/api/articles?q=technology&source=newsapi

# Get personalized feed (authenticated)
curl http://localhost:8000/api/articles/personalized/feed \
  -H "Authorization: Bearer {your_token}"
```

---

## Support

For issues or questions, please contact the development team or refer to the project repository.

