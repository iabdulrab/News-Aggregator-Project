<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class FetchArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search_query' => 'sometimes|string|max:255',
            'from_date' => 'sometimes|date|before_or_equal:to_date',
            'to_date' => 'sometimes|date|after_or_equal:from_date',
            'article_category' => 'sometimes|string|max:100',
            'source_key' => 'sometimes|string|max:150',
            'author_name' => 'sometimes|string|max:150',
            'sort_order' => 'sometimes|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'sort_order' => $this->input('sort_order', 'desc'),
            'per_page' => $this->input('per_page', 15)
        ]);
    }

    public function filters()
    {
        return $this->only([
            'search_query', 'from_date', 'to_date', 'article_category', 'source_key', 'author_name', 'sort_order', 'per_page'
        ]);
    }
}
