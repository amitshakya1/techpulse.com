import axios from 'axios';

/**
 * Create a centralized Axios instance with default configuration
 */
const api = axios.create({
    baseURL: '/', // Laravel base URL
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true, // Important for Laravel Sanctum/Session-based auth
});

/**
 * Request Interceptor
 * Automatically attaches CSRF token to all requests
 */
api.interceptors.request.use(
    (config) => {
        // Automatically attach CSRF token if available in meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }

        // Optional: Add loading indicator here if needed
        // showLoadingSpinner();

        return config;
    },
    (error) => {
        // Handle request error
        console.error('Request Error:', error);
        return Promise.reject(error);
    }
);

/**
 * Response Interceptor
 * Handles common response scenarios globally
 */
api.interceptors.response.use(
    (response) => {
        // Optional: Hide loading indicator here if needed
        // hideLoadingSpinner();

        // You can add global success handling here if needed
        return response;
    },
    (error) => {
        // Optional: Hide loading indicator here if needed
        // hideLoadingSpinner();

        // Handle common error status codes globally
        if (error.response) {
            const { status, data } = error.response;

            switch (status) {
                case 401:
                    // Unauthorized - redirect to login
                    console.warn('Unauthorized access. Redirecting to login...');
                    // Uncomment if you want automatic redirect
                    // window.location.href = '/admin/login';
                    break;

                case 403:
                    // Forbidden
                    console.warn('Access forbidden:', data.message);
                    break;

                case 419:
                    // CSRF token mismatch - session expired
                    console.warn('Session expired. Please refresh the page.');
                    // Optional: Show a modal or notification
                    alert('Your session has expired. Please refresh the page.');
                    break;

                case 422:
                    // Validation error - let the caller handle this
                    console.warn('Validation error:', data.errors);
                    break;

                case 429:
                    // Too many requests
                    console.warn('Too many requests. Please slow down.');
                    break;

                case 500:
                    // Server error
                    console.error('Server error:', data);
                    break;

                case 503:
                    // Service unavailable (maintenance mode)
                    console.warn('Service temporarily unavailable.');
                    break;

                default:
                    console.error('API Error:', status, data);
            }
        } else if (error.request) {
            // Request was made but no response received (network error)
            console.error('Network error: No response received', error.request);
        } else {
            // Something else happened
            console.error('Error:', error.message);
        }

        return Promise.reject(error);
    }
);

export default api;

