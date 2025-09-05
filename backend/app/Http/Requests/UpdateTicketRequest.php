<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'in:open,in_progress,resolved,closed'],
            'category' => ['sometimes', 'nullable', 'in:billing,technical,account,shipping,sales,other'],
            'note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
