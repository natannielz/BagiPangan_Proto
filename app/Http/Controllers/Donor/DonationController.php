<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Http\Requests\DonationStoreRequest;
use App\Http\Requests\DonationUpdateRequest;
use App\Events\DonationCancelled;
use App\Models\Category;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class DonationController extends Controller
{
    public function index(Request $request): View
    {
        $donations = Donation::query()
            ->where('donor_id', $request->user()->id)
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('donor.donations.index', [
            'donations' => $donations,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Donation::class);

        $categories = Category::query()->orderBy('name')->get();

        return view('donor.donations.create', [
            'categories' => $categories,
        ]);
    }

    public function store(DonationStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Donation::class);

        $data = $request->validated();

        $donation = Donation::create([
            'donor_id' => $request->user()->id,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'qty_portions' => $data['qty_portions'],
            'location_district' => $data['location_district'],
            'expiry_at' => $data['expiry_at'],
            'status' => 'available',
            'moderation_status' => 'pending',
        ]);

        $file = $request->file('photo');
        if ($file) {
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getPathname())->scaleDown(width: 1200);
                $encoded = $image->toWebp(quality: 80);
            } catch (Throwable $e) {
                abort(422, 'Invalid image');
            }

            $path = 'donations/'.$donation->id.'/'.Str::uuid().'.webp';
            Storage::disk('local')->put($path, (string) $encoded);

            $donation->forceFill(['photo_path' => $path])->save();
        }

        return Redirect::route('donor.donations.index')->with('status', 'donation-created');
    }

    public function edit(Donation $donation): View
    {
        $this->authorize('update', $donation);

        $categories = Category::query()->orderBy('name')->get();

        return view('donor.donations.edit', [
            'donation' => $donation,
            'categories' => $categories,
        ]);
    }

    public function update(DonationUpdateRequest $request, Donation $donation): RedirectResponse
    {
        $this->authorize('update', $donation);

        $data = $request->validated();

        $donation->fill([
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'qty_portions' => $data['qty_portions'],
            'location_district' => $data['location_district'],
            'expiry_at' => $data['expiry_at'],
        ])->save();

        $file = $request->file('photo');
        if ($file) {
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getPathname())->scaleDown(width: 1200);
                $encoded = $image->toWebp(quality: 80);
            } catch (Throwable $e) {
                abort(422, 'Invalid image');
            }

            $path = 'donations/'.$donation->id.'/'.Str::uuid().'.webp';

            if ($donation->photo_path) {
                Storage::disk('local')->delete($donation->photo_path);
            }

            Storage::disk('local')->put($path, (string) $encoded);
            $donation->forceFill(['photo_path' => $path])->save();
        }

        return Redirect::route('donor.donations.index')->with('status', 'donation-updated');
    }

    public function cancel(Donation $donation): RedirectResponse
    {
        $this->authorize('cancel', $donation);

        $donation->forceFill(['status' => 'cancelled'])->save();

        DonationCancelled::dispatch($donation->fresh());

        return Redirect::route('donor.donations.index')->with('status', 'donation-cancelled');
    }
}
