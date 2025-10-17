<?php

namespace App\Http\Requests\Admin\Page;

use App\Http\Requests\BaseFormRequest;

class PageCreateRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'status' => 'required|in:active,draft,archived',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a title for the page.',
            'title.max' => 'The title may not be greater than 255 characters.',

            'slug.unique' => 'This slug is already in use. Please choose a different one.',
            'slug.max' => 'The slug may not exceed 255 characters.',

            'status.required' => 'Please select a status for the page.',
            'status.in' => 'The selected status is invalid. It must be active, draft, or archived.',
        ];
    }
}
