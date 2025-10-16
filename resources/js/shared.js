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

if (navigator.webdriver || !navigator.plugins.length) {
    document.body.innerHTML = "<h1>Access Denied</h1>";
    throw new Error("Headless browsers blocked");
}

// jQuery
// import $ from 'jquery';
// window.$ = window.jQuery = $; // Make globally available

// // jQuery Validation
// import 'jquery-validation';

// import JustValidate from 'just-validate';
// window.JustValidate = JustValidate;
