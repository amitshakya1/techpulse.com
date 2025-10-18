# API Instance Usage Guide

This guide explains how to use the centralized Axios API instance across your application.

## Overview

We've created a centralized Axios instance (`window.api`) that automatically handles:
- ✅ CSRF token injection
- ✅ Default headers (Content-Type, Accept)
- ✅ Global error handling
- ✅ Session expiration handling
- ✅ Network error handling

## Setup

The API instance is configured in `resources/js/api.js` and made globally available via `resources/js/bootstrap.js`.

## Usage

### 1. In Blade Templates (Inline Scripts)

Simply use `api` instead of `axios`:

```html
<script>
    // ❌ OLD WAY (Don't do this)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.post('/api/endpoint', payload, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })

    // ✅ NEW WAY (Much cleaner!)
    api.post('/api/endpoint', payload)
        .then(response => {
            console.log('Success:', response.data);
        })
        .catch(error => {
            console.error('Error:', error);
            // Error is already logged by interceptor
        });
</script>
```

### 2. GET Requests

```javascript
// Simple GET request
api.get('/admin/users')
    .then(response => {
        console.log(response.data);
    })
    .catch(error => {
        console.error(error);
    });

// GET with query parameters
api.get('/admin/users', {
    params: {
        page: 1,
        limit: 10,
        search: 'john'
    }
})
    .then(response => {
        console.log(response.data);
    });
```

### 3. POST Requests

```javascript
// POST with JSON payload
api.post('/admin/users', {
    name: 'John Doe',
    email: 'john@example.com',
    password: 'secret123'
})
    .then(response => {
        console.log('User created:', response.data);
    })
    .catch(error => {
        // Handle validation errors
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            console.log('Validation errors:', errors);
        }
    });
```

### 4. PUT/PATCH Requests

```javascript
// Update user
api.put('/admin/users/123', {
    name: 'Jane Doe',
    email: 'jane@example.com'
})
    .then(response => {
        console.log('User updated:', response.data);
    });

// Partial update with PATCH
api.patch('/admin/users/123', {
    email: 'newemail@example.com'
});
```

### 5. DELETE Requests

```javascript
// Delete resource
api.delete('/admin/users/123')
    .then(response => {
        console.log('User deleted');
    })
    .catch(error => {
        console.error('Failed to delete user');
    });
```

### 6. With Async/Await (Recommended)

```javascript
// ✅ Modern and cleaner approach
async function loginUser(email, password) {
    try {
        const response = await api.post('/admin/login', {
            email,
            password
        });
        
        console.log('Login successful:', response.data);
        window.location.href = response.data.redirect;
    } catch (error) {
        if (error.response?.status === 422) {
            // Validation errors
            console.error('Validation errors:', error.response.data.errors);
        } else if (error.response?.status === 401) {
            // Authentication failed
            console.error('Invalid credentials');
        }
    }
}
```

### 7. Handling Validation Errors

```javascript
api.post('/admin/users', formData)
    .then(response => {
        console.log('Success!');
    })
    .catch(error => {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            
            // Display errors in your UI
            for (const field in errors) {
                if (errors.hasOwnProperty(field)) {
                    errors[field].forEach(msg => {
                        console.log(`${field}: ${msg}`);
                        // Display in your form
                        displayFieldError(field, msg);
                    });
                }
            }
        }
    });
```

### 8. FormData Uploads (Files)

```javascript
// For file uploads, the API instance will automatically set the correct Content-Type
const formData = new FormData();
formData.append('file', fileInput.files[0]);
formData.append('title', 'My File');

api.post('/admin/upload', formData)
    .then(response => {
        console.log('File uploaded:', response.data);
    });
```

### 9. Custom Headers for Specific Requests

```javascript
// Override headers for a specific request
api.post('/api/special-endpoint', payload, {
    headers: {
        'X-Custom-Header': 'CustomValue'
    }
})
    .then(response => {
        console.log(response.data);
    });
```

### 10. Complete Form Example

```html
@push('script')
<script>
    const formName = 'my-form';
    const validation = new JustValidate(`#${formName}`);

    validation
        .addField('input[name="email"]', [
            { rule: 'required', errorMessage: 'Email is required' },
            { rule: 'email', errorMessage: 'Invalid email format' }
        ])
        .addField('input[name="password"]', [
            { rule: 'required', errorMessage: 'Password is required' }
        ])
        .onSuccess(async (event) => {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const payload = Object.fromEntries(formData.entries());
            
            try {
                const response = await api.post('{{ route('admin.store') }}', payload);
                
                // Show success message
                showSuccessMessage(response.data.message);
                
                // Redirect or update UI
                setTimeout(() => {
                    window.location.href = response.data.redirect;
                }, 1000);
                
            } catch (error) {
                const errorDiv = document.getElementById(`${formName}-message`);
                errorDiv.innerHTML = '';
                
                if (error.response) {
                    const { status, data } = error.response;
                    
                    // Handle different error types
                    if (status === 422) {
                        // Validation errors
                        let html = `<p class="font-semibold mb-2">${data.message}</p>`;
                        
                        if (data.errors) {
                            html += '<ul class="list-disc list-inside">';
                            for (const field in data.errors) {
                                data.errors[field].forEach(msg => {
                                    html += `<li>${msg}</li>`;
                                });
                            }
                            html += '</ul>';
                        }
                        
                        errorDiv.innerHTML = html;
                    } else if (status === 419) {
                        // CSRF token expired
                        errorDiv.innerHTML = '<p>Session expired. Please refresh the page.</p>';
                    } else {
                        // Generic error
                        errorDiv.innerHTML = `<p>${data.message || 'An error occurred'}</p>`;
                    }
                } else {
                    // Network error
                    errorDiv.innerHTML = '<p>Network error. Please check your connection.</p>';
                }
            }
        });
</script>
@endpush
```

## Global Error Handling

The API instance automatically handles these errors:

| Status Code | Description | Action |
|-------------|-------------|--------|
| 401 | Unauthorized | Console warning (customize in `api.js`) |
| 403 | Forbidden | Console warning |
| 419 | CSRF Token Mismatch | Alert to refresh page |
| 422 | Validation Error | Logged to console, handle in catch block |
| 429 | Too Many Requests | Console warning |
| 500 | Server Error | Console error |
| 503 | Service Unavailable | Console warning |

## Customization

To customize the API instance, edit `resources/js/api.js`:

```javascript
// Change base URL
const api = axios.create({
    baseURL: '/api/v1/', // Your custom base URL
    // ...
});

// Add custom interceptors
api.interceptors.request.use(
    (config) => {
        // Add your custom logic
        return config;
    }
);
```

## Import in JavaScript Modules

If you're using ES6 modules:

```javascript
import api from './api';

// Use api as needed
api.get('/endpoint');
```

## Benefits

✅ **DRY Principle**: Write CSRF token handling once, use everywhere  
✅ **Consistency**: Same headers and error handling across all requests  
✅ **Maintainability**: Update API configuration in one place  
✅ **Error Handling**: Centralized error logging and handling  
✅ **Type Safety**: Easy to extend with TypeScript if needed  

## Migration Guide

To migrate existing code:

1. **Find**: `axios.post(` or `axios.get(`
2. **Replace with**: `api.post(` or `api.get(`
3. **Remove**: Manual CSRF token handling
4. **Remove**: Repetitive headers configuration

## Questions?

If you encounter issues or need additional features, update `resources/js/api.js` to fit your needs.

