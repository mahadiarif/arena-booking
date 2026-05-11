<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'method'       => ['required', 'in:cash,bkash,nagad,rocket,bank_transfer,cheque,credit'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'note'         => ['nullable', 'string', 'max:500'],
            'paid_at'      => ['nullable', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->has('amount')) {
                return;
            }

            /** @var Booking|null $booking */
            $booking = $this->route('booking');

            if (! $booking) {
                return;
            }

            $amount = (float) $this->input('amount');
            $due    = (float) $booking->due_amount;

            if ($amount > $due) {
                $v->errors()->add(
                    'amount',
                    "Payment amount (৳" . number_format($amount, 2) . ") exceeds the outstanding balance (৳" . number_format($due, 2) . ")."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Payment amount is required.',
            'amount.min'      => 'Payment amount must be greater than zero.',
            'method.required' => 'Please select a payment method.',
            'method.in'       => 'Invalid payment method selected.',
        ];
    }
}
