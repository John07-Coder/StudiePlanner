@php($overdue = ! $task->is_completed && $task->due_date->lt(today()))
<article @class(['task-row', 'task-done' => $task->is_completed, 'task-overdue' => $overdue])>
    <form method="POST" action="{{ route('tasks.complete', $task) }}" class="complete-form">
        @csrf @method('PATCH')
        <button class="complete-button" aria-label="{{ $task->is_completed ? 'Opnieuw openzetten' : 'Afronden' }}: {{ $task->title }}" title="{{ $task->is_completed ? 'Opnieuw openzetten' : 'Afronden' }}">@if($task->is_completed)✓@endif</button>
    </form>
    <div class="task-info">
        <a class="task-title" href="{{ route('tasks.edit', $task) }}">{{ $task->title }}</a>
        <div class="task-meta"><span class="subject-label"><i class="subject-dot color-{{ $task->subject?->color ?? 'blue' }}"></i>{{ $task->subject?->name ?? 'Zonder vak' }}</span><span>·</span><span>{{ \App\Models\Task::TYPES[$task->type] }}</span></div>
    </div>
    <div class="task-details">
        <span class="badge priority-{{ $task->priority }}">{{ \App\Models\Task::PRIORITIES[$task->priority] }}</span>
        <span class="badge {{ $task->is_completed ? 'completed' : ($overdue ? 'overdue' : 'pending') }}">{{ $task->is_completed ? 'Afgerond' : 'Openstaand' }}</span>
        <time class="due-date {{ $overdue ? 'text-danger' : '' }}" datetime="{{ $task->due_date->toDateString() }}">{{ $task->due_date->format('d-m-Y') }}</time>
    </div>
</article>

