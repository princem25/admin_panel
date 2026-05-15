<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller or middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'          => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'required|string|max:10',
            'admin_note'      => 'required|string|max:100',
            'history_note'    => 'required|string|max:100',
        ];
    }
}
