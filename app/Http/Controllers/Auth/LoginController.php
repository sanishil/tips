<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    private const LOCKOUT_SECONDS = 600;

    /**
     * Sign in, then open the account's portal, or the "choose a password" page if it's still on the emailed one.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => [new Recaptcha($request->ip())],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->string('email'))).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($throttleKey) / 60);

            throw ValidationException::withMessages([
                'email' => "Too many wrong attempts. Please wait {$minutes} ".Str::plural('minute', $minutes).' and try again.',
            ]);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);

            throw ValidationException::withMessages([
                'email' => 'That email and password don\'t match. Check both and try again.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        if ($request->user()->deactivated_at) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'This account has been deactivated. Ask an admin to turn it back on.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        return redirect()->intended(route($user->role->dashboardRoute()));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('notice', 'You\'ve signed out.');
    }
}
