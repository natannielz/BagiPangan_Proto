<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $categoryId = $request->integer('category_id');
        $location = $request->string('location')->toString();
        $q = $request->string('q')->toString();
        $sort = $request->string('sort')->toString();

        $query = Donation::query()
            ->with(['category'])
            ->where('moderation_status', 'approved')
            ->when($status !== '', function ($q) use ($status) {
                if (in_array($status, ['available', 'claimed', 'picked_up', 'completed', 'cancelled'], true)) {
                    $q->where('status', $status);
                }
            }, function ($q) {
                $q->where('status', 'available');
            })
            ->when($categoryId > 0, fn ($q) => $q->where('category_id', $categoryId))
            ->when($location !== '', fn ($q) => $q->where('location_district', 'like', '%'.$location.'%'))
            ->when($q !== '' && strlen($q) >= 2, function ($q2) use ($q) {
                if (DB::connection()->getDriverName() === 'mysql') {
                    $q2->whereRaw(
                        'MATCH(title, location_district, description) AGAINST(? IN BOOLEAN MODE)',
                        [$q.'*']
                    )->orderByRaw(
                        'MATCH(title, location_district, description) AGAINST(? IN BOOLEAN MODE) DESC',
                        [$q.'*']
                    );
                } else {
                    $q2->where('title', 'like', '%'.$q.'%');
                }
            })
            ->when($sort === 'expiry_asc', fn ($q) => $q->orderBy('expiry_at'))
            ->when($sort === 'expiry_desc', fn ($q) => $q->orderByDesc('expiry_at'))
            ->when($sort === 'oldest', fn ($q) => $q->orderBy('created_at'))
            ->when(! in_array($sort, ['expiry_asc', 'expiry_desc', 'oldest'], true), fn ($q) => $q->orderByDesc('created_at'));

        if (($status === '' || $status === 'available')) {
            $query->where('expiry_at', '>', now());
        }

        $cacheKey  = 'list:'.md5(json_encode($request->only(['status', 'category_id', 'location', 'q', 'sort', 'page'])));
        try {
            $donations = Cache::tags(['donations'])->remember($cacheKey, 60, fn () => $query->paginate(12)->withQueryString());
        } catch (\BadMethodCallException) {
            $donations = Cache::remember($cacheKey, 60, fn () => $query->paginate(12)->withQueryString());
        }

        $donations->getCollection()->transform(function (Donation $donation) {
            $donation->photo_url = $donation->photo_path
                ? URL::temporarySignedRoute('donations.photo', now()->addHour(), ['donation' => $donation->id])
                : null;

            return $donation;
        });

        try {
            $categories = Cache::tags(['categories'])->remember('all_active', 3600, fn () => Category::query()->where('is_active', true)->orderBy('name')->get());
        } catch (\BadMethodCallException) {
            $categories = Cache::remember('all_active', 3600, fn () => Category::query()->where('is_active', true)->orderBy('name')->get());
        }

        return view('donations.index', [
            'donations' => $donations,
            'categories' => $categories,
            'filters' => [
                'status' => $status,
                'category_id' => $categoryId,
                'location' => $location,
                'q' => $q,
                'sort' => $sort,
            ],
        ]);
    }

    public function show(Donation $donation): View
    {
        if ($donation->moderation_status !== 'approved') {
            $user = request()->user();
            $canSee = $user && ($user->isAdmin() || ($user->isDonor() && $donation->donor_id === $user->id));

            if (! $canSee) {
                abort(404);
            }
        }

        $donation->load(['donor', 'category']);

        $photoUrl = $donation->photo_path
            ? URL::temporarySignedRoute('donations.photo', now()->addHour(), ['donation' => $donation->id])
            : null;

        return view('donations.show', [
            'donation' => $donation,
            'photoUrl' => $photoUrl,
        ]);
    }

    public function photo(Request $request, Donation $donation): StreamedResponse
    {
        if ($donation->moderation_status !== 'approved') {
            $user = $request->user();
            $canSee = $user && ($user->isAdmin() || ($user->isDonor() && $donation->donor_id === $user->id));

            if (! $canSee) {
                abort(404);
            }
        }

        if (! $donation->photo_path) {
            abort(404);
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($donation->photo_path)) {
            abort(404);
        }

        return response()->stream(function () use ($disk, $donation) {
            $stream = $disk->readStream($donation->photo_path);
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
}
