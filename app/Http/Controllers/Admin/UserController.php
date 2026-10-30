<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\EmailedPasswordAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Admin → User accounts. Students sign themselves up; admins create tech and admin accounts here, and can
 * edit, send a new password to, deactivate or delete any account except their own.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('portal.users', [
            'portal' => 'admin',
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, EmailedPasswordAccount $accounts): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::enum(UserRole::class)->only(UserRole::staff())],
        ], [
            'email.unique' => 'There\'s already an account with this email.',
        ]);

        $email = Str::lower($validated['email']);

        try {
            $accounts->create(trim($validated['name']), $email, UserRole::from($validated['role']));
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'email' => 'We couldn\'t send the password email just now, so the account wasn\'t created. Please try again.',
            ]);
        }

        return redirect()->route('admin.users')->with('status', "Account created. The password is on its way to {$email}.");
    }

    /**
     * Change an account's name, email or role. Admins can't change their own role, so they can't lock themselves out.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge(['name' => Str::squish((string) $request->input('name'))]);

        $validated = $request->validateWithBag('updateUser', [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::enum(UserRole::class)],
        ], [
            'name.required' => 'Enter their name.',
            'email.required' => 'Enter a valid email address. It\'s also their username.',
            'email.email' => 'Enter a valid email address. It\'s also their username.',
            'email.unique' => 'There\'s already an account with this email.',
        ]);

        if ($user->is($request->user()) && $validated['role'] !== $user->role->value) {
            throw ValidationException::withMessages(['role' => 'You can\'t change your own role.'])->errorBag('updateUser');
        }

        $user->update(['name' => $validated['name'], 'email' => Str::lower($validated['email']), 'role' => $validated['role']]);

        return redirect()->route('admin.users')->with('status', "“{$user->name}” saved.");
    }

    public function sendNewPassword(Request $request, User $user, EmailedPasswordAccount $accounts): RedirectResponse
    {
        if ($user->is($request->user())) {
            return $this->refuse('Change your own password from My profile.');
        }

        try {
            $accounts->sendNewPassword($user);
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return $this->refuse('We couldn\'t send the email just now, so the password wasn\'t changed. Please try again.');
        }

        return redirect()->route('admin.users')->with('status', "A new password is on its way to {$user->email}.");
    }

    /**
     * A deactivated account can't sign in, and is signed out straight away. Nothing is deleted.
     */
    public function deactivate(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return $this->refuse('You can\'t deactivate your own account.');
        }

        $user->forceFill(['deactivated_at' => now(), 'remember_token' => Str::random(60)])->save();
        $user->signOutEverywhere();

        return redirect()->route('admin.users')->with('status', "“{$user->name}” deactivated.");
    }

    public function reactivate(User $user): RedirectResponse
    {
        $user->forceFill(['deactivated_at' => null])->save();

        return redirect()->route('admin.users')->with('status', "“{$user->name}” can sign in again.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return $this->refuse('You can\'t delete your own account.');
        }

        $user->signOutEverywhere();

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $user->delete();

        return redirect()->route('admin.users')->with('status', "“{$user->name}” deleted.");
    }

    private function refuse(string $message): RedirectResponse
    {
        return redirect()->route('admin.users')->with('error', $message);
    }
}
