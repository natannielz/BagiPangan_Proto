<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->string('status')->toString();
        $categoryId = $request->integer('category_id');
        $location = $request->string('location')->toString();
        $q = $request->string('q')->toString();
        $sort = $request->string('sort')->toString();
        $perPage = (int) ($request->integer('per_page') ?: 15);

        $perPage = max(1, min(50, $perPage));

        $query = Donation::query()
            ->with('category')
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
            ->when($q !== '', fn ($q2) => $q2->where('title', 'like', '%'.$q.'%'))
            ->when($sort === 'expiry_asc', fn ($q) => $q->orderBy('expiry_at'))
            ->when($sort === 'expiry_desc', fn ($q) => $q->orderByDesc('expiry_at'))
            ->when($sort === 'oldest', fn ($q) => $q->orderBy('created_at'))
            ->when(! in_array($sort, ['expiry_asc', 'expiry_desc', 'oldest'], true), fn ($q) => $q->orderByDesc('created_at'));

        if (($status === '' || $status === 'available')) {
            $query->where('expiry_at', '>', now());
        }

        $donations = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => DonationResource::collection($donations),
            'meta' => [
                'current_page' => $donations->currentPage(),
                'last_page' => $donations->lastPage(),
                'per_page' => $donations->perPage(),
                'total' => $donations->total(),
            ],
            'message' => 'OK',
        ]);
    }

    public function show(Donation $donation): JsonResponse
    {
        if ($donation->moderation_status !== 'approved') {
            abort(404);
        }

        $donation->load('category');

        return response()->json([
            'data' => new DonationResource($donation),
            'message' => 'OK',
        ]);
    }
}
