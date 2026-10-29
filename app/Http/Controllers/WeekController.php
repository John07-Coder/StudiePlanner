<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeekRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as TaskCollection;
use Illuminate\Support\Collection;

class WeekController extends Controller
{
    public function index(WeekRequest $request)
    {
        $start = $this->getWeekStart($request->validated('date'));
        $end = $this->getWeekEnd($start);
        $tasks = $this->getWeekTasks($request->user(), $start, $end);

        return view('week', [
            'start' => $start,
            'end' => $end,
            'days' => $this->getWeekDays($start),
            'tasksByDay' => $this->groupTasksByDay($tasks),
        ]);
    }

    private function getWeekStart(?string $date): Carbon
    {
        return Carbon::parse($date ?? today()->toDateString())->startOfWeek(Carbon::MONDAY);
    }

    private function getWeekEnd(Carbon $start): Carbon
    {
        return $start->copy()->addDays(6);
    }

    private function getWeekDays(Carbon $start): array
    {
        $days = [];
        for ($day = 0; $day < 7; $day++) {
            $days[] = $start->copy()->addDays($day);
        }

        return $days;
    }

    private function getWeekTasks(User $user, Carbon $start, Carbon $end): TaskCollection
    {
        return $user->tasks()->with('subject')
            ->whereDate('due_date', '>=', $start)
            ->whereDate('due_date', '<=', $end)
            ->orderBy('is_completed')->orderBy('id')->get();
    }

    private function groupTasksByDay(TaskCollection $tasks): Collection
    {
        return $tasks->groupBy(fn ($task) => $task->due_date->toDateString());
    }
}
