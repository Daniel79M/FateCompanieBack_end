<?php

namespace App\Providers;

use App\Interfaces\AuthInterface;
use App\Repositories\Authrepository;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app-> bind(AuthInterface::class, Authrepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
