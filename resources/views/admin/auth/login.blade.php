<x-admin.guest-layout>
    @php
        $formName = 'login-form';
    @endphp
    <div class="mb-5 sm:mb-8">
        <x-admin.heading.h1 title="Sign In" />
        <x-admin.p text="Enter your email and password to sign in!" />
    </div>
    <div>
        <x-admin.social-login />

        <form id="{{ $formName }}" class="space-y-5">
            <div>
                <x-admin.form.input label="Email" type="email" name="email" placeholder="info@gmail.com" />
            </div>
            <div>
                <x-admin.form.input label="Password" type="password" name="password"
                    placeholder="Please enter password" />
            </div>
            <div class="flex items-center justify-between">
                <x-admin.form.checkbox for="remember" name="remember" label="Keep me logged in" />
                <x-admin.link href="{{ route('admin.forgot-password') }}" text="Forgot password?" />

            </div>
            <div>
                <x-admin.form.button name="Sign In" />
            </div>
        </form>
        <div id="{{ $formName }}-message"
            class="hidden relative mt-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded"></div>
        <div id="success-message" class="hidden mt-4"></div>
        <div class="mt-5">
            <x-admin.p>Don't have an account?
                <x-admin.link href="{{ route('admin.register') }}" text="Sign Up" /> </x-admin.p>
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
                .addField('input[name="password"]', [{
                    rule: 'required',
                    errorMessage: 'Please enter password',
                }, ], {
                    errorsContainer: '.password-error' // <-- custom container
                })
                .onSuccess(async (event) => {
                    event.preventDefault(); // ✅ Prevent default form submission
                    const formData = new FormData(event.target);
                    const payload = Object.fromEntries(formData.entries());

                    try {

                        const response = await api.post('{{ route('admin.login') }}', payload);

                        // Show success message (optional)
                        apiHelpers.showSuccessMessage('Login successfully!  Redirecting...', 'success-message', 1000);

                        // Redirect after short delay
                        setTimeout(() => {
                            const redirectUrl = response.data.data?.redirect ||
                                "{{ route('admin.dashboard') }}";
                            window.location.href = redirectUrl;
                        }, 500);

                    } catch (error) {
                        // ✅ Use helper function for consistent error display
                        apiHelpers.displayApiError(error, '{{ $formName }}-message', '{{ $formName }}');
                    }
                });
        </script>
    @endpush

</x-admin.guest-layout>
