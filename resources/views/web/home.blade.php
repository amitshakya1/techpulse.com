<x-web.app-layout title="Dashboard" description="..." keywords="web, dashboard" author="Amit Shakya">
    <x-slot name="header">
        <h2>web Dashboard</h2>
    </x-slot>

    <div>
        Welcome to the web Panel
    </div>
    @push('script')
        <script>
            // Your tracking code here
            console.log('Order placed tracking fired');
        </script>
    @endpush
</x-web.app-layout>
