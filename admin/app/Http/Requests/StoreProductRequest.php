<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'price' => ['required', 'numeric'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'max:500'],
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
