# API Key Authentication Guide

## ✅ Setup Complete

Your `CheckApiKey` middleware is now active and ready to use!

## 📋 What's Been Configured

### 1. Middleware Registered (`bootstrap/app.php`)
```php
$middleware->alias([
    'api.key' => CheckApiKey::class,
]);
```

### 2. Routes Updated (`routes/api.php`)
Three types of API authentication are now available:
- **Public routes** (no auth)
- **API Key protected routes** (requires `X-API-KEY` header)
- **Sanctum protected routes** (requires `Bearer` token)
- **Combined** (requires both API key and Sanctum token)

## 🔑 How CheckApiKey Middleware Works

### What It Does
1. Extracts `X-API-KEY` header from the request
2. Validates the key against `api_keys` table
3. Checks if the key is `active`
4. Attaches `store_id` to the request
5. Returns `401 Unauthorized` if invalid

### Middleware Code
```php
// app/Http/Middleware/CheckApiKey.php
public function handle(Request $request, Closure $next): Response
{
    $key = $request->header('X-API-KEY');
    
    $apiKey = ApiKey::where('api_key', $key)
        ->where('status', 'active')
        ->first();
    
    if (!$apiKey) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Attach store_id to request
    $request->merge(['store_id' => $apiKey->store_id]);
    return $next($request);
}
```

## 🚀 Usage Examples

### Method 1: Apply to Route Groups (Recommended)

```php
// Protect entire group with API key
Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
});
```

### Method 2: Apply to Individual Routes

```php
// Single route protection
Route::get('v1/products', [ProductController::class, 'index'])
    ->middleware('api.key');
```

### Method 3: Multiple Middleware (API Key + Sanctum)

```php
// Requires BOTH API key AND user authentication
Route::prefix('v1')->middleware(['api.key', 'auth:sanctum'])->group(function () {
    Route::get('store/orders', [StoreOrderController::class, 'index']);
});
```

### Method 4: Apply in Controller Constructor

```php
// app/Http/Controllers/Api/ProductController.php
class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.key');
        // OR combine with other middleware
        $this->middleware(['api.key', 'auth:sanctum'])->only(['store', 'update']);
    }
}
```

## 🧪 Testing Your API Key Endpoints

### Test 1: Without API Key (Should Fail)

```bash
curl -X GET http://api.techpulse.test:8000/v1/secure/test
```

**Expected Response:**
```json
{
  "error": "Unauthorized"
}
```
**Status:** `401`

### Test 2: With Valid API Key (Should Succeed)

```bash
curl -X GET http://api.techpulse.test:8000/v1/secure/test \
  -H "X-API-KEY: your-api-key-here"
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
**Status:** `200`

### Test 3: From JavaScript/Frontend

```javascript
fetch('http://api.techpulse.test:8000/v1/secure/test', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-API-KEY': 'your-api-key-here'
    }
})
.then(response => response.json())
.then(data => console.log('Success:', data))
.catch(error => console.error('Error:', error));
```

### Test 4: Combined (API Key + Bearer Token)

```bash
curl -X GET http://api.techpulse.test:8000/v1/secure/user-store \
  -H "X-API-KEY: your-api-key-here" \
  -H "Authorization: Bearer your-sanctum-token"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Authenticated with both API key and user token",
  "store_id": 1,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "timestamp": "2025-10-16T..."
}
```

## 📊 Your Current Route Structure

```
PUBLIC (No Auth)
├── GET /v1/health
└── GET /v1/test-cors

API KEY PROTECTED
├── GET /v1/secure/test
└── [Your custom routes here]

SANCTUM PROTECTED
├── GET /v1/user
└── [Your custom routes here]

COMBINED (API Key + Sanctum)
├── GET /v1/secure/user-store
└── [Your custom routes here]

N8N ROUTES
├── POST /n8n/v1/auth/login
├── POST /n8n/v1/auth/register
└── ... (other N8N routes)
```

## 🔐 Managing API Keys

### Generate API Key

You'll need to create API keys in your database. Here's a helper method you can add to your `ApiKey` model:

```php
// app/Models/ApiKey.php
use Illuminate\Support\Str;

public static function generate(int $storeId): string
{
    $apiKey = 'sk_' . Str::random(40); // Example: sk_abc123...
    
    self::create([
        'store_id' => $storeId,
        'api_key' => $apiKey,
        'status' => 'active',
    ]);
    
    return $apiKey;
}
```

### Usage in Controller/Seeder

```php
// Generate API key for store
$apiKey = ApiKey::generate($storeId);
echo "Your API Key: " . $apiKey;
```

### Create API Key via Tinker

```bash
php artisan tinker
```

```php
>>> $apiKey = App\Models\ApiKey::create([
...     'store_id' => 1,
...     'api_key' => 'sk_' . \Illuminate\Support\Str::random(40),
...     'status' => 'active'
... ]);
>>> echo $apiKey->api_key;
```

## 🎯 Accessing Store ID in Controllers

The middleware attaches `store_id` to the request:

```php
// In your controller
public function index(Request $request)
{
    $storeId = $request->get('store_id');
    
    // Use it to filter data by store
    $products = Product::where('store_id', $storeId)->get();
    
    return response()->json([
        'store_id' => $storeId,
        'products' => $products
    ]);
}
```

## 🛡️ Security Best Practices

### 1. Use Strong API Keys

```php
// Generate cryptographically secure keys
$apiKey = 'sk_' . bin2hex(random_bytes(32)); // 64 characters
```

### 2. Rotate Keys Regularly

```php
// Add expiry to api_keys table
Schema::table('api_keys', function (Blueprint $table) {
    $table->timestamp('expires_at')->nullable();
});

// Update middleware to check expiry
if ($apiKey->expires_at && $apiKey->expires_at < now()) {
    return response()->json(['error' => 'API key expired'], 401);
}
```

### 3. Rate Limiting

```php
// Add rate limiting to API key routes
Route::prefix('v1')
    ->middleware(['api.key', 'throttle:60,1']) // 60 requests per minute
    ->group(function () {
        // Your routes
    });
```

### 4. Log API Key Usage

```php
// In CheckApiKey middleware
Log::info('API Key Used', [
    'api_key' => substr($key, 0, 10) . '...', // Don't log full key
    'store_id' => $apiKey->store_id,
    'ip' => $request->ip(),
    'endpoint' => $request->path(),
]);
```

### 5. HTTPS Only in Production

```php
// Add to CheckApiKey middleware (production only)
if (app()->environment('production') && !$request->secure()) {
    return response()->json([
        'error' => 'HTTPS required'
    ], 403);
}
```

## 🔧 Advanced Configuration

### Optional: Make API Key Header Configurable

```php
// config/api.php
return [
    'key_header' => env('API_KEY_HEADER', 'X-API-KEY'),
];

// In middleware
$headerName = config('api.key_header');
$key = $request->header($headerName);
```

### Optional: Support Multiple Authentication Methods

```php
// Middleware checks API key OR Bearer token
$key = $request->header('X-API-KEY');
$token = $request->bearerToken();

if (!$key && !$token) {
    return response()->json(['error' => 'Authentication required'], 401);
}
```

## 📝 Error Responses

The middleware returns these error responses:

| Scenario | Status | Response |
|----------|--------|----------|
| No API key provided | 401 | `{"error": "Unauthorized"}` |
| Invalid API key | 401 | `{"error": "Unauthorized"}` |
| Inactive API key | 401 | `{"error": "Unauthorized"}` |
| Valid API key | 200 | (continues to controller) |

## 🧩 Integration with Your Controllers

### Example Controller Using API Key

```php
// app/Http/Controllers/Api/ProductController.php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Get store_id from middleware
        $storeId = $request->get('store_id');
        
        // Fetch products for this store only
        $products = Product::where('store_id', $storeId)
            ->where('status', 'active')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'store_id' => $storeId,
            'data' => $products
        ]);
    }
}
```

### Add Routes

```php
// routes/api.php
use App\Http\Controllers\Api\ProductController;

Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
});
```

## ✅ Checklist

- [x] Middleware registered as `api.key` alias
- [x] Routes configured with middleware
- [x] Test endpoints created
- [x] CORS configured (from previous setup)
- [ ] Generate API keys for your stores
- [ ] Test with real API keys
- [ ] Implement rate limiting
- [ ] Add API key management UI (optional)
- [ ] Set up key rotation policy (optional)

## 🚀 Next Steps

1. **Generate an API key** for testing:
   ```bash
   php artisan tinker
   >>> App\Models\ApiKey::create(['store_id' => 1, 'api_key' => 'sk_test_' . \Illuminate\Support\Str::random(40), 'status' => 'active']);
   ```

2. **Test the endpoint**:
   ```bash
   curl -X GET http://api.techpulse.test:8000/v1/secure/test \
     -H "X-API-KEY: sk_test_YOUR_KEY_HERE"
   ```

3. **Create your API controllers** and add routes

4. **Document your API** endpoints for users

---

**Need help?** Check the middleware file: `app/Http/Middleware/CheckApiKey.php`

