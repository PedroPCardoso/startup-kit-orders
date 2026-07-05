<?php

declare(strict_types=1);

return [
    'default_driver' => env('STARTUP_KIT_ORDER_DRIVER', 'mysql'),

    'routes' => [
        'prefix' => 'orders',
        'middleware' => ['api'],
    ],
];
