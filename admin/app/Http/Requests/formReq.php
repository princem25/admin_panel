<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class formReq extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'price' => ['required', 'numeric'],
            'description' => ['required', 'max:500'],
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ];
    }
}
