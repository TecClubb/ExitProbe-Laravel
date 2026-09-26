<?php

namespace ExitProbe\Laravel;

use Illuminate\Support\ServiceProvider;

class ExitProbeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/exitprobe.php', 'exitprobe');

        $this->app->singleton(ExitProbeClient::class, function ($app) {
            return new ExitProbeClient(
                apiKey: (string) $app['config']->get('exitprobe.api_key'),
                baseUrl: $app['config']->get('exitprobe.base_url'),
                timeout: $app['config']->get('exitprobe.timeout'),
            );
        });

        $this->app->alias(ExitProbeClient::class, 'exitprobe');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/exitprobe.php' => config_path('exitprobe.php'),
            ], 'exitprobe-config');
        }
    }
}
