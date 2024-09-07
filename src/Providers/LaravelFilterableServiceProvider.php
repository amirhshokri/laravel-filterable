<?php

namespace Amirhshokri\LaravelFilterable\Providers;

use Amirhshokri\LaravelFilterable\Console\Commands\CreateCustomFilter;
use Illuminate\Support\ServiceProvider;

class LaravelFilterableServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/filterable.php' => $this->app->configPath('filterable.php'),
        ], "laravel-filterable-config");

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateCustomFilter::class,
            ]);
        }
    }
}