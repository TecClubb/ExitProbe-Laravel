<?php

namespace ExitProbe\Laravel;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

/**
 * ExitProbe API client. One instance per API token.
 *
 * ```php
 * $client = new ExitProbeClient(config('exitprobe.api_key'));
 * $monitors = $client->monitors()->list(['status' => 'down']);
 * ```
 *
 * Or via the container/facade, which is wired to the `exitprobe` config:
 * `ExitProbe::monitors()->list();`
 */
class ExitProbeClient
{
    protected string $baseUrl;

    protected int $timeout;

    public function __construct(
        protected string $apiKey,
        ?string $baseUrl = null,
        ?int $timeout = null,
    ) {
        if ($this->apiKey === '') {
            throw new InvalidArgumentException('ExitProbe: an API key is required.');
        }

        $this->baseUrl = rtrim($baseUrl ?? 'https://app.exitprobe.com/api/v1', '/');
        $this->timeout = $timeout ?? 30;
    }

    public function monitors(): MonitorsResource
    {
        return new MonitorsResource($this);
    }

    public function projects(): ProjectsResource
    {
        return new ProjectsResource($this);
    }

    protected function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->apiKey)
            ->acceptJson()
            ->timeout($this->timeout);
    }

    /**
     * Low-level request — used by the resource classes, exposed for
     * endpoints not yet wrapped by a typed method.
     */
    public function request(string $method, string $path, array $query = [], ?array $body = null): array
    {
        $request = $this->http();

        $response = match (strtoupper($method)) {
            'GET' => $request->get($path, $query),
            'POST' => $request->post($path, $body ?? []),
            'PUT' => $request->put($path, $body ?? []),
            'PATCH' => $request->patch($path, $body ?? []),
            'DELETE' => $request->delete($path, $body ?? []),
            default => throw new InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };

        return $this->handle($response);
    }

    protected function handle(Response $response): array
    {
        if ($response->failed()) {
            throw ExitProbeException::fromResponse($response);
        }

        return $response->json() ?? [];
    }
}
