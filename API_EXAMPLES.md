# API Implementation Examples

Ready-to-use examples for common scenarios across your application.

---

## 1. 📝 CREATE Form (User Registration, Add Product, etc.)

```html
<form id="create-form" class="space-y-4">
    <input type="text" name="name" placeholder="Name" class="form-input">
    <input type="email" name="email" placeholder="Email" class="form-input">
    <button type="submit">Submit</button>
</form>

<div id="create-form-message" class="hidden"></div>
<div id="success-message" class="hidden"></div>

<script>
document.getElementById('create-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const payload = Object.fromEntries(formData.entries());
    
    try {
        const response = await api.post('{{ route('admin.users.store') }}', payload);
        
        apiHelpers.showSuccessMessage(response.data.message || 'Created successfully!');
        
        // Redirect or reset form
        setTimeout(() => {
            window.location.href = response.data.redirect || '{{ route('admin.users.index') }}';
        }, 1000);
        
    } catch (error) {
        apiHelpers.displayApiError(error, 'create-form-message', 'create-form');
    }
});
</script>
```

---

## 2. ✏️ UPDATE Form (Edit User, Update Settings, etc.)

```html
<form id="edit-form" class="space-y-4">
    <input type="text" name="name" value="{{ $user->name }}" class="form-input">
    <input type="email" name="email" value="{{ $user->email }}" class="form-input">
    <button type="submit">Update</button>
</form>

<div id="edit-form-message" class="hidden"></div>

<script>
document.getElementById('edit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const payload = Object.fromEntries(formData.entries());
    
    try {
        const response = await api.put('{{ route('admin.users.update', $user->id) }}', payload);
        
        apiHelpers.showSuccessMessage('Updated successfully!');
        
    } catch (error) {
        apiHelpers.displayApiError(error, 'edit-form-message', 'edit-form');
    }
});
</script>
```

---

## 3. 🗑️ DELETE Button (Delete Record)

```html
<button id="delete-btn-{{ $id }}" class="btn-danger" data-url="{{ route('admin.users.destroy', $id) }}">
    Delete
</button>

<script>
document.getElementById('delete-btn-{{ $id }}').addEventListener('click', async function() {
    const url = this.dataset.url;
    
    try {
        await apiHelpers.confirmDelete(url, (response) => {
            apiHelpers.showSuccessMessage('Deleted successfully!');
            
            // Remove row from table or reload
            this.closest('tr')?.remove();
            // OR: location.reload();
        }, 'Are you sure you want to delete this user?');
        
    } catch (error) {
        // Error is already handled by confirmDelete
        console.error('Delete failed');
    }
});
</script>
```

---

## 4. 🔄 TOGGLE Status (Active/Inactive, Published/Draft)

```html
<input type="checkbox" 
       id="status-toggle-{{ $id }}" 
       {{ $item->is_active ? 'checked' : '' }}
       data-url="{{ route('admin.items.toggle', $item->id) }}"
       data-status="{{ $item->is_active ? 'true' : 'false' }}">
<label for="status-toggle-{{ $id }}">Active</label>

<script>
document.getElementById('status-toggle-{{ $id }}').addEventListener('change', async function() {
    const url = this.dataset.url;
    const currentStatus = this.dataset.status === 'true';
    
    try {
        const response = await apiHelpers.toggleStatus(url, currentStatus, 'is_active');
        
        // Update data attribute
        this.dataset.status = (!currentStatus).toString();
        
        apiHelpers.showSuccessMessage('Status updated!');
        
    } catch (error) {
        // Revert checkbox on error
        this.checked = currentStatus;
        alert('Failed to update status');
    }
});
</script>
```

---

## 5. 🔍 SEARCH with Debounce

```html
<input type="search" id="search-input" placeholder="Search..." class="form-input">
<div id="search-results"></div>
<div id="search-loader" class="hidden">Searching...</div>

<script>
const searchInput = document.getElementById('search-input');
const resultsDiv = document.getElementById('search-results');
const loader = document.getElementById('search-loader');

searchInput.addEventListener('input', async (e) => {
    const query = e.target.value.trim();
    
    if (query.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    try {
        loader.classList.remove('hidden');
        
        const results = await apiHelpers.debouncedSearch(
            '{{ route('admin.search') }}',
            query,
            300 // debounce delay
        );
        
        // Display results
        if (results.data.length > 0) {
            resultsDiv.innerHTML = results.data.map(item => `
                <div class="result-item">
                    <a href="/admin/items/${item.id}">${item.name}</a>
                </div>
            `).join('');
        } else {
            resultsDiv.innerHTML = '<p>No results found</p>';
        }
        
    } catch (error) {
        console.error('Search failed:', error);
        resultsDiv.innerHTML = '<p>Search failed</p>';
    } finally {
        loader.classList.add('hidden');
    }
});
</script>
```

---

## 6. 📄 PAGINATION

```html
<div id="data-container"></div>
<div id="pagination-controls"></div>

<script>
let currentPage = 1;
const itemsPerPage = 10;

async function loadData(page = 1) {
    try {
        const data = await apiHelpers.fetchPaginated(
            '{{ route('admin.users.index') }}',
            page,
            itemsPerPage,
            { 
                search: searchQuery, // optional filters
                sort: 'created_at'
            }
        );
        
        // Display data
        displayData(data.data);
        
        // Update pagination
        updatePagination(data.current_page, data.last_page);
        
    } catch (error) {
        console.error('Failed to load data:', error);
    }
}

function displayData(items) {
    const container = document.getElementById('data-container');
    container.innerHTML = items.map(item => `
        <div class="item">${item.name}</div>
    `).join('');
}

function updatePagination(current, total) {
    const controls = document.getElementById('pagination-controls');
    let html = '';
    
    for (let i = 1; i <= total; i++) {
        html += `<button onclick="loadData(${i})" ${i === current ? 'disabled' : ''}>${i}</button>`;
    }
    
    controls.innerHTML = html;
}

// Initial load
loadData(1);
</script>
```

---

## 7. 📤 FILE UPLOAD with Progress

```html
<form id="upload-form">
    <input type="file" id="file-input" name="file" accept="image/*">
    <input type="text" name="title" placeholder="File title">
    <button type="submit">Upload</button>
</form>

<div id="progress-bar" class="hidden">
    <div id="progress-fill" style="width: 0%"></div>
    <span id="progress-text">0%</span>
</div>

<script>
document.getElementById('upload-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const fileInput = document.getElementById('file-input');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a file');
        return;
    }
    
    const additionalData = {
        title: document.querySelector('input[name="title"]').value
    };
    
    const progressBar = document.getElementById('progress-bar');
    const progressFill = document.getElementById('progress-fill');
    const progressText = document.getElementById('progress-text');
    
    try {
        progressBar.classList.remove('hidden');
        
        const response = await apiHelpers.uploadFile(
            '{{ route('admin.upload') }}',
            file,
            additionalData,
            (progress) => {
                progressFill.style.width = `${progress}%`;
                progressText.textContent = `${progress}%`;
            }
        );
        
        apiHelpers.showSuccessMessage('File uploaded successfully!');
        
        // Reset form
        e.target.reset();
        progressBar.classList.add('hidden');
        
    } catch (error) {
        alert('Upload failed: ' + (error.response?.data?.message || 'Unknown error'));
        progressBar.classList.add('hidden');
    }
});
</script>
```

---

## 8. 💾 AUTO-SAVE (Draft functionality)

```html
<form id="draft-form">
    <textarea id="content" name="content" placeholder="Start typing..."></textarea>
    <span id="save-status" class="text-sm text-gray-500"></span>
</form>

<script>
const contentField = document.getElementById('content');
const saveStatus = document.getElementById('save-status');

contentField.addEventListener('input', async (e) => {
    saveStatus.textContent = 'Saving...';
    
    try {
        await apiHelpers.autoSave(
            '{{ route('admin.drafts.save', $draftId) }}',
            {
                content: e.target.value,
                updated_at: new Date().toISOString()
            },
            1000 // 1 second debounce
        );
        
        saveStatus.textContent = 'Saved ✓';
        
        setTimeout(() => {
            saveStatus.textContent = '';
        }, 2000);
        
    } catch (error) {
        saveStatus.textContent = 'Save failed ✗';
        console.error('Auto-save failed:', error);
    }
});
</script>
```

---

## 9. 🔢 BATCH OPERATIONS

```html
<button id="batch-action-btn">Process Selected Items</button>

<script>
document.getElementById('batch-action-btn').addEventListener('click', async () => {
    const selectedIds = getSelectedIds(); // Your function to get selected IDs
    
    if (selectedIds.length === 0) {
        alert('Please select at least one item');
        return;
    }
    
    const requests = selectedIds.map(id => ({
        method: 'patch',
        url: `/admin/items/${id}`,
        data: { status: 'processed' }
    }));
    
    try {
        const results = await apiHelpers.batchRequests(requests);
        
        apiHelpers.showSuccessMessage(`${results.length} items processed successfully!`);
        location.reload();
        
    } catch (error) {
        alert('Batch operation failed');
        console.error(error);
    }
});
</script>
```

---

## 10. 🔐 FORM WITH VALIDATION (Using JustValidate)

```html
<form id="validated-form" class="space-y-4">
    <div>
        <input type="email" name="email" placeholder="Email" class="form-input">
        <div class="email-error text-red-500 text-sm"></div>
    </div>
    
    <div>
        <input type="password" name="password" placeholder="Password" class="form-input">
        <div class="password-error text-red-500 text-sm"></div>
    </div>
    
    <button type="submit">Submit</button>
</form>

<div id="validated-form-message" class="hidden"></div>

<script>
const validation = new JustValidate('#validated-form');

validation
    .addField('input[name="email"]', [
        { rule: 'required', errorMessage: 'Email is required' },
        { rule: 'email', errorMessage: 'Must be a valid email' }
    ], {
        errorsContainer: '.email-error'
    })
    .addField('input[name="password"]', [
        { rule: 'required', errorMessage: 'Password is required' },
        { rule: 'minLength', value: 8, errorMessage: 'Minimum 8 characters' }
    ], {
        errorsContainer: '.password-error'
    })
    .onSuccess(async (event) => {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const payload = Object.fromEntries(formData.entries());
        
        try {
            const response = await api.post('{{ route('admin.submit') }}', payload);
            
            apiHelpers.showSuccessMessage('Form submitted successfully!');
            
        } catch (error) {
            apiHelpers.displayApiError(error, 'validated-form-message', 'validated-form');
        }
    });
</script>
```

---

## 🎨 Styling Classes Reference

Add these utility classes to your error/success containers:

```css
/* Error container */
.error-container {
    @apply hidden relative mt-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded;
}

/* Success container */
.success-container {
    @apply hidden mt-4 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded;
}

/* Loading spinner */
.loader {
    @apply inline-block w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin;
}
```

---

## 📝 Template Structure

Use this structure for consistent implementation:

```html
<!-- 1. Form -->
<form id="my-form">
    <!-- form fields -->
</form>

<!-- 2. Error container -->
<div id="my-form-message" class="error-container"></div>

<!-- 3. Success container -->
<div id="success-message" class="success-container"></div>

<!-- 4. Script -->
@push('script')
<script>
    // Your implementation here
</script>
@endpush
```

---

These examples cover 90% of common use cases. Mix and match as needed!

