<?php

namespace App\Http\Requests;

use App\Enums\EventType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; //??
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:3'],
            'country' => ['required', 'string', 'max:3'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'event_type' => ['required', Rule::enum(EventType::class)],
            'start_at' => ['required', 'date', 'after:now'],
        ];
    }
}