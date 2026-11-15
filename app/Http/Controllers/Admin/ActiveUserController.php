<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Admin → Signed in now: other accounts whose sign-in token was used in the last few minutes, read from the
 * auth_tokens table, and a way to sign one out.
 */
class ActiveUserController extends Controller
{
    /**
     * A session counts as "signed in now" if it was used within this many minutes.
     */
    public const ACTIVE_MINUTES = 10;

    public function index(Request $request): View
    {
        $lastActivity = AuthToken::query()
            ->where('user_id', '!=', $request->user()->id)
            ->where('expires_at', '>', now())
            ->where('last_used_at', '>=', now()->subMinutes(self::ACTIVE_MINUTES))
            ->selectRaw('user_id, max(last_used_at) as last_used_at')
            ->groupBy('user_id')
            ->pluck('last_used_at', 'user_id');

        $users = User::whereIn('id', $lastActivity->keys())->get()
            ->each(fn (User $user) => $user->setAttribute('last_active_at', Carbon::parse($lastActivity[$user->id])))
            ->sortByDesc('last_active_at')
            ->values();

        return view('portal.active-users', [
            'portal' => 'admin',
            'users' => $users,
        ]);
    }

    public function signOut(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return redirect()->route('admin.active-users')->with('error', 'Use Sign out at the top to sign yourself out.');
        }

        $user->forceFill(['remember_token' => Str::random(60)])->save();
        $user->signOutEverywhere();

        return redirect()->route('admin.active-users')->with('status', "“{$user->name}” has been signed out.");
    }
}
