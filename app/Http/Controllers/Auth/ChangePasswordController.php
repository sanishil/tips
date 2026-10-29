<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * First sign-in with the emailed password: the account must choose its own before going further.
 */
class ChangePasswordController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->must_change_password) {
            return redirect()->route($user->role->dashboardRoute());
        }

        return view('auth.new-password', [
            'title' => 'Choose your password',
            'steps' => null,
            'heading' => 'Choose your own password',
            'intro' => 'You signed in with the password we emailed you. Pick one only you know.',
            'action' => route('password.change'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'password' => [
                'required', 'string', 'confirmed', Password::min(8),
                function (string $attribute, mixed $value, \Closure $fail) use ($user): void {
                    if (Hash::check($value, $user->password)) {
                        $fail('Choose a different password from the one we emailed you.');
                    }
                },
            ],
        ]);

        $user->forceFill([
            'password' => $request->string('password')->toString(),
            'must_change_password' => false,
        ])->save();

        return redirect()->route($user->role->dashboardRoute())->with('status', 'Your new password is saved.');
    }
}
