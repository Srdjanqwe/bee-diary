<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Guard: during migrations, fresh deploys, or if the DB connection
        // isn't ready yet, the permissions table may not exist. Without this
        // check, EVERY artisan command (including migrate itself) and every
        // HTTP request would crash with "relation permissions does not exist".
        try {
            if (!Schema::hasTable('permissions')) {
                return;
            }

            foreach (Permission::pluck('name') as $permission) {
                Gate::define($permission, function ($user) use ($permission) {
                    return $user->roles()
                        ->whereHas('permissions', function ($q) use ($permission) {
                            $q->where('name', $permission);
                        })
                        ->exists();
                });
            }
        } catch (\Throwable $e) {
            // DB not reachable / not migrated yet - fail safe, don't crash boot()
            report($e);
        }
    }
}
