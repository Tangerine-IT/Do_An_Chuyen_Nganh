<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shop identity
    |--------------------------------------------------------------------------
    */

    'name' => env('PETSHOP_NAME', 'PetShop'),

    // Money is stored as whole VND amounts (unsigned big integers), never floats.
    'currency' => 'VND',
    'currency_symbol' => '₫',

    // Shown in the site footer. Replace with the real shop details before going live.
    'contact' => [
        'address' => '123 Nguyễn Trãi, Quận 1, TP. Hồ Chí Minh',
        'phone' => '0901 234 567',
        'email' => 'xinchao@petshop.test',
    ],

    /*
    |--------------------------------------------------------------------------
    | Working hours
    |--------------------------------------------------------------------------
    |
    | Keyed by ISO-8601 weekday (1 = Monday ... 7 = Sunday). Use null for a
    | closed day. Times are in the application timezone (Asia/Ho_Chi_Minh).
    |
    */

    'working_hours' => [
        1 => ['open' => '08:00', 'close' => '20:00'],
        2 => ['open' => '08:00', 'close' => '20:00'],
        3 => ['open' => '08:00', 'close' => '20:00'],
        4 => ['open' => '08:00', 'close' => '20:00'],
        5 => ['open' => '08:00', 'close' => '20:00'],
        6 => ['open' => '08:00', 'close' => '20:00'],
        7 => ['open' => '09:00', 'close' => '18:00'],
    ],

    'booking' => [
        'slot_minutes' => 30,
        'buffer_minutes' => 0,
        'max_days_ahead' => 60,
        'cancel_before_hours' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */

    // Products with stock at or below this number are flagged as low stock.
    'low_stock_threshold' => 5,

    'shipping' => [
        'fee' => 30000,
        'free_threshold' => 500000,
    ],

];
