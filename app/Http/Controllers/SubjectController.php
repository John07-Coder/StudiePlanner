<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        return view('subjects.index', [
            'subjects' => $this->getSubjectsWithOpenTaskCount($request->user()),
        ]);
    }

    public function store(SubjectRequest $request)
    {
        $request->user()->subjects()->create($request->validated());

        return redirect()->route('subjects.index')->with('success', 'Het vak is toegevoegd.');
    }

    public function update(SubjectRequest $request, string $subject)
    {
        $this->findOwnSubject($request->user(), $subject)->update($request->validated());

        return redirect()->route('subjects.index')->with('success', 'Het vak is bijgewerkt.');
    }

    public function destroy(Request $request, string $subject)
    {
        $this->findOwnSubject($request->user(), $subject)->delete();

        return redirect()->route('subjects.index')->with('success', 'Het vak is verwijderd. Je taken zijn behouden.');
    }

    private function getSubjectsWithOpenTaskCount(User $user): Collection
    {
        return $user->subjects()->withCount([
            'tasks as open_tasks_count' => fn ($query) => $query->where('is_completed', false),
        ])->orderBy('name')->get();
    }

    private function findOwnSubject(User $user, string $id): Subject
    {
        return $user->subjects()->findOrFail($id);
    }
}
