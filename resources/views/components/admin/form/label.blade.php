@php
    $required = $required ?? false; // default to false if not provided
@endphp
<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400" for="{{ $label }}">
    {{ $label }}{!! $required ? '<span class="text-error-500">*</span>' : '' !!}
</label>
