<?php

namespace App\Http\Requests\Admin\Page;

use App\Http\Requests\BaseFormRequest;

class PageIndexRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100', // Items per page
            'page' => 'sometimes|integer|min:1',            // Current page
            'search' => 'sometimes|string|max:255',        // Search query
            'status' => 'sometimes|in:active,draft,archived', // Optional status filter
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'per_page.integer' => 'Items per page must be a number.',
            'per_page.min' => 'Items per page must be at least 1.',
            'per_page.max' => 'Items per page cannot exceed 100.',
            'page.integer' => 'Page number must be a number.',
            'page.min' => 'Page number must be at least 1.',
            'search.string' => 'Search value must be a string.',
            'search.max' => 'Search value cannot exceed 255 characters.',
            'status.in' => 'Status must be one of: active, draft, archived.',
        ];
    }
}
