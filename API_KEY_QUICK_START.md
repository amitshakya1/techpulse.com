# API Key Authentication - Quick Start Guide

## ✅ Setup Complete!

Your `CheckApiKey` middleware is now active and ready to protect your API routes.

## 🚀 Quick Start (3 Steps)

### Step 1: Generate an API Key

```bash
# Interactive mode (shows available stores)
php artisan api:generate-key

# Specify store ID directly
php artisan api:generate-key 1

# Generate with draft status
php artisan api:generate-key 1 --status=draft
```

**Example Output:**
```
✅ API Key Generated Successfully!

┌──────────────┬─────────────────────────────────────┐
│ Field        │ Value                               │
├──────────────┼─────────────────────────────────────┤
│ Store ID     │ 1                                   │
│ Store Name   │ My Store                            │
│ API Key      │ sk_abc123...                        │
│ Status       │ active                              │
│ Created At   │ 2025-10-16 12:00:00                 │
└──────────────┴─────────────────────────────────────┘

⚠️  Store this API key securely. It won't be shown again!
```

### Step 2: Test Your API Key

```bash
curl -X GET http://api.techpulse.test:8000/v1/secure/test \
  -H "X-API-KEY: your-generated-key-here"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "API Key authentication successful!",
  "store_id": 1,
  "timestamp": "2025-10-16T..."
}
```

### Step 3: Use in Your Controllers

```php
// routes/api.php
Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
});

// Controller
public function index(Request $request)
{
    $storeId = $request->get('store_id'); // From middleware
    $products = Product::where('store_id', $storeId)->get();
    
    return response()->json(['data' => $products]);
}
```

## 📋 Available Routes

### Public Routes (No Auth)
```
GET  /v1/health           - Health check
GET  /v1/test-cors        - CORS test
```

### API Key Protected Routes
```
GET  /v1/secure/test      - Test API key auth (requires X-API-KEY)
```

### Sanctum Protected Routes
```
GET  /v1/user             - Get authenticated user (requires Bearer token)
```

### Combined Protection (API Key + Sanctum)
```
GET  /v1/secure/user-store - Requires both X-API-KEY and Bearer token
```

## 🔑 How to Apply Middleware

### Method 1: Route Group
```php
Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
});
```

### Method 2: Individual Route
```php
Route::get('v1/products', [ProductController::class, 'index'])
    ->middleware('api.key');
```

### Method 3: Multiple Middleware
```php
Route::prefix('v1')
    ->middleware(['api.key', 'auth:sanctum'])
    ->group(function () {
        Route::get('secure-data', [DataController::class, 'index']);
    });
```

### Method 4: Controller Constructor
```php
class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.key');
    }
}
```

## 🧪 Testing Examples

### cURL
```bash
# Test without API key (401 error)
curl -X GET http://api.techpulse.test:8000/v1/secure/test

# Test with API key (success)
curl -X GET http://api.techpulse.test:8000/v1/secure/test \
  -H "X-API-KEY: sk_your_key_here"
```

### JavaScript/Fetch
```javascript
fetch('http://api.techpulse.test:8000/v1/secure/test', {
    headers: {
        'X-API-KEY': 'sk_your_key_here'
    }
})
.then(r => r.json())
.then(data => console.log(data));
```

### Axios
```javascript
axios.get('http://api.techpulse.test:8000/v1/secure/test', {
    headers: {
        'X-API-KEY': 'sk_your_key_here'
    }
})
.then(response => console.log(response.data));
```

### Postman
1. Open Postman
2. Create new GET request: `http://api.techpulse.test:8000/v1/secure/test`
3. Go to Headers tab
4. Add header: `X-API-KEY` with value `sk_your_key_here`
5. Send request

## 📊 API Key Management Commands

```bash
# Generate new API key
php artisan api:generate-key

# Generate for specific store
php artisan api:generate-key 1

# Generate as draft (inactive)
php artisan api:generate-key 1 --status=draft

# View all routes
php artisan route:list --path=v1

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## 🔐 Security Features

✅ **API Key Validation** - Keys are validated against database  
✅ **Status Check** - Only 'active' keys are accepted  
✅ **Store Isolation** - Each key is linked to a specific store  
✅ **Request Injection** - Store ID is automatically injected into requests  
✅ **CORS Protected** - Cross-origin requests are controlled  

## 📁 File Locations

```
app/
├── Http/
│   └── Middleware/
│       └── CheckApiKey.php          ← Middleware logic
├── Models/
│   └── ApiKey.php                   ← API Key model
└── Console/
    └── Commands/
        └── GenerateApiKey.php       ← Generate keys command

bootstrap/
└── app.php                          ← Middleware registered here

routes/
└── api.php                          ← Your API routes

config/
└── cors.php                         ← CORS configuration

database/
└── migrations/
    └── *_create_api_keys_table.php  ← Database schema
```

## 🎯 Next Steps

1. ✅ Generate an API key: `php artisan api:generate-key`
2. ✅ Test the endpoint with your key
3. ✅ Create your API controllers
4. ✅ Add protected routes using `->middleware('api.key')`
5. ✅ Document your API for users

## 💡 Tips

- **Prefix Convention**: Use `sk_` prefix for API keys (e.g., `sk_abc123...`)
- **Key Length**: Current keys are 48 random characters (very secure)
- **Status Management**: Use `draft` for testing, `active` for production, `archived` for retired keys
- **Store ID Access**: Always available via `$request->get('store_id')` in controllers

## 🐛 Troubleshooting

### "Unauthorized" error even with valid key

**Check:**
1. Key status is `active` in database
2. Header name is exactly `X-API-KEY` (case-sensitive)
3. No extra spaces in the key value
4. Run `php artisan config:clear`

### Can't access store_id in controller

**Solution:**
```php
// Correct way to get store_id
$storeId = $request->get('store_id');

// NOT this way (won't work)
$storeId = $request->input('store_id');
```

### Routes not found

**Solution:**
```bash
php artisan route:clear
php artisan route:list --path=v1
```

---

**For detailed documentation, see:** `API_KEY_AUTHENTICATION_GUIDE.md`

