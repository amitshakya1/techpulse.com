<x-www.app-layout title="503 - Service Unavailable" description="Service Unavailable" keywords="error, 503"
    author="Amit Shakya">
    <x-slot name="header">
        <h2>503 - Service Unavailable</h2>
    </x-slot>

    <div class="container mt-5">
        <div class="text-center">
            <h1 class="display-1 fw-bold">503</h1>
            <p class="fs-3">
                <span class="text-warning">We'll be right back!</span>
            </p>
            <p class="lead">
                Our website is currently undergoing maintenance. Please check back in a few minutes.
            </p>
            <a href="/" class="btn btn-secondary" onclick="location.reload(); return false;">Retry</a>
        </div>
    </div>

</x-www.app-layout>
