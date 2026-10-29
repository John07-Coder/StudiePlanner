<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlannerTest extends TestCase
{
    use RefreshDatabase;

    private function taskData(array $changes = []): array
    {
        return array_merge([
            'title' => 'PHP-opdracht', 'description' => 'Hoofdstuk 3',
            'subject_id' => null, 'type' => 'homework', 'priority' => 'medium',
            'due_date' => '2026-10-28',
        ], $changes);
    }

    public function test_student_can_create_update_and_delete_a_subject_without_losing_tasks(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user)->post('/subjects', ['name' => 'PHP', 'color' => 'blue', 'user_id' => $other->id])
            ->assertRedirect('/subjects');
        $subject = $user->subjects()->firstOrFail();
        $this->put("/subjects/{$subject->id}", ['name' => 'Programmeren', 'color' => 'green'])
            ->assertRedirect('/subjects');
        $task = $user->tasks()->create($this->taskData(['subject_id' => $subject->id]));
        $this->get('/subjects')->assertOk()->assertSee('Programmeren')->assertSee('1 openstaand');
        $this->delete("/subjects/{$subject->id}")->assertRedirect('/subjects');
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'subject_id' => null]);
    }

    public function test_student_can_create_a_task_with_their_subject_and_cannot_inject_owner_or_status(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $subject = $user->subjects()->create(['name' => 'PHP']);
        $this->actingAs($user)->post('/tasks', $this->taskData([
            'subject_id' => $subject->id, 'user_id' => $other->id, 'is_completed' => true,
        ]))->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'title' => 'PHP-opdracht', 'user_id' => $user->id, 'subject_id' => $subject->id, 'is_completed' => false,
        ]);
    }

    public function test_student_can_edit_their_task_and_remove_its_subject(): void
    {
        $user = User::factory()->create();
        $subject = $user->subjects()->create(['name' => 'PHP']);
        $task = $user->tasks()->create($this->taskData(['subject_id' => $subject->id]));
        $this->actingAs($user)->get("/tasks/{$task->id}/edit")->assertOk()->assertSee('PHP-opdracht');
        $this->put("/tasks/{$task->id}", $this->taskData(['title' => 'Aangepast', 'type' => 'exam']))
            ->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Aangepast', 'type' => 'exam', 'subject_id' => null]);
    }

    public function test_student_can_complete_and_reopen_a_task(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create($this->taskData());
        $this->actingAs($user)->from('/week')->patch("/tasks/{$task->id}/complete")->assertRedirect('/week');
        $this->assertTrue($task->fresh()->is_completed);
        $this->patch("/tasks/{$task->id}/complete")->assertRedirect();
        $this->assertFalse($task->fresh()->is_completed);
    }

    public function test_student_can_delete_their_task(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create($this->taskData());
        $this->actingAs($user)->delete("/tasks/{$task->id}")->assertRedirect('/tasks');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_foreign_tasks_cannot_be_viewed_edited_completed_or_deleted(): void
    {
        $owner = User::factory()->create();
        $task = $owner->tasks()->create($this->taskData(['title' => 'Geheime taak']));
        $this->actingAs(User::factory()->create());
        $this->get("/tasks/{$task->id}/edit")->assertNotFound();
        $this->put("/tasks/{$task->id}", $this->taskData())->assertNotFound();
        $this->put("/tasks/{$task->id}", ['title' => ''])->assertNotFound();
        $this->patch("/tasks/{$task->id}/complete")->assertNotFound();
        $this->delete("/tasks/{$task->id}")->assertNotFound();
        foreach (['/dashboard', '/tasks', '/week?date=2026-10-28'] as $url) {
            $this->get($url)->assertOk()->assertDontSee('Geheime taak');
        }
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Geheime taak', 'is_completed' => false]);
    }

    public function test_foreign_subjects_cannot_be_listed_modified_deleted_or_attached(): void
    {
        $owner = User::factory()->create();
        $subject = $owner->subjects()->create(['name' => 'Geheim vak', 'color' => 'blue']);
        $user = User::factory()->create();
        $task = $user->tasks()->create($this->taskData());
        $this->actingAs($user)->get('/subjects')->assertDontSee('Geheim vak');
        $this->get('/tasks/create')->assertDontSee('Geheim vak');
        $this->put("/subjects/{$subject->id}", ['name' => 'Overgenomen'])->assertNotFound();
        $this->put("/subjects/{$subject->id}", ['name' => ''])->assertNotFound();
        $this->delete("/subjects/{$subject->id}")->assertNotFound();
        $this->post('/tasks', $this->taskData(['subject_id' => $subject->id]))->assertSessionHasErrors('subject_id');
        $this->put("/tasks/{$task->id}", $this->taskData(['subject_id' => $subject->id]))->assertSessionHasErrors('subject_id');
        $this->from('/tasks')->get("/tasks?subject_id={$subject->id}")->assertSessionHasErrors('subject_id');
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => 'Geheim vak']);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'subject_id' => null]);
        $this->assertDatabaseCount('tasks', 1);
    }

    public function test_invalid_task_fields_are_rejected_on_create_and_update(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create($this->taskData());
        $invalid = $this->taskData(['title' => '', 'type' => 'unknown', 'priority' => 'urgent', 'due_date' => '2026-02-30']);
        $this->actingAs($user)->post('/tasks', $invalid)->assertSessionHasErrors(['title', 'type', 'priority', 'due_date']);
        $this->put("/tasks/{$task->id}", $invalid)->assertSessionHasErrors(['title', 'type', 'priority', 'due_date']);
        $this->post('/subjects', ['name' => '', 'color' => 'url(evil)'])->assertSessionHasErrors(['name', 'color']);
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'PHP-opdracht']);
    }

    public function test_dashboard_statistics_and_date_boundaries_are_correct(): void
    {
        $this->travelTo(Carbon::parse('2026-10-28 12:00:00', 'Europe/Amsterdam'));
        $user = User::factory()->create();
        $today = $user->tasks()->create($this->taskData(['title' => 'Vandaag']));
        $doneToday = $user->tasks()->create($this->taskData(['title' => 'Vandaag klaar', 'is_completed' => true]));
        $overdue = $user->tasks()->create($this->taskData(['title' => 'Verlopen', 'due_date' => '2026-10-27']));
        $sunday = $user->tasks()->create($this->taskData(['title' => 'Zondag', 'due_date' => '2026-11-01']));
        $seventh = $user->tasks()->create($this->taskData(['title' => 'Zevende dag', 'due_date' => '2026-11-04']));
        $user->tasks()->create($this->taskData(['title' => 'Buiten bereik', 'due_date' => '2026-11-05']));
        $user->tasks()->create($this->taskData(['title' => 'Oud afgerond', 'due_date' => '2026-10-01', 'is_completed' => true]));
        User::factory()->create()->tasks()->create($this->taskData(['title' => 'Andermans taak']));
        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertViewHas('stats', ['open' => 5, 'today' => 1, 'week' => 3, 'completed' => 2])
            ->assertViewHas('todayTasks', fn ($tasks) => $tasks->pluck('id')->all() === [$today->id, $doneToday->id])
            ->assertViewHas('upcomingTasks', fn ($tasks) => $tasks->pluck('id')->all() === [$sunday->id, $seventh->id])
            ->assertViewHas('overdueTasks', fn ($tasks) => $tasks->pluck('id')->all() === [$overdue->id])
            ->assertDontSee('Andermans taak')->assertDontSee('Buiten bereik')->assertDontSee('Oud afgerond');
    }

    public function test_week_places_tasks_on_correct_days_and_navigates_across_year_boundary(): void
    {
        $this->travelTo(Carbon::parse('2026-12-30 10:00:00', 'Europe/Amsterdam'));
        $user = User::factory()->create();
        $monday = $user->tasks()->create($this->taskData(['title' => 'Maandagtaak', 'due_date' => '2026-12-28']));
        $sunday = $user->tasks()->create($this->taskData(['title' => 'Zondagtaak', 'due_date' => '2027-01-03']));
        $user->tasks()->create($this->taskData(['title' => 'Vorige week', 'due_date' => '2026-12-27']));
        $user->tasks()->create($this->taskData(['title' => 'Volgende week taak', 'due_date' => '2027-01-04']));
        $this->actingAs($user)->get('/week')->assertOk()
            ->assertViewHas('start', fn ($date) => $date->toDateString() === '2026-12-28')
            ->assertViewHas('days', fn ($days) => count($days) === 7 && $days[6]->toDateString() === '2027-01-03')
            ->assertViewHas('tasksByDay', fn ($groups) => $groups['2026-12-28']->first()->id === $monday->id && $groups['2027-01-03']->first()->id === $sunday->id)
            ->assertSeeInOrder(['data-date="2026-12-28"', 'Maandagtaak', 'data-date="2026-12-29"', 'data-date="2027-01-03"', 'Zondagtaak'], false)
            ->assertDontSee('Volgende week taak');
        $this->get('/week?date=2026-12-21')->assertOk()->assertSee('Vorige week')->assertDontSee('Maandagtaak');
        $this->get('/week?date=2027-01-04')->assertOk()->assertSee('Volgende week taak')->assertDontSee('Zondagtaak');
        $this->from('/week')->get('/week?date=ongeldig')->assertSessionHasErrors('date');
    }

    public function test_filters_sorting_and_pagination_work_and_keep_filter_values(): void
    {
        $user = User::factory()->create();
        $subject = $user->subjects()->create(['name' => 'Databases']);
        $first = $user->tasks()->create($this->taskData(['title' => 'Eerste', 'type' => 'exam', 'due_date' => '2026-10-01', 'subject_id' => $subject->id]));
        for ($i = 0; $i < 13; $i++) {
            $user->tasks()->create($this->taskData(['title' => 'Toets '.$i, 'type' => 'exam', 'subject_id' => $subject->id]));
        }
        $user->tasks()->create($this->taskData(['title' => 'Afgerond', 'is_completed' => true, 'due_date' => '2026-01-01']));
        $user->tasks()->create($this->taskData(['title' => 'Huiswerk']));
        $user->tasks()->create($this->taskData(['title' => 'Projecttaak', 'type' => 'project']));
        $url = "/tasks?status=open&type=exam&subject_id={$subject->id}";
        $this->actingAs($user)->get($url)->assertOk()
            ->assertViewHas('tasks', fn ($tasks) => $tasks->total() === 14 && $tasks->count() === 12 && $tasks->first()->id === $first->id)
            ->assertDontSee('Projecttaak')->assertSee('page=2', false)->assertSee('status=open', false);
        $this->get($url.'&page=2')->assertOk()->assertViewHas('tasks', fn ($tasks) => $tasks->count() === 2);
        $this->get('/tasks?status=completed')->assertOk()->assertSee('Afgerond')->assertDontSee('Projecttaak');
        $this->get('/tasks?type=project')->assertOk()->assertSee('Projecttaak')->assertDontSee('Toets 0');
        $this->get('/tasks?type=homework')->assertOk()->assertSee('Huiswerk')->assertDontSee('Projecttaak');
        $this->get('/tasks')->assertOk()->assertViewHas('tasks', fn ($tasks) => $tasks->first()->id === $first->id);
    }

    public function test_user_input_is_escaped_in_views(): void
    {
        $user = User::factory()->create();
        $user->tasks()->create($this->taskData(['title' => '<script>alert(1)</script>']));
        $this->actingAs($user)->get('/tasks')->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_empty_pages_and_task_form_render(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/dashboard')->assertOk()->assertSee('Je hebt vandaag geen taken. Goed bezig!');
        $this->get('/tasks')->assertOk()->assertSee('Een frisse start');
        $this->get('/tasks/create')->assertOk()->assertSee('Een nieuwe taak');
        $this->get('/subjects')->assertOk()->assertSee('Welke vakken volg jij?');
        $this->get('/week')->assertOk()->assertSee('Geen taken');
    }
}

