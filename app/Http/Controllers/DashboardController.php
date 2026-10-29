<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('dashboard', [
            'stats' => $this->getStatistics($user),
            'todayTasks' => $this->getTodayTasks($user),
            'upcomingTasks' => $this->getUpcomingTasks($user),
            'overdueTasks' => $this->getOverdueTasks($user),
        ]);
    }

    private function getStatistics(User $user): array
    {
        return [
            'open' => $this->countOpenTasks($user),
            'today' => $this->countTodayTasks($user),
            'week' => $this->countWeekTasks($user),
            'completed' => $this->countCompletedTasks($user),
        ];
    }

    private function countOpenTasks(User $user): int
    {
        return $user->tasks()->where('is_completed', false)->count();
    }

    private function countTodayTasks(User $user): int
    {
        return $user->tasks()
            ->where('is_completed', false)
            ->whereDate('due_date', today())
            ->count();
    }

    private function countWeekTasks(User $user): int
    {
        return $user->tasks()
            ->where('is_completed', false)
            ->whereDate('due_date', '>=', today()->startOfWeek())
            ->whereDate('due_date', '<=', today()->endOfWeek())
            ->count();
    }

    private function countCompletedTasks(User $user): int
    {
        return $user->tasks()->where('is_completed', true)->count();
    }

    private function getTodayTasks(User $user): Collection
    {
        return $user->tasks()->with('subject')
            ->whereDate('due_date', today())
            ->orderBy('is_completed')->orderBy('id')->get();
    }

    private function getUpcomingTasks(User $user): Collection
    {
        return $user->tasks()->with('subject')
            ->where('is_completed', false)
            ->whereDate('due_date', '>', today())
            ->whereDate('due_date', '<=', today()->addDays(7))
            ->orderBy('due_date')->orderBy('id')->get();
    }

    private function getOverdueTasks(User $user): Collection
    {
        return $user->tasks()->with('subject')
            ->where('is_completed', false)
            ->whereDate('due_date', '<', today())
            ->orderBy('due_date')->orderBy('id')->get();
    }
}
