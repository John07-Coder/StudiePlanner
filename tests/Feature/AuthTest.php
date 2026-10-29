<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_and_password_is_hashed(): void
    {
        $this->post('/register', [
            'name' => 'Sanne', 'email' => 'Sanne@example.test',
            'password' => 'Veilig123!', 'password_confirmation' => 'Veilig123!',
        ])->assertRedirect('/dashboard');

        $user = User::first();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('sanne@example.test', $user->email);
        $this->assertTrue(Hash::check('Veilig123!', $user->password));
        $this->assertNotSame('Veilig123!', $user->password);
    }

    public function test_student_can_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'Veilig123!']);
        $this->post('/login', ['email' => $user->email, 'password' => 'Veilig123!'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_guests_cannot_access_any_private_page_or_change_data(): void
    {
        foreach (['/dashboard', '/tasks', '/tasks/create', '/tasks/1/edit', '/subjects', '/week'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        $this->post('/tasks', [])->assertRedirect('/login');
        $this->put('/tasks/1', [])->assertRedirect('/login');
        $this->patch('/tasks/1/complete')->assertRedirect('/login');
        $this->delete('/tasks/1')->assertRedirect('/login');
        $this->post('/subjects', [])->assertRedirect('/login');
        $this->put('/subjects/1', [])->assertRedirect('/login');
        $this->delete('/subjects/1')->assertRedirect('/login');
    }

    public function test_registration_validation_is_in_dutch(): void
    {
        $this->from('/register')->post('/register', [
            'name' => '', 'email' => 'fout', 'password' => 'kort', 'password_confirmation' => 'anders',
        ])->assertSessionHasErrors(['name', 'email', 'password']);
        // Neem de sessiecookie mee, zoals een browser bij de redirect doet.
        $this->withCookie(config('session.cookie'), session()->getId())->get('/register')->assertSee('Het veld naam is verplicht.')->assertSee('Vul een geldig e-mailadres in.');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_duplicate_email_is_rejected_case_insensitively(): void
    {
        User::factory()->create(['email' => 'student@example.test']);
        $this->post('/register', [
            'name' => 'Student', 'email' => 'STUDENT@example.test',
            'password' => 'Veilig123!', 'password_confirmation' => 'Veilig123!',
        ])->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_login_is_limited_after_five_failed_attempts(): void
    {
        $user = User::factory()->create(['password' => 'Veilig123!']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'Verkeerd123!'])
                ->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'Veilig123!'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->withCookie(config('session.cookie'), session()->getId())->get('/login')->assertSee('Te veel inlogpogingen.');
        $this->travel(61)->seconds();
        $this->post('/login', ['email' => $user->email, 'password' => 'Veilig123!'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_pages_and_redirects_work(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Welkom terug!');
        $this->get('/register')->assertOk()->assertSee('Maak je account');
        $this->actingAs(User::factory()->create())->get('/login')->assertRedirect('/dashboard');
    }

    public function test_array_email_is_rejected_without_a_server_error(): void
    {
        $this->post('/login', ['email' => ['ongeldig'], 'password' => 'Veilig123!'])->assertSessionHasErrors('email');
        $this->post('/register', ['email' => ['ongeldig']])->assertSessionHasErrors('email');
    }

    public function test_post_forms_require_a_csrf_token(): void
    {
        // Laravel slaat de CSRF-controle normaal over tijdens tests.
        $this->app['env'] = 'local';
        $this->post('/login', ['email' => 'student@example.test', 'password' => 'Veilig123!'])
            ->assertStatus(419)->assertSee('Je sessie is verlopen');
        $this->actingAs(User::factory()->create())->post('/subjects', ['name' => 'Onveilig verzoek'])
            ->assertStatus(419);
        $this->assertDatabaseCount('subjects', 0);
    }
}

