# API Quick Reference

## 🚀 Quick Start

### Basic Usage
```javascript
// GET request
api.get('/admin/users')

// POST request  
api.post('/admin/users', { name: 'John', email: 'john@example.com' })

// PUT request
api.put('/admin/users/1', { name: 'Jane' })

// PATCH request
api.patch('/admin/users/1', { status: true })

// DELETE request
api.delete('/admin/users/1')
```

## 📦 Helper Functions

### Display Errors
```javascript
try {
    await api.post('/endpoint', data);
} catch (error) {
    apiHelpers.displayApiError(error, 'error-container-id', 'form-name');
}
```

### Show Success Message
```javascript
apiHelpers.showSuccessMessage('Record saved successfully!', 'success-container-id', 3000);
```

### Confirm Delete
```javascript
await apiHelpers.confirmDelete('/admin/users/1', (response) => {
    console.log('Deleted!');
    location.reload();
});
```

### Fetch Paginated Data
```javascript
const data = await apiHelpers.fetchPaginated('/admin/users', page: 1, limit: 10, { search: 'john' });
```

### Upload File
```javascript
const file = document.querySelector('#fileInput').files[0];
await apiHelpers.uploadFile('/admin/upload', file, { title: 'My File' }, (progress) => {
    console.log(`Upload progress: ${progress}%`);
});
```

### Toggle Status
```javascript
await apiHelpers.toggleStatus('/admin/users/1', currentStatus, 'is_active');
```

### Debounced Search
```javascript
const results = await apiHelpers.debouncedSearch('/admin/search', searchQuery, 300);
```

### Auto-save
```javascript
input.addEventListener('input', (e) => {
    apiHelpers.autoSave('/admin/drafts/1', { content: e.target.value }, 1000);
});
```

## 🎯 Common Patterns

### Simple Form Submit
```javascript
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const payload = Object.fromEntries(formData.entries());
    
    try {
        const response = await api.post('/endpoint', payload);
        window.location.href = response.data.redirect;
    } catch (error) {
        apiHelpers.displayApiError(error, 'error-div');
    }
});
```

### Delete Button
```javascript
deleteBtn.addEventListener('click', async () => {
    await apiHelpers.confirmDelete('/admin/items/1', () => {
        location.reload();
    });
});
```

### Search with Debounce
```javascript
searchInput.addEventListener('input', async (e) => {
    const results = await apiHelpers.debouncedSearch('/admin/search', e.target.value);
    displayResults(results);
});
```

### File Upload with Progress
```javascript
fileInput.addEventListener('change', async (e) => {
    const file = e.target.files[0];
    const progressBar = document.querySelector('#progress');
    
    try {
        await apiHelpers.uploadFile('/admin/upload', file, {}, (progress) => {
            progressBar.style.width = `${progress}%`;
        });
        alert('Upload complete!');
    } catch (error) {
        alert('Upload failed!');
    }
});
```

## 🔧 Error Handling

### Validation Errors (422)
```javascript
catch (error) {
    if (error.response?.status === 422) {
        const errors = error.response.data.errors;
        // errors = { email: ['Email is required'], password: ['Too short'] }
    }
}
```

### Unauthorized (401)
```javascript
catch (error) {
    if (error.response?.status === 401) {
        window.location.href = '/login';
    }
}
```

### Session Expired (419)
```javascript
// Automatically handled by interceptor
// Shows alert: "Your session has expired. Please refresh the page."
```

## 💡 Tips

✅ **Always use `api` instead of `axios`** - Automatic CSRF handling  
✅ **Use helper functions** - Less code, consistent behavior  
✅ **Use async/await** - Cleaner than `.then()` chains  
✅ **Let interceptors handle common errors** - Don't repeat yourself  
✅ **Check for specific status codes** - Handle validation errors gracefully  

## 📂 Files

- `resources/js/api.js` - Main API instance configuration
- `resources/js/api-helpers.js` - Helper functions
- `resources/js/bootstrap.js` - Global setup

## 🌐 Global Access

Both `api` and `apiHelpers` are available globally in Blade templates:

```html
<script>
    // Available everywhere after bootstrap.js loads
    api.get('/endpoint');
    apiHelpers.showSuccessMessage('Success!');
</script>
```

