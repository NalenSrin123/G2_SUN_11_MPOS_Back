# API Documentation - Categories and Products

## Base URL
```
http://127.0.0.1:3000/api/v1
```

## Authentication
Currently, no authentication is required (auth:sanctum middleware removed for testing).

---

## Categories API

### 1. Get All Categories
**Endpoint:** `GET /categories`

**Query Parameters:**
- `search` (string, optional) - Search by name

**Example Request:**
```bash
GET http://127.0.0.1:3000/api/v1/categories
GET http://127.0.0.1:3000/api/v1/categories?search=category
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
  ]
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/categories')
  .then(response => response.json())
  .then(data => console.log(data));

// With search
fetch('http://127.0.0.1:3000/api/v1/categories?search=category')
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 2. Create Category
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

### 3. Update Category by ID
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

### 4. Delete Category by ID
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

### 1. Get All Products
**Endpoint:** `GET /products`

**Query Parameters:**
- `category_id` (integer, optional) - Filter by category ID

**Example Request:**
```bash
GET http://127.0.0.1:3000/api/v1/products
GET http://127.0.0.1:3000/api/v1/products?category_id=2
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
  ]
}
```

**Fetch API Example:**
```javascript
fetch('http://127.0.0.1:3000/api/v1/products')
  .then(response => response.json())
  .then(data => console.log(data));

// With category filter
fetch('http://127.0.0.1:3000/api/v1/products?category_id=2')
  .then(response => response.json())
  .then(data => console.log(data));
```

---

### 2. Create Product
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

### 3. Update Product by ID
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

### 4. Delete Product by ID
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
