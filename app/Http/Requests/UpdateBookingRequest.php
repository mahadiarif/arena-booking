<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $booking = $this->route('booking');
        return $this->user()->can('update', $booking);
    }

    public function rules(): array
    {
        $isAdmin = $this->user()->hasRole(['admin', 'super_admin']);

        $rules = [
            'notes'                => ['nullable', 'string', 'max:1000'],
            'participants'         => ['nullable', 'array', 'max:50'],
            'participants.*.name'  => ['required_with:participants', 'string', 'max:150'],
            'participants.*.phone' => ['nullable', 'string', 'max:20'],
            'participants.*.note'  => ['nullable', 'string', 'max:255'],
        ];

        // Admin-only fields
        if ($isAdmin) {
            $rules['total_amount'] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }
}
