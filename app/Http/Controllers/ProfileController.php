<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAvatarRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateAvatar(ProfileAvatarRequest $request): RedirectResponse
    {
        $user = $request->user();
        $file = $request->file('avatar');

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname())->scaleDown(width: 1200);
            $encoded = $image->toWebp(quality: 80);
        } catch (Throwable $e) {
            abort(422, 'Invalid image');
        }

        $path = 'avatars/'.$user->id.'/'.Str::uuid().'.webp';

        if ($user->avatar_path) {
            Storage::disk('local')->delete($user->avatar_path);
        }

        Storage::disk('local')->put($path, (string) $encoded);
        $user->forceFill(['avatar_path' => $path])->save();

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    public function showAvatar(Request $request, User $user): StreamedResponse
    {
        $authUser = $request->user();

        if (! $authUser) {
            abort(401);
        }

        if (! $authUser->isAdmin() && $authUser->id !== $user->id) {
            abort(403);
        }

        if (! $user->avatar_path) {
            abort(404);
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($user->avatar_path)) {
            abort(404);
        }

        return response()->stream(function () use ($disk, $user) {
            $stream = $disk->readStream($user->avatar_path);
            if ($stream === false) {
                return;
            }
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->avatar_path) {
            Storage::disk('local')->delete($user->avatar_path);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
