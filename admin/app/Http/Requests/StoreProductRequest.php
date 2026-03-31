<?php

namespace App\Http\Requests;

use App\Rules\ValidDiscount;
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
            'name'=> ['required', 'string', 'max:255', 'unique:products,name'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_price' => ['nullable', 'numeric', 'lt:price', new ValidDiscount],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['nullable', 'string'],
            'description' => ['required', 'max:500'],
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'name.unique' => 'Product name already exists',
            'discount_price.lt' => 'Discount must be less than price',
            'category_id.exists' => 'Invalid category selected',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'product name',
            'category_id' => 'category',
        ];
    }
}
