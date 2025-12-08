<?php

namespace App\Providers;

use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationRepositoryInterface;
use App\Services\OrganizationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);

        $this->app->singleton(OrganizationService::class, function ($app) {
            return new OrganizationService(
                $app->make(OrganizationRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
