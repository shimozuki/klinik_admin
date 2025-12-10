<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Pasien;
use App\Models\RekamMedis;
use App\Policies\PasienPolicy;
use App\Policies\RekamMedisPolicy;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Pasien::class => PasienPolicy::class,
        RekamMedis::class => RekamMedisPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate untuk super admin (bypass semua permission)
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true; // Admin bisa akses semua
            }
        });
    }
}
