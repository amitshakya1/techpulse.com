@props([
    'href' => '',
    'text' => '',
])
<a href="{{ $href }}"
    class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">{{ $text }}</a>
