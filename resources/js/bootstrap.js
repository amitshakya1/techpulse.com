import axios from 'axios';
import api from './api';
import * as apiHelpers from './api-helpers';

// Make both axios and api instance globally available
window.axios = axios;
window.api = api;
window.apiHelpers = apiHelpers;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
