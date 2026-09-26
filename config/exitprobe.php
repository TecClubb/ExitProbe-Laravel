<?php

return [
    // Settings → API Tokens on your ExitProbe dashboard.
    'api_key' => env('EXITPROBE_API_KEY'),

    // Override for a self-hosted instance or staging environment.
    'base_url' => env('EXITPROBE_BASE_URL', 'https://app.exitprobe.com/api/v1'),

    'timeout' => env('EXITPROBE_TIMEOUT', 30),
];
