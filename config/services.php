<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    // for stripe
    'stripe' => [
        'secret' => env('STRIPE_SECRET_KEY'),
        'key'    => env('STRIPE_PUBLIC_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    // for classmarker
    'classmarker' => [
        'api_key' => env('CLASSMARKER_API_KEY'),
        'api_secret' => env('CLASSMARKER_API_SECRET'),
        'api_url' => env('CLASSMARKER_BASE_URL'),
        'webhook_secret' => env('CLASSMARKER_WEBHOOK_SECRET'),
    ],

    // for ai configurations
    'ai' => [
        'pathway_wizard_enable' => filter_var(env('AI_PATHWAY_WIZARD_ENABLE', false), FILTER_VALIDATE_BOOLEAN),
        'primary' => [
            'provider' => env('AI_PRIMARY_PROVIDER'),
            'api_key'  => env('AI_PRIMARY_API_KEY'),
            'model'    => env('AI_PRIMARY_MODEL'),
        ],
        'fallback' => [
            'provider' => env('AI_FALLBACK_PROVIDER'),
            'api_key'  => env('AI_FALLBACK_API_KEY'),
            'model'    => env('AI_FALLBACK_MODEL'),
        ],
    ],

];
