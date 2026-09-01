<?php

/*
|--------------------------------------------------------------------------
| CORS
|--------------------------------------------------------------------------
|
| allowed_origins was ['*'] while supports_credentials was true. With
| credentials enabled the CORS middleware does not send a literal "*" - it
| echoes back whichever Origin made the request and adds
| Access-Control-Allow-Credentials: true. That let any website on the internet
| make credentialed cross-origin calls to this API and read the responses.
|
| Origins are now an explicit allow-list driven by CORS_ALLOWED_ORIGINS
| (comma separated). Set it to the frontend domains only, e.g.
| CORS_ALLOWED_ORIGINS="https://gihqs.vercel.app,https://www.gihqs.org"
|
*/

$origins = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))
)));

if (empty($origins)) {
    $origins = array_values(array_filter([
        env('FRONTEND_URL'),
        'http://localhost:5173',
        'http://localhost:3000',
    ]));
}

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => $origins,

    'allowed_origins_patterns' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CORS_ALLOWED_ORIGIN_PATTERNS', ''))
    ))),

    'allowed_headers' => ['Accept', 'Authorization', 'Content-Type', 'X-Requested-With', 'X-Localization'],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => true,

];
