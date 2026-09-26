# exitprobe/laravel

Official Laravel client for the [ExitProbe](https://exitprobe.com) API. Manage projects and monitors, trigger checks, and read uptime/analytics from your own Laravel app — no HTTP boilerplate.

## Install

```bash
composer require exitprobe/laravel
```

The service provider and `ExitProbe` facade are auto-discovered. Publish the config if you want to tweak defaults:

```bash
php artisan vendor:publish --tag=exitprobe-config
```

Then set your API token (Settings → API Tokens on your ExitProbe dashboard):

```dotenv
EXITPROBE_API_KEY=ep_live_...
```

## Quick start

```php
use ExitProbe\Laravel\Facades\ExitProbe;

// List monitors that are currently down
$monitors = ExitProbe::monitors()->list(['status' => 'down']);

// Create a project, then a monitor inside it
$project = ExitProbe::projects()->create(['name' => 'Production']);
$created = ExitProbe::monitors()->createForProject($project['data']['project']['id'], [
    'name' => 'EU Exit Node',
    'expected_exit_ip' => '203.0.113.45',
    'wireguard_enabled' => true,
    'wireguard_config' => "[Interface]\nPrivateKey=...",
]);

// Trigger an on-demand check, then poll it
$dispatched = ExitProbe::monitors()->checkNow($created['data']['monitor']['id']);
ExitProbe::monitors()->checkStatus($created['data']['monitor']['id'], $dispatched['data']['check_ids']);

// Uptime history and full analytics
ExitProbe::monitors()->uptime($created['data']['monitor']['id'], ['range' => '7d']);
ExitProbe::monitors()->analytics($created['data']['monitor']['id'], ['range' => '30d']);
```

Or resolve `ExitProbe\Laravel\ExitProbeClient` from the container / inject it, if you'd rather not use the facade.

## Error handling

Every non-2xx response throws `ExitProbe\Laravel\ExitProbeException`, which carries the HTTP status and, for a `422`, the field-level validation errors:

```php
use ExitProbe\Laravel\ExitProbeException;

try {
    ExitProbe::monitors()->create(['name' => '']);
} catch (ExitProbeException $e) {
    report($e);
    // $e->status, $e->errors, $e->getMessage()
}
```

## API surface

- **`ExitProbe::projects()`** — `list`, `create`, `get`, `update`, `delete`, `restore`, `regenerateKey`, `clearChecks`, `monitors`
- **`ExitProbe::monitors()`** — `list`, `create`, `createForProject`, `get`, `update`, `delete`, `restore`, `checkNow`, `checkStatus`, `checks`, `clearChecks`, `uptime`, `uptimeRegions`, `analytics`, `status`

Every method mirrors an endpoint documented at `/api-docs` on your ExitProbe dashboard — see there for full request/response shapes. Every method returns the API's decoded JSON body as an associative array (`['success' => ..., 'data' => [...]]`).

## Configuration

`config/exitprobe.php`:

```php
return [
    'api_key' => env('EXITPROBE_API_KEY'),
    'base_url' => env('EXITPROBE_BASE_URL', 'https://app.exitprobe.com/api/v1'),
    'timeout' => env('EXITPROBE_TIMEOUT', 30),
];
```

## Testing

This package ships its own PHPUnit suite built on Orchestra Testbench and `Http::fake()`:

```bash
composer install
vendor/bin/phpunit
```

## Publishing

Not published yet. Once this repo is pushed to GitHub:

1. Create a [Packagist](https://packagist.org) account and submit this repo's URL — `composer.json` is at the repo root, so Packagist picks it up directly.
2. Set up the GitHub webhook Packagist gives you, so new tags auto-publish.
3. `git tag v1.0.0 && git push --tags` for the first release; repeat for future versions.

## License

MIT
