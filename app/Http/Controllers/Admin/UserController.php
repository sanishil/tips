<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\EmailedPasswordAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Admin → User accounts. Students sign themselves up; admins create tech and admin accounts here.
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
}
