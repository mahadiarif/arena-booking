<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\Customer;
use App\Models\Slot;
use App\Services\BookingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'              => ['required', 'integer', 'exists:customers,id'],
            'venue_id'                 => ['required', 'integer', 'exists:venues,id'],
            'slot_id'                  => ['required', 'integer', 'exists:slots,id'],
            'total_amount'             => ['required', 'numeric', 'min:0'],
            'initial_payment'          => ['nullable', 'numeric', 'min:0', 'lte:total_amount'],
            'payment_method'           => ['required_with:initial_payment', 'in:cash,bkash,nagad,rocket,bank_transfer,cheque,credit'],
            'payment_reference'        => ['nullable', 'string', 'max:100'],
            'notes'                    => ['nullable', 'string', 'max:1000'],

            // Participants
            'participants'             => ['nullable', 'array', 'max:50'],
            'participants.*.name'      => ['required_with:participants', 'string', 'max:150'],
            'participants.*.phone'     => ['nullable', 'string', 'max:20'],

            // Recurrence
            'is_recurring'             => ['nullable', 'boolean'],
            'recurrence_type'          => ['required_if:is_recurring,true', 'in:daily,weekly,monthly'],
            'recurrence_interval'      => ['nullable', 'integer', 'min:1', 'max:12'],
            'recurrence_days_of_week'  => ['nullable', 'array'],
            'recurrence_end_type'      => ['required_if:is_recurring,true', 'in:on_date,after_count'],
            'recurrence_end_date'      => ['required_if:recurrence_end_type,on_date', 'date', 'after:today'],
            'recurrence_end_after_count' => ['required_if:recurrence_end_type,after_count', 'integer', 'min:2', 'max:52'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // Skip cross-validation if basic rules already failed
            if ($v->errors()->hasAny(['venue_id', 'slot_id', 'customer_id', 'initial_payment'])) {
                return;
            }

            $slot = Slot::find($this->integer('slot_id'));

            // 1. Slot must belong to venue
            if ($slot && (int) $slot->venue_id !== $this->integer('venue_id')) {
                $v->errors()->add('slot_id', 'The selected slot does not belong to this venue.');
            }

            // 2. Availability check via service
            $available = app(BookingService::class)
                ->checkAvailability($this->integer('venue_id'), $this->integer('slot_id'));

            if (! $available) {
                $v->errors()->add('slot_id', 'This slot is no longer available. Please choose another time.');
            }

            // 3. Credit balance check
            if ($this->input('payment_method') === PaymentMethod::Credit->value) {
                $customer = Customer::find($this->integer('customer_id'));
                $payment  = (float) ($this->input('initial_payment') ?? 0);

                if ($customer && ! $customer->isCreditSufficient($payment)) {
                    $v->errors()->add(
                        'initial_payment',
                        "Insufficient credit balance. Customer has ৳" . number_format($customer->credit_balance, 2) . " available."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer_id.required'                  => 'Please select a customer.',
            'customer_id.exists'                    => 'The selected customer does not exist.',
            'venue_id.required'                     => 'Please select a venue.',
            'venue_id.exists'                       => 'The selected venue does not exist.',
            'slot_id.required'                      => 'Please select a time slot.',
            'slot_id.exists'                        => 'The selected slot does not exist.',
            'total_amount.required'                 => 'Total amount is required.',
            'total_amount.min'                      => 'Total amount cannot be negative.',
            'initial_payment.min'                   => 'Payment amount cannot be negative.',
            'initial_payment.lte'                   => 'Initial payment cannot exceed the total amount.',
            'payment_method.required_with'          => 'Please select a payment method.',
            'payment_method.in'                     => 'Invalid payment method selected.',
            'participants.max'                      => 'A booking can have at most 50 participants.',
            'participants.*.name.required_with'     => 'Each participant must have a name.',
            'recurrence_type.required_if'           => 'Please select a recurrence type.',
            'recurrence_end_type.required_if'       => 'Please specify when the recurrence should end.',
            'recurrence_end_date.required_if'       => 'Please provide an end date for the recurrence.',
            'recurrence_end_date.after'             => 'Recurrence end date must be in the future.',
            'recurrence_end_after_count.required_if'=> 'Please specify how many occurrences.',
            'recurrence_end_after_count.min'        => 'Recurrence must have at least 2 occurrences.',
            'recurrence_end_after_count.max'        => 'Recurrence cannot exceed 52 occurrences.',
        ];
    }
}
