# Swagger API Documentation Guide

## 🎉 Swagger/OpenAPI Documentation Successfully Implemented!

Your News Aggregator API now has complete Swagger/OpenAPI documentation with interactive testing capabilities.

---

## 📍 Access URLs

### **Swagger UI (Interactive Documentation)**
```
http://localhost:8000/api/documentation
```

### **OpenAPI JSON Specification**
```
http://localhost:8000/docs/api-docs.json
```

---

## 🚀 Quick Start

### 1. Start Your Server
```bash
cd c:\wamp64\www\news-aggregator
php artisan serve
```

### 2. Open Swagger UI
Open your browser and navigate to:
```
http://localhost:8000/api/documentation
```

### 3. Explore the API
- Browse all available endpoints
- View request/response examples
- Test APIs directly from the browser
- See authentication requirements

---

## 📚 Features

### ✅ **Complete API Documentation**
- All 16 endpoints documented
- Request/response schemas
- Parameter descriptions
- Authentication requirements
- Example values

### ✅ **Interactive Testing**
- Test APIs directly from the browser
- No need for Postman or cURL
- Real-time responses
- Authentication support

### ✅ **Organized by Tags**
- **Authentication** - Login, register, logout endpoints
- **Articles** - Article search, filtering, auto-fetch feature
- **Sources** - News source management
- **User Preferences** - Personalization settings

---

## 🔐 Testing Protected Endpoints

### Step 1: Register or Login

1. Click on **Authentication** section
2. Click on **POST /api/auth/login**
3. Click **"Try it out"**
4. Enter credentials:
   ```json
   {
     "email": "john@example.com",
     "password": "password123"
   }
   ```
5. Click **"Execute"**
6. Copy the `token` from the response

### Step 2: Authorize

1. Click the **"Authorize"** button (🔒 icon at the top)
2. Enter: `Bearer YOUR_TOKEN_HERE`
   Example: `Bearer 1|xxxxxxxxxxxx`
3. Click **"Authorize"**
4. Click **"Close"**

### Step 3: Test Protected Endpoints

Now you can test any protected endpoint (marked with 🔒):
- GET /api/auth/user
- GET /api/preferences
- PUT /api/preferences
- GET /api/articles/personalized/feed

---

## 📖 API Endpoints Overview

### **Public Endpoints** (No Auth Required)

#### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user

#### Articles
- `GET /api/articles` - Get articles with filtering
  - **Features**: Auto-fetch, search, date range, category, source, author filters
- `GET /api/articles/{id}` - Get single article
- `GET /api/articles/meta/categories` - Get all categories
- `GET /api/articles/meta/authors` - Get all authors

#### Sources
- `GET /api/sources` - Get all news sources
- `GET /api/sources/{id}` - Get single source

### **Protected Endpoints** (Auth Required 🔒)

#### Authentication
- `GET /api/auth/user` - Get current user
- `POST /api/auth/logout` - Logout

#### User Preferences
- `GET /api/preferences` - Get user preferences
- `PUT /api/preferences` - Update preferences
- `DELETE /api/preferences` - Reset preferences

#### Personalized Content
- `GET /api/articles/personalized/feed` - Get personalized articles

---

## 🎯 Testing Scenarios

### Scenario 1: Search Articles (Public)

1. Find **GET /api/articles** under **Articles** tag
2. Click **"Try it out"**
3. Set parameters:
   - `q`: `cryptocurrency`
   - `per_page`: `20`
   - `sort`: `desc`
4. Click **"Execute"**
5. View the response

**Expected:** If no articles found for "cryptocurrency", auto-fetch feature triggers!

### Scenario 2: Get Personalized Feed (Protected)

1. Login and authorize (see above)
2. First, set preferences:
   - Find **PUT /api/preferences**
   - Click **"Try it out"**
   - Enter:
     ```json
     {
       "preferences": {
         "sources": ["newsapi", "guardian"],
         "categories": ["Technology"]
       }
     }
     ```
   - Click **"Execute"**
3. Then get personalized feed:
   - Find **GET /api/articles/personalized/feed**
   - Click **"Try it out"**
   - Click **"Execute"**

**Expected:** Articles filtered by your preferences!

### Scenario 3: Filter Articles by Multiple Criteria

1. Find **GET /api/articles**
2. Set parameters:
   - `q`: `technology`
   - `from`: `2025-10-01`
   - `to`: `2025-10-25`
   - `source`: `newsapi,guardian`
   - `per_page`: `15`
3. Click **"Execute"**

---

## 🛠️ Regenerate Documentation

If you make changes to the annotations:

```bash
php artisan l5-swagger:generate
```

Then refresh your browser.

---

## 📝 Documentation Highlights

### **Auto-Fetch Feature** ⭐
The `/api/articles` endpoint has special auto-fetch functionality:
- When search query returns 0 results
- Automatically fetches from all 3 news sources
- Response includes `X-Auto-Fetch: true` header
- Fully documented in Swagger!

### **Pagination**
All list endpoints support pagination:
- `per_page` - Items per page (max 100)
- `page` - Page number
- Response includes `total`, `current_page`, `last_page`

### **Filtering**
Advanced filtering on articles:
- By date range (`from`, `to`)
- By source (single or multiple)
- By category
- By author (partial match)
- Combined filters supported

---

## 🎨 Swagger UI Features

### **Try It Out**
Test any endpoint directly from the documentation.

### **Authorize**
Set bearer token once, use for all protected endpoints.

### **Schemas**
View detailed request/response structures.

### **Example Values**
Pre-filled examples for quick testing.

### **Responses**
See all possible HTTP status codes and responses.

### **Download Spec**
Export OpenAPI specification for import into other tools.

---

## 🔧 Configuration

### Swagger Config Location
```
config/l5-swagger.php
```

### Generated Documentation
```
storage/api-docs/api-docs.json
```

### Documentation Views
```
resources/views/vendor/l5-swagger/
```

---

## 📦 Export API Specification

### Option 1: Download from UI
1. Open Swagger UI
2. Look for the URL bar showing the spec
3. Access: `http://localhost:8000/docs/api-docs.json`
4. Save the JSON file

### Option 2: Direct Access
```bash
# Copy the generated JSON
cat storage/api-docs/api-docs.json
```

### Import to Other Tools
You can import the OpenAPI spec to:
- **Postman** - Import as OpenAPI 3.0
- **Insomnia** - Import as OpenAPI
- **API Testing Tools** - Most support OpenAPI/Swagger

---

## 🎓 Best Practices

### 1. **Always Authorize First**
For protected endpoints, authorize at the beginning of your session.

### 2. **Check Response Codes**
- `200` - Success
- `201` - Created
- `401` - Unauthorized (need to login/authorize)
- `404` - Not found
- `422` - Validation error

### 3. **Use Examples**
Click **"Try it out"** and modify the pre-filled examples.

### 4. **Test Edge Cases**
- Empty search results (triggers auto-fetch)
- Invalid IDs (404 responses)
- Missing required fields (422 validation)

### 5. **Explore Response Headers**
Some endpoints return special headers (e.g., `X-Auto-Fetch`)

---

## 🚨 Troubleshooting

### Issue: "Documentation not found"
**Solution:**
```bash
php artisan l5-swagger:generate
```

### Issue: "Authorization not working"
**Solution:**
1. Make sure to include `Bearer ` prefix
2. Use a fresh token from login
3. Check token hasn't expired

### Issue: "Changes not showing"
**Solution:**
```bash
# Clear cache and regenerate
php artisan l5-swagger:generate
# Refresh browser (Ctrl+F5)
```

### Issue: "Server error when testing"
**Solution:**
1. Make sure Laravel server is running (`php artisan serve`)
2. Check `storage/logs/laravel.log` for errors
3. Verify database is running

---

## 📚 Additional Resources

### OpenAPI/Swagger Documentation
- [OpenAPI Specification](https://swagger.io/specification/)
- [Swagger UI](https://swagger.io/tools/swagger-ui/)

### Laravel L5-Swagger
- [Package Documentation](https://github.com/DarkaOnLine/L5-Swagger)
- [Annotation Reference](https://github.com/zircote/swagger-php)

---

## 🎉 Benefits of Swagger Documentation

### **For Developers**
✅ Interactive API testing without Postman
✅ Complete API reference in one place
✅ Request/response examples
✅ Authentication testing

### **For Frontend Teams**
✅ Self-documenting API
✅ Contract-first development
✅ Easy integration testing
✅ Export spec for code generation

### **For API Consumers**
✅ Clear documentation
✅ Try before integrating
✅ Understand data structures
✅ See all available endpoints

---

## 🔗 Quick Links

| Resource | URL |
|----------|-----|
| **Swagger UI** | http://localhost:8000/api/documentation |
| **OpenAPI JSON** | http://localhost:8000/docs/api-docs.json |
| **API Base URL** | http://localhost:8000/api |
| **Health Check** | http://localhost:8000/api/sources |

---

## 📞 Support

For issues with Swagger documentation:
1. Check this guide
2. Regenerate docs: `php artisan l5-swagger:generate`
3. Review annotations in controllers
4. Check Laravel logs

---

**🎊 Your News Aggregator API is now fully documented with Swagger!**

Access it now at: **http://localhost:8000/api/documentation**

