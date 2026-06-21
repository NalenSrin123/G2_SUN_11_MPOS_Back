# API Documentation - Categories and Products

## Base URL
```
http://127.0.0.1:3000/api/v1
```

## Authentication
Currently, no authentication is required (auth:sanctum middleware removed for testing).

---

## Categories API

### 1. List Categories
**Endpoint:** `GET /categories`

**Query Parameters:**
- `search` (string) - Search by name
- `search_description` (string) - Search by description
- `is_active` (boolean) - Filter by active status
- `created_from` (date) - Filter by created date from (YYYY-MM-DD)
- `created_to` (date) - Filter by created date to (YYYY-MM-DD)
- `updated_from` (date) - Filter by updated date from (YYYY-MM-DD)
- `updated_to` (date) - Filter by updated date to (YYYY-MM-DD)
- `sort_by` (string) - Sort by field: `name`, `created_at`, `updated_at`, `id` (default: `id`)
- `sort_dir` (string) - Sort direction: `asc`, `desc` (default: `desc`)
- `page` (integer) - Page number (default: `1`)
- `per_page` (integer) - Items per page (default: `15`)

**Example Request:**
```bash
GET http://127.0.0.1:3000/api/v1/categories?search=category&is_active=true&sort_by=name&sort_dir=asc&page=1&per_page=10
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "name": "Updated Category",
      "slug": "updated-category",
      "description": "Updated description",
      "is_active": true,
      "created_at": "2026-06-14T05:41:25.000000Z",
      "updated_at": "2026-06-21T05:19:43.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  }
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/categories?page=1&per_page=10&sort_by=name&sort_dir=asc')
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 2. Get Single Category
**Endpoint:** `GET /categories/{id}`

**Example Request:**
```bash
GET http://127.0.0.1:3000/api/v1/categories/2
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "name": "Updated Category",
    "slug": "updated-category",
    "description": "Updated description",
    "is_active": true,
    "created_at": "2026-06-14T05:41:25.000000Z",
    "updated_at": "2026-06-21T05:19:43.000000Z"
  }
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/categories/2')
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 3. Create Category
**Endpoint:** `POST /categories`

**Request Body:**
```json
{
  "name": "New Category",
  "description": "Category description",
  "is_active": true
}
```

**Validation Rules:**
- `name` (required, string, max 255)
- `description` (nullable, string)
- `is_active` (optional, boolean)

**Example Request:**
```bash
POST http://127.0.0.1:3000/api/v1/categories
Content-Type: application/json

{
  "name": "New Category",
  "description": "Category description",
  "is_active": true
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "id": 4,
    "name": "New Category",
    "slug": "new-category",
    "description": "Category description",
    "is_active": true,
    "created_at": "2026-06-21T05:18:09.000000Z",
    "updated_at": "2026-06-21T05:18:09.000000Z"
  }
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/categories', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    name: 'New Category',
    description: 'Category description',
    is_active: true
  })
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 4. Update Category
**Endpoint:** `PUT /categories/{id}`

**Request Body:**
```json
{
  "name": "Updated Category Name",
  "description": "New description",
  "is_active": false
}
```

**Validation Rules:**
- `name` (optional, string, max 255)
- `description` (nullable, string)
- `is_active` (optional, boolean)

**Note:** Slug is automatically regenerated if name changes.

**Example Request:**
```bash
PUT http://127.0.0.1:3000/api/v1/categories/2
Content-Type: application/json

{
  "name": "Updated Category Name",
  "description": "New description",
  "is_active": false
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Category updated successfully",
  "data": {
    "id": 2,
    "name": "Updated Category Name",
    "slug": "updated-category-name",
    "description": "New description",
    "is_active": false,
    "created_at": "2026-06-14T05:41:25.000000Z",
    "updated_at": "2026-06-21T05:19:43.000000Z"
  }
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/categories/2', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    name: 'Updated Category Name',
    description: 'New description',
    is_active: false
  })
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 5. Delete Category
**Endpoint:** `DELETE /categories/{id}`

**Note:** Cannot delete category that has products. Remove or reassign products first.

**Example Request:**
```bash
DELETE http://127.0.0.1:3000/api/v1/categories/4
```

**Example Response:**
```json
{
  "success": true,
  "message": "Category deleted successfully"
}
```

**Error Response (if category has products):**
```json
{
  "success": false,
  "message": "Cannot delete category that has products. Remove or reassign products first."
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/categories/4', {
  method: 'DELETE'
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

## Products API

### 1. List Products
**Endpoint:** `GET /products`

**Query Parameters:**
- `search` (string) - Search by name
- `search_description` (string) - Search by description
- `category_id` (integer) - Filter by category
- `is_active` (boolean) - Filter by active status
- `min_price` (decimal) - Filter by minimum price
- `max_price` (decimal) - Filter by maximum price
- `min_stock` (integer) - Filter by minimum stock
- `max_stock` (integer) - Filter by maximum stock
- `created_from` (date) - Filter by created date from (YYYY-MM-DD)
- `created_to` (date) - Filter by created date to (YYYY-MM-DD)
- `updated_from` (date) - Filter by updated date from (YYYY-MM-DD)
- `updated_to` (date) - Filter by updated date to (YYYY-MM-DD)
- `sort_by` (string) - Sort by field: `name`, `price`, `stock`, `created_at`, `updated_at`, `id` (default: `id`)
- `sort_dir` (string) - Sort direction: `asc`, `desc` (default: `desc`)
- `page` (integer) - Page number (default: `1`)
- `per_page` (integer) - Items per page (default: `15`)

**Example Request:**
```bash
GET http://127.0.0.1:3000/api/v1/products?category_id=2&min_price=5&max_price=20&is_active=true&sort_by=price&sort_dir=asc&page=1&per_page=10
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "category_id": 2,
      "name": "New Product",
      "slug": "new-product",
      "description": "New product description",
      "price": "15.99",
      "stock": 100,
      "image": null,
      "is_active": true,
      "created_at": "2026-06-21T05:21:33.000000Z",
      "updated_at": "2026-06-21T05:21:33.000000Z",
      "category": {
        "id": 2,
        "name": "Updated Category",
        "slug": "updated-category",
        "description": "Updated description",
        "is_active": true,
        "created_at": "2026-06-14T05:41:25.000000Z",
        "updated_at": "2026-06-21T05:19:43.000000Z"
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  }
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/products?category_id=2&min_price=5&max_price=20&sort_by=price&sort_dir=asc')
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 2. Get Single Product
**Endpoint:** `GET /products/{id}`

**Example Request:**
```bash
GET http://127.0.0.1:3000/api/v1/products/2
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "category_id": 2,
    "name": "Updated Coffee",
    "slug": "updated-coffee",
    "description": null,
    "price": "3.50",
    "stock": 0,
    "image": null,
    "is_active": true,
    "created_at": "2026-06-14T05:43:03.000000Z",
    "updated_at": "2026-06-21T05:19:49.000000Z",
    "category": {
      "id": 2,
      "name": "Updated Category",
      "slug": "updated-category",
      "description": "Updated description",
      "is_active": true,
      "created_at": "2026-06-14T05:41:25.000000Z",
      "updated_at": "2026-06-21T05:19:43.000000Z"
    }
  }
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/products/2')
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 3. Create Product
**Endpoint:** `POST /products`

**Request Body:**
```json
{
  "category_id": 2,
  "name": "New Product",
  "description": "Product description",
  "price": 15.99,
  "stock": 50,
  "image": "product-image.jpg",
  "is_active": true
}
```

**Validation Rules:**
- `category_id` (required, exists in categories table)
- `name` (required, string, max 255)
- `description` (nullable, string)
- `price` (required, numeric, min 0)
- `stock` (optional, integer, min 0)
- `image` (nullable, string)
- `is_active` (optional, boolean)

**Example Request:**
```bash
POST http://127.0.0.1:3000/api/v1/products
Content-Type: application/json

{
  "category_id": 2,
  "name": "New Product",
  "description": "Product description",
  "price": 15.99,
  "stock": 50,
  "image": "product-image.jpg",
  "is_active": true
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 5,
    "category_id": 2,
    "name": "New Product",
    "slug": "new-product",
    "description": "Product description",
    "price": "15.99",
    "stock": 50,
    "image": "product-image.jpg",
    "is_active": true,
    "created_at": "2026-06-21T05:21:33.000000Z",
    "updated_at": "2026-06-21T05:21:33.000000Z",
    "category": {
      "id": 2,
      "name": "Updated Category",
      "slug": "updated-category",
      "description": "Updated description",
      "is_active": true,
      "created_at": "2026-06-14T05:41:25.000000Z",
      "updated_at": "2026-06-21T05:19:43.000000Z"
    }
  }
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/products', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    category_id: 2,
    name: 'New Product',
    description: 'Product description',
    price: 15.99,
    stock: 50,
    image: 'product-image.jpg',
    is_active: true
  })
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 4. Update Product
**Endpoint:** `PUT /products/{id}`

**Request Body:**
```json
{
  "category_id": 2,
  "name": "Updated Product Name",
  "description": "New description",
  "price": 25.99,
  "stock": 75,
  "image": "updated-image.jpg",
  "is_active": false
}
```

**Validation Rules:**
- `category_id` (optional, exists in categories table)
- `name` (optional, string, max 255)
- `description` (nullable, string)
- `price` (optional, numeric, min 0)
- `stock` (optional, integer, min 0)
- `image` (nullable, string)
- `is_active` (optional, boolean)

**Note:** Slug is automatically regenerated if name changes.

**Example Request:**
```bash
PUT http://127.0.0.1:3000/api/v1/products/2
Content-Type: application/json

{
  "name": "Updated Product Name",
  "price": 25.99,
  "stock": 75
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Product updated successfully",
  "data": {
    "id": 2,
    "category_id": 2,
    "name": "Updated Product Name",
    "slug": "updated-product-name",
    "description": null,
    "price": "25.99",
    "stock": 75,
    "image": null,
    "is_active": true,
    "created_at": "2026-06-14T05:43:03.000000Z",
    "updated_at": "2026-06-21T05:19:49.000000Z",
    "category": {
      "id": 2,
      "name": "Updated Category",
      "slug": "updated-category",
      "description": "Updated description",
      "is_active": true,
      "created_at": "2026-06-14T05:41:25.000000Z",
      "updated_at": "2026-06-21T05:19:43.000000Z"
    }
  }
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/products/2', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    name: 'Updated Product Name',
    price: 25.99,
    stock: 75
  })
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 5. Delete Product
**Endpoint:** `DELETE /products/{id}`

**Example Request:**
```bash
DELETE http://127.0.0.1:3000/api/v1/products/5
```

**Example Response:**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/products/5', {
  method: 'DELETE'
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

### Not Found (404)
```json
{
  "message": "No query results for model [App\\Models\\Category] 999"
}
```

### Server Error (500)
```json
{
  "message": "Server Error",
  "exception": "..."
}
```

---

## Common Use Cases

### Get all active categories sorted by name
```bash
GET http://127.0.0.1:3000/api/v1/categories?is_active=true&sort_by=name&sort_dir=asc
```

### Get products in a specific price range
```bash
GET http://127.0.0.1:3000/api/v1/products?min_price=10&max_price=50
```

### Get products with low stock
```bash
GET http://127.0.0.1:3000/api/v1/products?max_stock=10
```

### Search products by name or description
```bash
GET http://127.0.0.1:3000/api/v1/products?search=coffee
GET http://127.0.0.1:3000/api/v1/products?search_description=premium
```

### Get products created this week
```bash
GET http://127.0.0.1:3000/api/v1/products?created_from=2026-06-14&created_to=2026-06-21
```

### Paginated results with sorting
```bash
GET http://127.0.0.1:3000/api/v1/products?page=1&per_page=20&sort_by=price&sort_dir=desc
```
