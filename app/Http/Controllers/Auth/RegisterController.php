<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Recaptcha;
use App\Support\EmailedPasswordAccount;
use App\Support\EmailOtp;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Sign-up: name and email → emailed code → account created and its first password emailed.
 */
class RegisterController extends Controller
{
    private const PURPOSE = 'register';

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, EmailOtp $otp): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'g-recaptcha-response' => [new Recaptcha($request->ip())],
        ], [
            'email.unique' => 'There\'s already an account with this email. Sign in, or reset the password.',
        ]);

        $email = Str::lower($validated['email']);
        $otp->send(self::PURPOSE, $email, ['name' => trim($validated['name'])]);

        return redirect()->route('register.verify');
    }

    public function showVerify(EmailOtp $otp): View|RedirectResponse
    {
        if (! $otp->pending(self::PURPOSE)) {
            return redirect()->route('register');
        }

        return view('auth.verify-code', [
            'title' => 'Create an account',
            'steps' => ['Your details', 'Code', 'Password'],
            'maskedEmail' => $otp->maskedEmail(self::PURPOSE),
            'action' => route('register.verify'),
            'resendAction' => route('register.resend'),
            'resendIn' => $otp->resendIn(self::PURPOSE),
            'backHref' => route('register'),
        ]);
    }

    /**
     * The code matched: create the account with a random password and email it.
     */
    public function verify(Request $request, EmailOtp $otp, EmailedPasswordAccount $accounts): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']], ['code.*' => 'The code is 6 digits. Check your email again.']);

        if (! $otp->pending(self::PURPOSE)) {
            return redirect()->route('register');
        }

        $otp->verify(self::PURPOSE, $request->string('code')->toString());

        ['email' => $email, 'data' => ['name' => $name]] = $otp->pending(self::PURPOSE);

        if (User::where('email', $email)->exists()) {
            $otp->forget(self::PURPOSE);

            return redirect()->route('login')->with('notice', 'You already have an account. Sign in, or reset the password.');
        }

        try {
            $accounts->create($name, $email, UserRole::Student, emailVerified: true);
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'code' => 'We couldn\'t email your password just now. Please try again in a minute.',
            ]);
        }

        $otp->forget(self::PURPOSE);

        return redirect()->route('login')
            ->with('notice', "We've emailed your password to {$email}. Use it to sign in.")
            ->withInput(['email' => $email]);
    }

    public function resend(EmailOtp $otp): RedirectResponse
    {
        if (! $otp->pending(self::PURPOSE)) {
            return redirect()->route('register');
        }

        $otp->resend(self::PURPOSE);

        return back()->with('notice', 'We sent you a new code.');
    }
}
