<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    public const TYPES = ['homework' => 'Huiswerk', 'exam' => 'Toets', 'project' => 'Project'];
    public const PRIORITIES = ['low' => 'Laag', 'medium' => 'Normaal', 'high' => 'Hoog'];

    protected $fillable = ['subject_id', 'title', 'description', 'type', 'priority', 'due_date', 'is_completed'];

    protected function casts(): array
    {
        return ['due_date' => 'date', 'is_completed' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
