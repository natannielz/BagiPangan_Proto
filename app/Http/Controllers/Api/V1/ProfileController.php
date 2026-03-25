<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileAvatarRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class ProfileController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'avatar_url' => $this->avatarUrl($user),
            ],
            'message' => 'OK',
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'avatar_url' => $this->avatarUrl($user),
            ],
            'message' => 'OK',
        ]);
    }

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json([
            'data' => [
                'user' => new UserResource($user->refresh()),
                'avatar_url' => $this->avatarUrl($user),
            ],
            'message' => 'Profil diperbarui.',
        ]);
    }

    public function avatar(ProfileAvatarRequest $request): JsonResponse
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

        return response()->json([
            'data' => [
                'user' => new UserResource($user->refresh()),
                'avatar_url' => $this->avatarUrl($user),
            ],
            'message' => 'Foto profil diperbarui.',
        ]);
    }

    protected function avatarUrl($user): ?string
    {
        if (! $user || ! $user->avatar_path) {
            return null;
        }

        return URL::temporarySignedRoute('avatars.show', now()->addHour(), ['user' => $user->id]);
    }
}
