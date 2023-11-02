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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'telegram' => [
        'bot_name' => env('TELEGRAM_BOT_NAME'),
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'hook_url' => env('TELEGRAM_HOOK_URL'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'partner_chat_id' => env('TELEGRAM_PARTNER_CHAT_ID'),
        'vacancy_chat_id' => env('TELEGRAM_VACANCY_CHAT_ID'),
    ],

    'atmos' => [
        'api_url' => env('ATMOS_API_URL', ''),
        'store_id' => env('ATMOS_STORE_ID', ''),
        'api_key' => env('ATMOS_API_KEY', ''),
        'consumer_key' => env('ATMOS_CONSUMER_KEY', ''),
        'consumer_secret' => env('ATMOS_CONSUMER_SECRET', ''),
        'access_token' => env('ATMOS_ACCESS_TOKEN', ''),
    ],

    'paycom' => [
	    'merchant_id' => env('PAYCOM_MERCHANT_ID', ''),
	    'login'       => 'Paycom',
	    'keyFile'     => env('PAYCOM_ENV', 'test') == 'production' ? config_path('password.paycom') : config_path('password-test.paycom'),
	],

    'click' => [
        'merchant_id' => env('CLICK_MERCHANT_ID', ''),
        'service_id' => env('CLICK_SERVICE_ID', ''),
        'user_id' => env('CLICK_USER_ID', ''),
        'secret_key' => env('CLICK_SECRET_KEY', ''),
        'model' => '\App\Order',
    ],

    'zoodpay' => [
        'env' => env('ZOODPAY_ENV', 'sandbox'),
        'merchant_key' => env('ZOODPAY_MERCHANT_KEY', ''),
        'merchant_secret' => env('ZOODPAY_MERCHANT_SECRET', ''),
        'salt' => env('ZOODPAY_SALT', ''),
        'model' => '\App\Order',
        'production_url' => env('ZOODPAY_PRODUCTION_URL', 'https://api.zoodpay.com/v0/'),
        'sandbox_url' => env('ZOODPAY_SANDBOX_URL', 'https://sandbox-api.zoodpay.com/v0/'),
        'min_limit' => env('ZOODPAY_MIN_LIMIT', 10),
        'max_limit' => env('ZOODPAY_MAX_LIMIT', 5500000),
        'installments' => env('ZOODPAY_INSTALLMENTS', 4),
    ],

    'smartup' => [
        'base_url' => env('SMARTUP_BASE_URL', ''),
        'login' => env('SMARTUP_LOGIN', ''),
        'password' => env('SMARTUP_PASSWORD', ''),
        'filial_id' => env('SMARTUP_FILIAL_ID', ''),
    ],

    'installment_payment' => [
        'chat_id' => env('INSTALLMENT_PAYMENT_CHAT_ID'),
        'partners' => [
            'alifshop' => [
                'chat_id' => env('INSTALLMENT_PAYMENT_PARTNER_ALIFSHOP_CHAT_ID'),
                'email' => env('INSTALLMENT_PAYMENT_PARTNER_ALIFSHOP_EMAIL'),
            ],
            'zmarket' => [
                'chat_id' => env('INSTALLMENT_PAYMENT_PARTNER_ZMARKET_CHAT_ID'),
                'email' => env('INSTALLMENT_PAYMENT_PARTNER_ZMARKET_EMAIL'),
            ],
            'intend' => [
                'chat_id' => env('INSTALLMENT_PAYMENT_PARTNER_INTEND_CHAT_ID'),
                'email' => env('INSTALLMENT_PAYMENT_PARTNER_INTEND_EMAIL'),
            ],
        ],
    ],

    'diart' => [
        'api_url' => env('DIART_API_URL'),
        'login' => env('DIART_LOGIN'),
        'password' => env('DIART_PASSWORD'),
    ],

    'play_mobile' => [
        'api_url' => env('PLAY_MOBILE_API_URL'),
        'login' => env('PLAY_MOBILE_LOGIN'),
        'password' => env('PLAY_MOBILE_PASSWORD'),
    ],

    'elmakon' => [
        'api_url' => env('ELMAKON_API_URL'),
        'login' => env('ELMAKON_LOGIN'),
        'password' => env('ELMAKON_PASSWORD'),
    ],

    'alifshop' => [
        'azo' => [
            'merchant_token' => env('ALIFSHOP_AZO_MERCHANT_TOKEN'),
            'url' => env('ALIFSHOP_AZO_API_URL'),
        ],
    ],

    'intend' => [
        'url' => env('INTEND_URL'),
        'api_key' => env('INTEND_API_KEY'),
        'calculate_url' => env('INTEND_CALCULATE_URL'),
        'order_check_url' => env('INTEND_ORDER_CHECK_URL'),
    ],

    'trendyol' => [
        'api_url' => env('TRENDYOL_API_URL'),
        'username' => env('TRENDYOL_USERNAME'),
        'password' => env('TRENDYOL_PASSWORD'),
    ],

    'growcrm' => [
        'token' => env('GROWCRM_TOKEN', ''),
        'base_url' => env('GROWCRM_BASEURL', ''),
    ],
];
