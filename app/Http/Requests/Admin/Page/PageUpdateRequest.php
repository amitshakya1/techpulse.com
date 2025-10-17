<?php

namespace App\Http\Requests\Admin\Page;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class PageUpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($this->page->id ?? null),
            ],
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'status' => 'required|in:active,draft,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a title for the page.',
            'status.in' => 'Invalid status value. Allowed: active, draft, archived.',
        ];
    }
}
