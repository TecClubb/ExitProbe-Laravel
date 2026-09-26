<?php

namespace ExitProbe\Laravel\Tests;

use ExitProbe\Laravel\ExitProbeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ExitProbeServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('exitprobe.api_key', 'ep_live_test');
        $app['config']->set('exitprobe.base_url', 'https://app.exitprobe.com/api/v1');
    }
}
