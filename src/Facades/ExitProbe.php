<?php

namespace ExitProbe\Laravel\Facades;

use ExitProbe\Laravel\ExitProbeClient;
use ExitProbe\Laravel\MonitorsResource;
use ExitProbe\Laravel\ProjectsResource;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MonitorsResource monitors()
 * @method static ProjectsResource projects()
 * @method static array request(string $method, string $path, array $query = [], ?array $body = null)
 *
 * @see ExitProbeClient
 */
class ExitProbe extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ExitProbeClient::class;
    }
}
