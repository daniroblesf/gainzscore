<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    | List every front-end origin that is allowed to call the API.
    | In production, replace the localhost entries with your real domain.
    | Read from the environment so the value never has to be hardcoded here.
    |
    | Example .env entry:
    |   FRONTEND_URL=https://gainzscore.yourdomain.com
    */
    'allowed_origins' => array_filter([
        env('FRONTEND_URL', 'http://localhost:5173'),
        'http://127.0.0.1:5173',
    ]),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
