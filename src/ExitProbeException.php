<?php

namespace ExitProbe\Laravel;

use Exception;
use Illuminate\Http\Client\Response;

class ExitProbeException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly array $errors = [],
        public readonly ?Response $response = null,
    ) {
        parent::__construct($message, $status);
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json() ?? [];

        return new self(
            $body['message'] ?? "ExitProbe API request failed with status {$response->status()}",
            $response->status(),
            $body['errors'] ?? [],
            $response,
        );
    }
}
