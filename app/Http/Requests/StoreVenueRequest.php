<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create venues') || $this->user()->can('edit venues');
    }

    public function rules(): array
    {
        $venueId = $this->route('venue')?->id;

        return [
            'name'                  => ['required', 'string', 'max:150'],
            'type'                  => ['required', 'string', 'in:stadium,turf_indoor,turf_outdoor,court,vip_box,other'],
            'capacity'              => ['nullable', 'integer', 'min:1'],
            'color'                 => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'hourly_rate'           => ['required', 'numeric', 'min:0'],
            'schedule_id'           => ['nullable', 'exists:schedules,id'],
            'min_duration_minutes'  => ['required', 'integer', 'in:60,90,120,180,240'],
            'max_duration_minutes'  => ['required', 'integer', 'in:120,180,240,360,480,720'],
            'requires_approval'     => ['nullable', 'boolean'],
            'is_active'             => ['nullable', 'boolean'],
            'sort_order'            => ['nullable', 'integer', 'min:0'],
            'description'           => ['nullable', 'string', 'max:2000'],
            'images'                => ['nullable', 'array', 'max:10'],
            'images.*'              => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'color.regex'          => 'Color must be a valid hex code (e.g. #3b82f6).',
            'images.*.max'         => 'Each image must be under 2MB.',
            'min_duration_minutes.in' => 'Min duration must be 60, 90, 120, 180, or 240 minutes.',
            'max_duration_minutes.in' => 'Max duration must be 120, 180, 240, 360, 480, or 720 minutes.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'requires_approval' => $this->boolean('requires_approval'),
            'is_active'         => $this->boolean('is_active', true),
        ]);
    }
}
