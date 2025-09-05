<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'status' => ['sometimes', 'in:open,in_progress,resolved,closed'],
            'category' => ['sometimes', 'nullable', 'in:billing,technical,account,shipping,sales,other'],
            'note' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'A subject is required.',
            'body.required' => 'The body of the ticket is required.',
        ];
    }
}
