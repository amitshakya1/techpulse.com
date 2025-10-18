<x-admin.guest-layout>
    @php
        $formName = 'login-form';
    @endphp
    <div class="mb-5 sm:mb-8">
        <x-admin.heading.h1 title="Forgot Password" />
        <x-admin.p text="Enter your email to reset your password!" />
    </div>
    <div>
        <x-admin.social-login />

        <form id="{{ $formName }}" class="space-y-5">
            <div>
                <x-admin.form.input label="Email" type="email" name="email" placeholder="info@gmail.com" />
            </div>

            <div>
                <x-admin.form.button name="Send Reset Link" />
            </div>
        </form>
        <div id="{{ $formName }}-message"
            class="hidden relative mt-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded"></div>
        <div id="success-message" class="hidden mt-4"></div>
        <div class="mt-5">
            <x-admin.p>Back to login?
                <x-admin.link href="{{ route('admin.login') }}" text="Sign In" /> </x-admin.p>
        </div>
    </div>
    @push('script')
        <script>
            const validation = new JustValidate('#{{ $formName }}');

            validation
                .addField('input[name="email"]', [{
                        rule: 'required',
                        errorMessage: 'Please enter email',
                    },
                    {
                        rule: 'email',
                        errorMessage: 'Please enter a valid email',
                    },
                ], {
                    errorsContainer: '.email-error' // <-- custom container
                })
                .onSuccess(async (event) => {
                    event.preventDefault(); // ✅ Prevent default form submission
                    const formData = new FormData(event.target);
                    const payload = Object.fromEntries(formData.entries());

                    try {

                        const response = await api.post('{{ route('admin.forgot-password') }}', payload);

                        // Show success message (optional)
                        apiHelpers.showSuccessMessage(
                            'Reset link sent successfully!  Please check your email for the reset link.',
                            'success-message', 1000);

                    } catch (error) {
                        // ✅ Use helper function for consistent error display
                        apiHelpers.displayApiError(error, '{{ $formName }}-message', '{{ $formName }}');
                    }
                });
        </script>
    @endpush

</x-admin.guest-layout>
