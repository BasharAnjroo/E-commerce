<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        Passport::refreshTokensExpireIn(now()->addDays(30));
        // Mandatory to define Scope
        Passport::tokensCan([
            'Admin' => 'Add/Edit/Delete all',
            'SubAdmin' => 'Add/Edit all',
            'Customer' => 'List all',
            'VIP' => 'List all and extra options'
        ]);
        Passport::setDefaultScope([
            'Admin'
        ]);

        // Passport::tokensExpireIn(now()->addDays(15));
        //  Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
