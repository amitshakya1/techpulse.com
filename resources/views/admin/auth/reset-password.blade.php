<x-admin.guest-layout>
    @php
        $formName = 'reset-password-form';
    @endphp
    <div class="mb-5 sm:mb-8">
        <x-admin.heading.h1 title="Reset Password" />
        <x-admin.p text="Enter your new password and confirm it to reset your password!" />
    </div>
    <div>

        <form id="{{ $formName }}" class="space-y-5">
            <div>
                <x-admin.form.input label="New Password" type="password" name="password"
                    placeholder="Please enter new password" />
            </div>

            <div>
                <x-admin.form.input label="Confirm New Password" type="password" name="password_confirmation"
                    placeholder="Please enter new password" />
            </div>

            <div>
                <x-admin.form.button name="Sign In" />
            </div>
        </form>
        <div id="{{ $formName }}-message"
            class="hidden relative mt-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded"></div>
        <div id="success-message" class="hidden mt-4"></div>
    </div>
    @push('script')
        <script>
            const validation = new JustValidate('#{{ $formName }}');

            validation
                .addField('input[name="password"]', [{
                        rule: 'required',
                        errorMessage: 'Please enter new password',
                    },
                    {
                        rule: 'minLength',
                        value: 8,
                        errorMessage: 'Password must be at least 8 characters long',
                    },
                ])
                .addField('input[name="password_confirmation"]', [{
                        rule: 'required',
                        errorMessage: 'Please confirm your password',
                    },
                    {
                        validator: (value, fields) => {
                            // value = confirmation password
                            // fields['input[name="password"]'].elem.value = original password
                            return value === fields['input[name="password"]'].elem.value;
                        },
                        errorMessage: 'Passwords do not match',
                    },
                ])
                .onSuccess(async (event) => {
                    event.preventDefault(); // ✅ Prevent default form submission
                    const formData = new FormData(event.target);
                    const token = '{{ request()->route('token') }}';
                    const email = '{{ request()->get('email') }}';
                    formData.append('token', token);
                    formData.append('email', email);
                    const payload = Object.fromEntries(formData.entries());
                    try {

                        const response = await api.post(
                            '{{ route('admin.reset-password-action') }}',
                            payload);

                        // Show success message (optional)
                        apiHelpers.showSuccessMessage('Password reset successfully!  Redirecting to login...',
                            'success-message', 1000);

                        // Redirect after short delay
                        setTimeout(() => {
                            const redirectUrl = response.data.data?.redirect ||
                                "{{ route('admin.login') }}";
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
