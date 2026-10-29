<?php

namespace App\Http\Requests;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->route('subject') !== null) {
            $this->user()->subjects()->findOrFail($this->route('subject'));
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', Rule::in(array_keys(Subject::COLORS))],
        ];
    }
}
