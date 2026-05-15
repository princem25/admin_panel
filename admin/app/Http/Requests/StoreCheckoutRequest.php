<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
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
            'full_name'        => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'phone'            => 'required|numeric|digits_between:10,15',
            'shipping_address' => 'required|string|min:5|max:100|regex:/^[a-zA-Z0-9\s]+$/',
            'payment_method'   => 'required|in:cod,card,upi',
            'notes'            => 'nullable|string|min:3|max:100|regex:/^[a-zA-Z0-9\s]+$/',
        ];
    }
}
