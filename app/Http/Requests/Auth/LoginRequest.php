<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim((string) $this->string('login'));
        $password = (string) $this->string('password');
        $user = $this->resolveUser($login);

        if (! $user || ! Hash::check($password, $user->getAuthPassword())) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => __('These credentials do not match our records.'),
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    private function resolveUser(string $login): ?User
    {
        $emailMatch = User::query()->where('email', $login)->first();
        if ($emailMatch) {
            return $emailMatch;
        }

        // Allow unique name login for waiters and admin.
        $nameMatches = User::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($login)])
            ->get();

        if ($nameMatches->count() > 1) {
            throw ValidationException::withMessages([
                'login' => 'Multiple accounts share this name. Please sign in with your email instead.',
            ]);
        }

        if ($nameMatches->isNotEmpty()) {
            return $nameMatches->first();
        }

        $worker = Worker::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($login)])
            ->first();

        if ($worker && ! $worker->isLoginRole()) {
            throw ValidationException::withMessages([
                'login' => "{$worker->name} is registered as {$worker->role}, not Waiter. Ask admin to set the role to Waiter/Waitress.",
            ]);
        }

        if ($worker && $worker->isLoginRole() && ! $worker->user_id) {
            throw ValidationException::withMessages([
                'login' => 'This waiter has no login yet. Ask admin to open Workers and use Resend code.',
            ]);
        }

        return null;
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => __('Too many login attempts. Please try again in :seconds seconds.', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string) $this->string('login')).'|'.$this->ip());
    }
}
