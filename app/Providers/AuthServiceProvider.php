<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Donation;
use App\Policies\CategoryPolicy;
use App\Policies\ClaimPolicy;
use App\Policies\DonationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Claim::class => ClaimPolicy::class,
        Donation::class => DonationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
