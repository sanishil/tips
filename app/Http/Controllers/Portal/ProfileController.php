<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * My profile, in both portals (/admin/profile and /tech/profile): the signed-in user's own name,
 * password and photo. The email is their username, so only an admin can change it.
 */
class ProfileController extends Controller
{
    /**
     * Photos live on the public disk under this folder.
     */
    private const PHOTO_FOLDER = 'avatars';

    public function edit(Request $request): View
    {
        return view('portal.profile', [
            'portal' => $request->user()->role->value,
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->merge(['name' => Str::squish((string) $request->input('name'))]);

        $validated = $request->validateWithBag('updateProfile', [
            'name' => ['required', 'string', 'min:2', 'max:100', "regex:/^[\pL\pM][\pL\pM\s.'-]*$/u"],
        ], [
            'name.required' => 'Your name can\'t be empty.',
            'name.min' => 'That name is too short.',
            'name.max' => 'Keep your name under 100 characters.',
            'name.regex' => 'Use letters, spaces, full stops and hyphens only.',
        ]);

        $request->user()->update($validated);

        return $this->backToProfile($request, 'Your details are saved.');
    }

    /**
     * A new password signs this account out everywhere else, in case the old one was known to someone.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => [
                'required', 'string', 'confirmed', Password::min(8),
                function (string $attribute, mixed $value, \Closure $fail) use ($user): void {
                    if (Hash::check($value, $user->password)) {
                        $fail('Choose a password different from your current one.');
                    }
                },
            ],
        ], [
            'current_password.required' => 'Enter your current password.',
            'current_password.current_password' => 'That isn\'t your current password.',
            'password.required' => 'Enter a new password.',
            'password.confirmed' => 'The two passwords don\'t match.',
            'password.min' => 'Use at least 8 characters.',
        ]);

        $user->forceFill([
            'password' => $request->string('password')->toString(),
            'remember_token' => Str::random(60),
        ])->save();

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return $this->backToProfile($request, 'Password changed. You\'ve been signed out on other devices.');
    }

    /**
     * The page crops photos to passport size (35 × 45 mm) before sending them; anything else is turned away.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validateWithBag('updatePhoto', [
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:ratio=35/45'],
        ], [
            'photo.required' => 'Choose an image.',
            'photo.file' => 'Choose an image.',
            'photo.mimes' => 'Use a JPG, PNG or WebP image.',
            'photo.max' => 'That image is larger than 2 MB.',
            'photo.dimensions' => 'Crop the photo to passport size (35 × 45 mm) first.',
        ]);

        $user = $request->user();
        $oldPhoto = $user->photo_path;

        $user->forceFill(['photo_path' => $request->file('photo')->store(self::PHOTO_FOLDER, 'public')])->save();

        if ($oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return $this->backToProfile($request, 'Photo updated.');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
            $user->forceFill(['photo_path' => null])->save();
        }

        return $this->backToProfile($request, 'Photo removed.');
    }

    private function backToProfile(Request $request, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.profile')->with('status', $message);
    }
}
