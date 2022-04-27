<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'acces' => \App\Models\Api\V1\TypeCommande\Cmdacces::class,
            'pm' => \App\Models\Api\V1\TypeCommande\Pm::class,
            'error' => \App\Models\Api\V1\Administration\Commandstypeerror::class
        ]);
    }
}

