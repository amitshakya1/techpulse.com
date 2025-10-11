<x-www.app-layout title="419 - Session Expired" description="CSRF Token Expired" keywords="error, 419" author="Amit Shakya">
    <x-slot name="header">
        <h2>419 - Session Expired</h2>
    </x-slot>

    <div class="container mt-5">
        <div class="text-center">
            <h1 class="display-1 fw-bold">419</h1>
            <p class="fs-3">
                <span class="text-warning">Session Expired</span>
            </p>
            <p class="lead">
                Your session has expired. Please refresh the page and try again.
            </p>
            <button onclick="location.reload();" class="btn btn-primary">Refresh Page</button>
            <a href="/" class="btn btn-secondary">Go to Homepage</a>
        </div>
    </div>

</x-www.app-layout>
