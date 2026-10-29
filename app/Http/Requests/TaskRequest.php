<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Controleer bij wijzigen het eigenaarschap vóór de formuliervalidatie.
        if ($this->route('task') !== null) {
            $this->user()->tasks()->findOrFail($this->route('task'));
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')->where('user_id', $this->user()->id)],
            'type' => ['required', Rule::in(array_keys(Task::TYPES))],
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITIES))],
            'due_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:1900-01-01', 'before_or_equal:2100-12-31'],
        ];
    }
}
