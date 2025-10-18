@props([
    'type' => 'submit',
    'name' => 'Submit',
])
<button type="{{ $type }}"
    class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
    <span>{{ $name }}</span>
</button>
