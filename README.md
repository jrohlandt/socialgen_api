# SocialGen API

## Project Setup Guide

1. **Start the Docker containers:**
    ```bash
    docker compose up -d
    ```
2. **Install Composer depencies:**
    ```bash
    // Run install locally
    composer install
    // Or run in container
    docker compose exec php composer install
    ```
3. **Laravel Config:**
    1. Copy and rename .env.example and rename to .env
    2. In .env populate the OPENAI_API_KEY (at bottom of file)
    3. Generate app secret

    ```bash
    docker compose exec php php artisan key:generate
    ```

    4. Run migrations and seed database

    ```bash
    docker compose exec php php artisan migrate --seed
    ```

    5. Run automated tests

    ```bash
    docker compose exec php php artisan test
    ```

---

# SocialGen API Documentation

Base URL: `http://localhost:8009`

All authenticated endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer <your_token>
```

Content-Type: `application/json`
Accept: `application/json`

---

## Authentication

### 1. Register User

**Endpoint:** `POST /api/register`

Register a new user account.

| Parameter             | Type   | Required | Description                        |
| --------------------- | ------ | -------- | ---------------------------------- |
| name                  | string | Yes      | User's name (max 100 chars)        |
| email                 | string | Yes      | Valid email address                |
| password              | string | Yes      | Password (requires confirmation)   |
| password_confirmation | string | Yes      | Must match password                |
| brand_name            | string | Yes      | Brand name (max 100 chars)         |
| brand_description     | string | Yes      | Brand description (max 1000 chars) |
| website               | string | No       | Website URL (max 1000 chars)       |

**Example Request:**

```bash
curl -X POST http://localhost:8009/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "brand_name": "My Brand",
    "brand_description": "We make awesome things",
    "website": "https://mybrand.com"
  }'
```

**Example Response (201):**

```json
{
    "token": "1|abc123def456...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "brand_name": "My Brand",
        "brand_description": "We make awesome things",
        "website": "https://mybrand.com",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

### 2. Login User

**Endpoint:** `POST /api/login`

Authenticate and receive an API token.

| Parameter | Type   | Required | Description         |
| --------- | ------ | -------- | ------------------- |
| email     | string | Yes      | Valid email address |
| password  | string | Yes      | User's password     |

**Example Request:**

```bash
curl -X POST http://localhost:8009/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Example Response (200):**

```json
{
    "token": "1|abc123def456...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "brand_name": "My Brand",
        "brand_description": "We make awesome things",
        "website": "https://mybrand.com",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

**Error Response (401):**

```json
{
    "message": "Invalid credentials"
}
```

---

### 3. Get Current User

**Endpoint:** `GET /api/user`

Returns the authenticated user's profile information.

**Example Request:**

```bash
curl -X GET http://localhost:8009/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Example Response (200):**

```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "brand_name": "My Brand",
        "brand_description": "We make awesome things",
        "website": "https://mybrand.com",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

## Dashboard

### 4. Get Dashboard

**Endpoint:** `GET /api/dashboard`

Returns user metrics including total requests, saved posts count, last generation time, and recent posts.

**Example Request:**

```bash
curl -X GET http://localhost:8009/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Example Response (200):**

```json
{
    "total_requests": 140,
    "total_saved_posts": 5,
    "last_generation_time": "2024-01-15T14:30:00.000000Z",
    "posts": [
        {
            "id": 1,
            "user_id": 1,
            "title": "My First Post",
            "content": "This is the content of my first post",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        },
        {
            "id": 2,
            "user_id": 1,
            "title": "Another Post",
            "content": "More content here",
            "created_at": "2024-01-15T11:00:00.000000Z",
            "updated_at": "2024-01-15T11:00:00.000000Z"
        }
    ]
}
```

---

## Post Management

### 5. List All Posts

**Endpoint:** `GET /api/posts`

Returns all posts created by the authenticated user.

**Example Request:**

```bash
curl -X GET http://localhost:8009/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Example Response (200):**

```json
{
    "posts": [
        {
            "id": 1,
            "user_id": 1,
            "title": "My First Post",
            "content": "This is the content of my first post",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        },
        {
            "id": 2,
            "user_id": 1,
            "title": "Another Post",
            "content": "More content here",
            "created_at": "2024-01-15T11:00:00.000000Z",
            "updated_at": "2024-01-15T11:00:00.000000Z"
        }
    ]
}
```

---

### 6. Create Post

**Endpoint:** `POST /api/posts`

Save a generated post option to the database.

| Parameter | Type   | Required | Description  |
| --------- | ------ | -------- | ------------ |
| title     | string | Yes      | Post title   |
| content   | string | Yes      | Post content |

**Example Request:**

```bash
curl -X POST http://localhost:8009/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "My New Post",
    "content": "This is the content of my new post"
  }'
```

**Example Response (201):**

```json
{
    "post": {
        "id": 3,
        "user_id": 1,
        "title": "My New Post",
        "content": "This is the content of my new post",
        "created_at": "2024-01-15T15:00:00.000000Z",
        "updated_at": "2024-01-15T15:00:00.000000Z"
    }
}
```

---

### 7. Get Single Post

**Endpoint:** `GET /api/posts/{id}`

Returns a specific post by ID.

**Example Request:**

```bash
curl -X GET http://localhost:8009/api/posts/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Example Response (200):**

```json
{
    "post": {
        "id": 1,
        "user_id": 1,
        "title": "My First Post",
        "content": "This is the content of my first post",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

**Error Response (403):**

```json
{
    "message": "Unauthorized"
}
```

---

### 8. Update Post

**Endpoint:** `PUT /api/posts/{id}`

Update an existing post's title and/or content.

| Parameter | Type   | Required | Description  |
| --------- | ------ | -------- | ------------ |
| title     | string | Yes      | Post title   |
| content   | string | Yes      | Post content |

**Example Request:**

```bash
curl -X PUT http://localhost:8009/api/posts/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Updated Title",
    "content": "Updated content"
  }'
```

**Example Response (200):**

```json
{
    "post": {
        "id": 1,
        "user_id": 1,
        "title": "Updated Title",
        "content": "Updated content",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T16:00:00.000000Z"
    }
}
```

---

### 9. Delete Post

**Endpoint:** `DELETE /api/posts/{id}`

Delete a specific post.

**Example Request:**

```bash
curl -X DELETE http://localhost:8009/api/posts/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Example Response (204):**

```
(No content returned)
```

---

## Content Generation

### 10. Generate Post Suggestions

**Endpoint:** `POST /api/posts/generate`

Generate 3 social media post suggestions based on a topic using AI.

| Parameter | Type   | Required | Description                            |
| --------- | ------ | -------- | -------------------------------------- |
| topic     | string | Yes      | The topic for the post (max 100 chars) |

**Example Request:**

```bash
curl -X POST http://localhost:8009/api/posts/generate \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "topic": "Sustainability in business"
  }'
```

**Example Response (201):**

```json
{
    "options": [
        {
            "title": "Going Green: How Sustainability Drives Success",
            "content": "Discover how embracing sustainability can boost your bottom line while saving the planet. #sustainability #business"
        },
        {
            "title": "5 Simple Steps to a Greener Office",
            "content": "Small changes make big impacts. Here are 5 easy ways to make your workplace more eco-friendly. #greenoffice #sustainability"
        },
        {
            "title": "Why Customers Choose Sustainable Brands",
            "content": "Studies show 73% of millennials prefer sustainable brands. Is your business ready? #sustainablebusiness #consumertrends"
        }
    ]
}
```

**Error Response (rate limit or API error):**

```json
{
    "message": "Rate limit exceeded. Please try again later."
}
```

---

## Testing Guide

### Quick Start

1. **Start the Laravel server:**

    ```bash
    docker compose up -d
    ```

2. **Register a new user** and copy the token from the response

3. **Use the token** in all subsequent requests via the Authorization header

### Complete Testing Flow

```bash
# 1. Register a new user
curl -X POST http://localhost:8009/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "brand_name": "Test Brand",
    "brand_description": "A test brand description"
  }'

# Save the token from response, then use it in these commands:

# 2. Get current user profile
curl -X GET http://localhost:8009/api/user \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 3. Get dashboard
curl -X GET http://localhost:8009/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 4. Generate post suggestions
curl -X POST http://localhost:8009/api/posts/generate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"topic": "Your topic here"}'

# 5. Save a generated post
curl -X POST http://localhost:8009/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title": "Post Title", "content": "Post Content"}'

# 6. List all posts
curl -X GET http://localhost:8009/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 7. Get single post
curl -X GET http://localhost:8009/api/posts/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 8. Update a post
curl -X PUT http://localhost:8009/api/posts/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title": "New Title", "content": "New Content"}'

# 9. Delete a post
curl -X DELETE http://localhost:8009/api/posts/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Using environment variables for convenience:

```bash
# Save token to variable
TOKEN="your_token_here"

# Test protected endpoint
curl -X GET http://localhost:8009/api/dashboard \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```
