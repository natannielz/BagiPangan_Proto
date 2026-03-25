<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DonationStoreRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:4000'],
            'qty_portions' => ['required', 'integer', 'min:1', 'max:10000'],
            'location_district' => ['required', 'string', 'max:120'],
            'expiry_at' => ['required', 'date', 'after:now'],
            'photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}

