<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Donation;
use App\Observers\CategoryObserver;
use App\Observers\ClaimObserver;
use App\Observers\DonationObserver;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api-general', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(60)->by('api|'.$key);
        });

        RateLimiter::for('api-claim', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(5)->by('claim|'.$key);
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Event → Listener bindings use PHP attributes (#[ListensTo]).
        // See app/Listeners/ for all event subscriptions.
        // Observers are registered explicitly below:
        Category::observe(CategoryObserver::class);
        Donation::observe(DonationObserver::class);
        Claim::observe(ClaimObserver::class);

        Horizon::auth(function (Request $request) {
            return $request->user()?->role === 'admin';
        });

        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $role = $request->user()?->role;

            return match ($role) {
                'admin'    => '/admin/dashboard',
                'donor'    => '/donor/dashboard',
                'receiver' => '/donations',
                default    => '/donations',
            };
        });
    }
}
