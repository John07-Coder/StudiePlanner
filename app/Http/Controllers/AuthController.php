<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->createUser($request->validated());
        Auth::login($user);
        $this->renewSession($request);

        return redirect()->route('dashboard')->with('success', 'Je account is aangemaakt. Welkom bij StudiePlanner!');
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $key = $this->getLoginLimitKey($request);
        $this->checkLoginLimit($key);
        $this->authenticate($request->validated(), $key);
        $this->resetLoginLimit($key);
        $this->renewSession($request);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $this->endSession($request);

        return redirect()->route('login')->with('success', 'Je bent uitgelogd. Tot de volgende keer!');
    }

    private function createUser(array $data): User
    {
        // De hashed-cast in User slaat het wachtwoord veilig op.
        return User::create($data);
    }

    private function getLoginLimitKey(LoginRequest $request): string
    {
        // De limiet geldt per combinatie van e-mailadres en IP-adres.
        return 'login:'.hash('sha256', $request->validated('email').'|'.$request->ip());
    }

    private function checkLoginLimit(string $key): void
    {
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Te veel inlogpogingen. Probeer het over '.RateLimiter::availableIn($key).' seconden opnieuw.',
            ]);
        }
    }

    private function authenticate(array $credentials, string $key): void
    {
        if (! Auth::attempt($credentials)) {
            $this->recordFailedLogin($key);
        }
    }

    private function recordFailedLogin(string $key): never
    {
        RateLimiter::hit($key, 60);

        throw ValidationException::withMessages(['email' => 'Het e-mailadres of wachtwoord klopt niet.']);
    }

    private function resetLoginLimit(string $key): void
    {
        RateLimiter::clear($key);
    }

    private function renewSession(Request $request): void
    {
        $request->session()->regenerate();
    }

    private function endSession(Request $request): void
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
