<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Recaptcha;
use App\Support\EmailOtp;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Password reset: email → emailed code → new password.
 */
class ForgotPasswordController extends Controller
{
    private const PURPOSE = 'reset';

    private const STEPS = ['Email', 'Code', 'New password'];

    public function create(): View
    {
        return view('auth.forgot-password', ['steps' => self::STEPS]);
    }

    /**
     * Unknown emails go through the same steps without a code being sent, so the page never says who has an account.
     */
    public function store(Request $request, EmailOtp $otp): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'g-recaptcha-response' => [new Recaptcha($request->ip())],
        ]);

        $email = Str::lower($validated['email']);
        $otp->send(self::PURPOSE, $email, deliver: User::where('email', $email)->exists());

        return redirect()->route('password.verify');
    }

    public function showVerify(EmailOtp $otp): View|RedirectResponse
    {
        if (! $otp->pending(self::PURPOSE)) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-code', [
            'title' => 'Reset your password',
            'steps' => self::STEPS,
            'maskedEmail' => $otp->maskedEmail(self::PURPOSE),
            'action' => route('password.verify'),
            'resendAction' => route('password.resend'),
            'resendIn' => $otp->resendIn(self::PURPOSE),
            'backHref' => route('password.request'),
        ]);
    }

    public function verify(Request $request, EmailOtp $otp): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']], ['code.*' => 'The code is 6 digits. Check your email again.']);

        if (! $otp->pending(self::PURPOSE)) {
            return redirect()->route('password.request');
        }

        $otp->verify(self::PURPOSE, $request->string('code')->toString());

        return redirect()->route('password.reset');
    }

    public function resend(EmailOtp $otp): RedirectResponse
    {
        if (! $otp->pending(self::PURPOSE)) {
            return redirect()->route('password.request');
        }

        $otp->resend(self::PURPOSE);

        return back()->with('notice', 'We sent you a new code.');
    }

    public function edit(EmailOtp $otp): View|RedirectResponse
    {
        if (! $otp->isVerified(self::PURPOSE)) {
            return redirect()->route('password.request');
        }

        return view('auth.new-password', [
            'title' => 'Reset your password',
            'steps' => self::STEPS,
            'heading' => 'Choose a new password',
            'intro' => 'You\'ll be signed out on any other device.',
            'action' => route('password.reset'),
        ]);
    }

    /**
     * Save the new password and sign the account out everywhere else.
     */
    public function update(Request $request, EmailOtp $otp): RedirectResponse
    {
        if (! $otp->isVerified(self::PURPOSE)) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        $user = User::where('email', $otp->pending(self::PURPOSE)['email'])->first();
        $otp->forget(self::PURPOSE);

        if (! $user) {
            return redirect()->route('password.request');
        }

        $user->forceFill([
            'password' => $request->string('password')->toString(),
            'must_change_password' => false,
            'remember_token' => Str::random(60),
        ])->save();

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
        }

        return redirect()->route('login')
            ->with('notice', 'Your password has been changed. Sign in with the new one.')
            ->withInput(['email' => $user->email]);
    }
}
