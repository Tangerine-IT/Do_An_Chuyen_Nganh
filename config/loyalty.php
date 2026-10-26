<?php

return [
    'order_points_per_10000' => 1,
    'booking_points_per_10000' => 1,
    'redeem_value' => 100,
    'max_redeem_percent' => 30,
    'tiers' => [
        'bronze' => ['min' => 0, 'perks' => ['Tích điểm cơ bản']],
        'silver' => ['min' => 500, 'perks' => ['Ưu đãi sinh nhật']],
        'gold' => ['min' => 1500, 'perks' => ['Ưu tiên hỗ trợ', 'Ưu đãi đặc biệt']],
    ],
];
