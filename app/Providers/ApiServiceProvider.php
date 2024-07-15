<?php

namespace App\Providers;

use App\Repositories\Auth\AuthInterface;
use App\Repositories\Auth\AuthRepo;
use App\Repositories\StripeCustomer\StripeCustomerRepository;
use App\Repositories\StripeCustomer\StripeCustomerRepositoryInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthInterface::class,
            AuthRepo::class
        );
        $this->app->bind(
            StripeCustomerRepositoryInterface::class,
            StripeCustomerRepository::class
        );

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
