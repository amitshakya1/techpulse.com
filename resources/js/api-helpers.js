import api from './api';

/**
 * API Helper Utilities
 * Common patterns and reusable functions for API calls
 */

/**
 * Display API errors in a specific container
 * @param {Error} error - Axios error object
 * @param {string} containerId - ID of the error container element
 * @param {string} formName - Form name for close button ID
 */
export function displayApiError(error, containerId, formName = null) {
    const errorDiv = document.getElementById(containerId);
    if (!errorDiv) return;

    errorDiv.innerHTML = '';

    // Add close button if formName is provided
    if (formName) {
        errorDiv.innerHTML = `
            <button type="button" id="${formName}-btn" 
                class="absolute top-2 right-2 text-red-700 hover:text-red-900 font-bold text-xl">
                &times;
            </button>
        `;
    }

    if (error.response) {
        const { status, data } = error.response;
        errorDiv.classList.remove('hidden');

        let html = `<p class="font-semibold ${status === 422 ? 'mb-2' : ''}">${data.message || 'An error occurred.'}</p>`;

        // Display validation errors
        if (status === 422 && data.errors) {
            html += '<ul class="list-disc list-inside space-y-1">';
            for (const field in data.errors) {
                if (data.errors.hasOwnProperty(field)) {
                    data.errors[field].forEach(msg => {
                        html += `<li>${msg}</li>`;
                    });
                }
            }
            html += '</ul>';
        }

        errorDiv.innerHTML += html;

        // Attach close button event
        if (formName) {
            const closeBtn = document.getElementById(`${formName}-btn`);
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    errorDiv.classList.add('hidden');
                });
            }
        }
    } else if (error.request) {
        // Network error
        errorDiv.classList.remove('hidden');
        errorDiv.innerHTML += '<p class="font-semibold">Network error. Please check your connection.</p>';
    } else {
        // Other errors
        errorDiv.classList.remove('hidden');
        errorDiv.innerHTML += '<p class="font-semibold">Something went wrong. Please try again.</p>';
    }
}

/**
 * Handle form submission with validation and API call
 * @param {Event} event - Form submit event
 * @param {string} url - API endpoint URL
 * @param {Function} onSuccess - Success callback
 * @param {string} errorContainerId - Error container ID
 * @param {string} formName - Form name
 */
export async function handleFormSubmit(event, url, onSuccess, errorContainerId, formName) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const payload = Object.fromEntries(formData.entries());

    try {
        const response = await api.post(url, payload);
        if (onSuccess) {
            onSuccess(response);
        }
    } catch (error) {
        displayApiError(error, errorContainerId, formName);
    }
}

/**
 * Show success message
 * @param {string} message - Success message
 * @param {string} containerId - Container element ID
 * @param {number} duration - Duration in milliseconds (default: 3000)
 */
export function showSuccessMessage(message, containerId = 'success-message', duration = 3000) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = `
        <div class="bg-green-50 border border-green-400 text-green-800 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">${message}</span>
        </div>
    `;
    container.classList.remove('hidden');

    setTimeout(() => {
        container.classList.add('hidden');
        container.innerHTML = '';
    }, duration);
}

/**
 * Confirm and delete resource
 * @param {string} url - Delete endpoint URL
 * @param {Function} onSuccess - Success callback
 * @param {string} confirmMessage - Confirmation message
 */
export async function confirmDelete(url, onSuccess, confirmMessage = 'Are you sure you want to delete this item?') {
    if (!confirm(confirmMessage)) {
        return;
    }

    try {
        const response = await api.delete(url);
        if (onSuccess) {
            onSuccess(response);
        }
        return response;
    } catch (error) {
        console.error('Delete failed:', error);
        alert('Failed to delete. Please try again.');
        throw error;
    }
}

/**
 * Fetch paginated data
 * @param {string} url - API endpoint
 * @param {number} page - Page number
 * @param {number} limit - Items per page
 * @param {Object} filters - Additional filters
 */
export async function fetchPaginated(url, page = 1, limit = 10, filters = {}) {
    try {
        const response = await api.get(url, {
            params: {
                page,
                limit,
                ...filters
            }
        });
        return response.data;
    } catch (error) {
        console.error('Fetch failed:', error);
        throw error;
    }
}

/**
 * Upload file with progress tracking
 * @param {string} url - Upload endpoint
 * @param {File} file - File object
 * @param {Object} additionalData - Additional form data
 * @param {Function} onProgress - Progress callback
 */
export async function uploadFile(url, file, additionalData = {}, onProgress = null) {
    const formData = new FormData();
    formData.append('file', file);

    // Append additional data
    for (const key in additionalData) {
        if (additionalData.hasOwnProperty(key)) {
            formData.append(key, additionalData[key]);
        }
    }

    try {
        const response = await api.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress: (progressEvent) => {
                if (onProgress && progressEvent.total) {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    onProgress(percentCompleted);
                }
            }
        });
        return response.data;
    } catch (error) {
        console.error('Upload failed:', error);
        throw error;
    }
}

/**
 * Fetch resource with loading state
 * @param {string} url - API endpoint
 * @param {string} loaderId - Loading indicator element ID
 */
export async function fetchWithLoader(url, loaderId = 'loader') {
    const loader = document.getElementById(loaderId);

    try {
        if (loader) loader.classList.remove('hidden');
        const response = await api.get(url);
        return response.data;
    } catch (error) {
        console.error('Fetch failed:', error);
        throw error;
    } finally {
        if (loader) loader.classList.add('hidden');
    }
}

/**
 * Debounced search function
 * @param {string} url - Search endpoint
 * @param {string} query - Search query
 * @param {number} delay - Debounce delay in ms (default: 300)
 */
export const debouncedSearch = (() => {
    let timeoutId;
    return async (url, query, delay = 300) => {
        clearTimeout(timeoutId);

        return new Promise((resolve, reject) => {
            timeoutId = setTimeout(async () => {
                try {
                    const response = await api.get(url, {
                        params: { q: query }
                    });
                    resolve(response.data);
                } catch (error) {
                    reject(error);
                }
            }, delay);
        });
    };
})();

/**
 * Batch API requests
 * @param {Array} requests - Array of request configs [{method, url, data}]
 */
export async function batchRequests(requests) {
    try {
        const promises = requests.map(req => {
            const method = req.method || 'get';
            return api[method](req.url, req.data);
        });

        const responses = await Promise.all(promises);
        return responses.map(res => res.data);
    } catch (error) {
        console.error('Batch request failed:', error);
        throw error;
    }
}

/**
 * Toggle resource status (active/inactive, published/draft, etc.)
 * @param {string} url - API endpoint
 * @param {boolean} currentStatus - Current status
 * @param {string} field - Field name (default: 'status')
 */
export async function toggleStatus(url, currentStatus, field = 'status') {
    try {
        const response = await api.patch(url, {
            [field]: !currentStatus
        });
        return response.data;
    } catch (error) {
        console.error('Status toggle failed:', error);
        throw error;
    }
}

/**
 * Auto-save form data (for draft functionality)
 * @param {string} url - Save endpoint
 * @param {Object} data - Form data to save
 * @param {number} delay - Debounce delay in ms (default: 1000)
 */
export const autoSave = (() => {
    let timeoutId;
    return async (url, data, delay = 1000) => {
        clearTimeout(timeoutId);

        return new Promise((resolve, reject) => {
            timeoutId = setTimeout(async () => {
                try {
                    const response = await api.post(url, data);
                    console.log('Auto-saved');
                    resolve(response.data);
                } catch (error) {
                    console.error('Auto-save failed:', error);
                    reject(error);
                }
            }, delay);
        });
    };
})();

// Export the api instance as well for direct usage
export { api };

