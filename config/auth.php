<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],


    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ]
    ],

    'passwords' => [
        'users' => [
            'driver' => 'eloquent',
            'provider' => 'users',
            // 'email' => 'auth.emails.password',
            'table' => 'password_resets',
            'expire' => 360,
        ],
    ],
];
