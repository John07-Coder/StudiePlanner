<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeekRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1900-01-08', 'before_or_equal:2100-12-24'],
        ];
    }
}
