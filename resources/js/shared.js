import './bootstrap';

// Import Bootstrap JavaScript
// import * as bootstrap from 'bootstrap';
// window.bootstrap = bootstrap;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.route = function (url) {
    window.location.href = url;
}

// jQuery
// import $ from 'jquery';
// window.$ = window.jQuery = $; // Make globally available

// // jQuery Validation
// import 'jquery-validation';

// import JustValidate from 'just-validate';
// window.JustValidate = JustValidate;
