<x-www.app-layout title="Dashboard" description="..." keywords="web, dashboard" author="Amit Shakya">
    <x-slot name="header">
        <h2>web Dashboard</h2>
    </x-slot>

    <div>
        Welcome to the web Panel
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            // Your tracking code here
            console.log('Order placed tracking fired');
        </script>
    @endpush
</x-www.app-layout>
