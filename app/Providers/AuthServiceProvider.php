<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Kalau kamu pakai Policy nanti, ini wajib tetap ada
        $this->registerPolicies();

        // Gate buat admin
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate buat petugas
        Gate::define('isPetugas', function (User $user) {
            return $user->role === 'petugas';
        });
    }
}
