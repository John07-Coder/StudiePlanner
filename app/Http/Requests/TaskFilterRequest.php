<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(['open', 'completed'])],
            'type' => ['nullable', Rule::in(array_keys(Task::TYPES))],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')->where('user_id', $this->user()->id)],
        ];
    }
}
