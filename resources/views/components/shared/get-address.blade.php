<input id="pin" placeholder="Enter PIN code"
    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
<div id="result"></div>

<script>
    const pinInput = document.getElementById('pin');
    const result = document.getElementById('result');
    let timer;

    pinInput.addEventListener('input', () => {
        clearTimeout(timer);
        const v = pinInput.value.trim();
        // basic validation for India: 6 digits
        if (!/^\d{6}$/.test(v)) {
            result.textContent = '';
            return;
        }
        timer = setTimeout(() => lookupIndiaPin(v), 400);
    });

    async function lookupIndiaPin(pin) {
        result.textContent = 'Loading...';
        try {
            const res = await fetch(`https://api.postalpincode.in/pincode/${pin}`);
            const data = await res.json();
            // API returns array; check status
            if (!Array.isArray(data) || data[0].Status !== 'Success') {
                result.textContent = 'No results found';
                return;
            }
            const offices = data[0].PostOffice;
            // show first office and district/state
            result.innerHTML = `
      <strong>PIN:</strong> ${pin}<br/>
      <strong>Post Offices:</strong> ${offices.map(o => o.Name).join(', ')}<br/>
      <strong>District:</strong> ${offices[0].District}<br/>
      <strong>State:</strong> ${offices[0].State}
    `;
        } catch (err) {
            console.error(err);
            result.textContent = 'Lookup failed';
        }
    }
</script>
