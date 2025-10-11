import './bootstrap';

// Import Bootstrap JavaScript
// import * as bootstrap from 'bootstrap';
// window.bootstrap = bootstrap;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// jQuery
// import $ from 'jquery';
// window.$ = window.jQuery = $; // Make globally available

// // jQuery Validation
// import 'jquery-validation';

document.addEventListener('alpine:init', () => {
    Alpine.data('formValidator', (options = {}) => ({
        fields: options.fields || {},
        errors: {},

        init() {
            // Initialize errors
            for (let field in this.fields) {
                this.errors[field] = '';
            }
        },

        validateField(field) {
            const rules = this.fields[field];
            const value = this[field];

            this.errors[field] = '';

            if (rules.required && !value) {
                this.errors[field] = 'This field is required';
                return false;
            }

            if (rules.min && value.length < rules.min) {
                this.errors[field] = `Minimum ${rules.min} characters required`;
                return false;
            }

            if (rules.email) {
                const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
                if (!emailPattern.test(value)) {
                    this.errors[field] = 'Invalid email address';
                    return false;
                }
            }

            return true;
        },

        validateForm() {
            let valid = true;
            for (let field in this.fields) {
                if (!this.validateField(field)) valid = false;
            }
            return valid;
        },

        submitForm(event) {
            event.preventDefault();
            if (this.validateForm()) {
                this.$el.submit(); // or call API
            }
        }
    }))
})

