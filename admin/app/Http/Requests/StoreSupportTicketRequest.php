<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketRequest extends FormRequest
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
            'subject' => 'required|string|min:3|max:100|regex:/^[a-zA-Z0-9\s]+$/',
            'customer_name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string|min:10|max:100',
        ];
    }
}
