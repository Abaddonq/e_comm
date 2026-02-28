<?php

return [
    'base_rate' => env('SHIPPING_BASE_RATE', 29.90),

    'free_shipping_threshold' => env('FREE_SHIPPING_THRESHOLD', 500),

    'zone_rates' => [
        'Turkey' => [
            'Istanbul' => 0,
            'Ankara' => 5,
            'Izmir' => 5,
            'default' => 10,
        ],
        'default' => 20,
    ],
];
