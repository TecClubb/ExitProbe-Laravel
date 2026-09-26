<?php

namespace ExitProbe\Laravel\Tests;

use ExitProbe\Laravel\ExitProbeClient;
use ExitProbe\Laravel\ExitProbeException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use ExitProbe\Laravel\Facades\ExitProbe;

class ExitProbeClientTest extends TestCase
{
    public function test_list_sends_the_bearer_token_and_decodes_the_response(): void
    {
        Http::fake([
            'app.exitprobe.com/api/v1/monitors*' => Http::response([
                'success' => true,
                'data' => ['total' => 0, 'monitors' => []],
            ], 200),
        ]);

        $result = ExitProbe::monitors()->list(['status' => 'down']);

        $this->assertSame([], $result['data']['monitors']);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer ep_live_test')
                && $request->url() === 'https://app.exitprobe.com/api/v1/monitors?status=down';
        });
    }

    public function test_create_sends_a_json_body(): void
    {
        Http::fake([
            'app.exitprobe.com/api/v1/monitors' => Http::response([
                'success' => true,
                'message' => 'ok',
                'data' => ['monitor' => ['id' => 1], 'protocols' => ['wireguard']],
            ], 200),
        ]);

        ExitProbe::monitors()->create(['name' => 'Node 1', 'wireguard_enabled' => true]);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request['name'] === 'Node 1';
        });
    }

    public function test_a_422_throws_exitprobe_exception_with_field_errors(): void
    {
        Http::fake([
            'app.exitprobe.com/api/v1/monitors' => Http::response([
                'success' => false,
                'message' => 'At least one VPN protocol must be configured.',
                'errors' => ['name' => ['The name field is required.']],
            ], 422),
        ]);

        try {
            ExitProbe::monitors()->create(['name' => '']);
            $this->fail('Expected ExitProbeException');
        } catch (ExitProbeException $e) {
            $this->assertSame(422, $e->status);
            $this->assertSame('At least one VPN protocol must be configured.', $e->getMessage());
            $this->assertSame(['name' => ['The name field is required.']], $e->errors);
        }
    }

    public function test_projects_regenerate_key(): void
    {
        Http::fake([
            'app.exitprobe.com/api/v1/projects/5/regenerate-key' => Http::response([
                'success' => true,
                'data' => ['project' => ['id' => 5, 'api_key' => 'ep_proj_new']],
            ], 200),
        ]);

        $result = ExitProbe::projects()->regenerateKey(5);

        $this->assertSame('ep_proj_new', $result['data']['project']['api_key']);
    }

    public function test_an_empty_api_key_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ExitProbeClient('');
    }
}
