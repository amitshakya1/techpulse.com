<script src="https://cdn.jsdelivr.net/npm/just-validate@4.3.0/dist/just-validate.production.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.3/build/js/intlTelInput.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const phoneInputs = document.querySelectorAll(".phone-number-input");

        phoneInputs.forEach(input => {
            window.intlTelInput(input, {
                initialCountry: "in", // default country
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.3/build/js/utils.js"
            });
        });
    });
</script>
