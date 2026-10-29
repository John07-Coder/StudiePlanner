<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskFilterRequest;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(TaskFilterRequest $request)
    {
        return view('tasks.index', [
            'tasks' => $this->getFilteredTasks($request->user(), $request->validated()),
            'subjects' => $this->getSubjects($request->user()),
        ]);
    }

    public function create(Request $request)
    {
        return view('tasks.form', [
            'task' => new Task,
            'subjects' => $this->getSubjects($request->user()),
        ]);
    }

    public function store(TaskRequest $request)
    {
        $request->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Je taak is toegevoegd.');
    }

    public function edit(Request $request, string $task)
    {
        return view('tasks.form', [
            'task' => $this->findOwnTask($request->user(), $task),
            'subjects' => $this->getSubjects($request->user()),
        ]);
    }

    public function update(TaskRequest $request, string $task)
    {
        $this->findOwnTask($request->user(), $task)->update($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Je taak is bijgewerkt.');
    }

    public function destroy(Request $request, string $task)
    {
        $this->findOwnTask($request->user(), $task)->delete();

        return redirect()->route('tasks.index')->with('success', 'Je taak is verwijderd.');
    }

    public function toggleComplete(Request $request, string $task)
    {
        $task = $this->findOwnTask($request->user(), $task);
        $this->toggleTaskStatus($task);

        return back()->with('success', $task->is_completed ? 'Goed bezig! Je taak is afgerond.' : 'Je taak staat weer open.');
    }

    private function findOwnTask(User $user, string $id): Task
    {
        // Ook een gewijzigd URL-ID blijft beperkt tot de eigen taken.
        return $user->tasks()->findOrFail($id);
    }

    private function getSubjects(User $user): Collection
    {
        return $user->subjects()->orderBy('name')->get();
    }

    private function getFilteredTasks(User $user, array $filters): LengthAwarePaginator
    {
        $query = $user->tasks()->with('subject');
        $this->filterByStatus($query, $filters['status'] ?? null);
        $this->filterByType($query, $filters['type'] ?? null);
        $this->filterBySubject($query, $filters['subject_id'] ?? null);

        return $this->paginateTasks($query);
    }

    private function filterByStatus(HasMany $query, ?string $status): void
    {
        if (! empty($status)) {
            $query->where('is_completed', $status === 'completed');
        }
    }

    private function filterByType(HasMany $query, ?string $type): void
    {
        if (! empty($type)) {
            $query->where('type', $type);
        }
    }

    private function filterBySubject(HasMany $query, int|string|null $subjectId): void
    {
        if (! empty($subjectId)) {
            $query->where('subject_id', $subjectId);
        }
    }

    private function paginateTasks(HasMany $query): LengthAwarePaginator
    {
        return $query->orderBy('is_completed')->orderBy('due_date')->orderBy('id')
            ->paginate(12)->withQueryString();
    }

    private function toggleTaskStatus(Task $task): void
    {
        $task->update(['is_completed' => ! $task->is_completed]);
    }
}
