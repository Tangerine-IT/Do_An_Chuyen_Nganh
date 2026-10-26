<?php

return [

    'user_role' => [
        'customer' => 'Khách hàng',
        'staff' => 'Nhân viên',
        'admin' => 'Quản trị viên',
    ],

    'pet_species' => [
        'dog' => 'Chó',
        'cat' => 'Mèo',
        'bird' => 'Chim',
        'fish' => 'Cá',
        'small_pet' => 'Thú cưng nhỏ',
        'other' => 'Khác',
    ],

    'product_species' => [
        'dog' => 'Chó',
        'cat' => 'Mèo',
        'bird' => 'Chim',
        'fish' => 'Cá',
        'small_pet' => 'Thú cưng nhỏ',
        'other' => 'Khác',
        'all' => 'Mọi loài',
    ],

    'life_stage' => [
        'young' => 'Con non',
        'adult' => 'Trưởng thành',
        'senior' => 'Lớn tuổi',
        'all' => 'Mọi độ tuổi',
    ],

    'pet_sex' => [
        'male' => 'Đực',
        'female' => 'Cái',
        'unknown' => 'Chưa xác định',
    ],

    'health_record_type' => [
        'vaccination' => 'Tiêm phòng',
        'deworming' => 'Tẩy giun',
        'weight' => 'Cân nặng',
        'checkup' => 'Khám sức khỏe',
        'other' => 'Khác',
    ],

    'order_status' => [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao hàng',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ],

    'payment_status' => [
        'pending' => 'Chưa thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thanh toán thất bại',
        'refunded' => 'Đã hoàn tiền',
    ],

    'payment_method' => [
        'cod' => 'Thanh toán khi nhận hàng',
        'vnpay' => 'VNPay',
        'momo' => 'MoMo',
    ],

    'booking_status' => [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        'no_show' => 'Khách không đến',
    ],

    'coupon_type' => [
        'percent' => 'Giảm theo phần trăm',
        'fixed' => 'Giảm số tiền cố định',
    ],

    'post_status' => [
        'draft' => 'Bản nháp',
        'published' => 'Đã đăng',
    ],

    'adoption_status' => [
        'available' => 'Đang tìm chủ',
        'pending' => 'Đang xét duyệt',
        'adopted' => 'Đã có chủ',
    ],
    'adoption_application_status' => [
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã duyệt',
        'rejected' => 'Từ chối',
        'withdrawn' => 'Đã rút',
    ],

    'loyalty_reason' => [
        'earned' => 'Tích điểm từ đơn hàng',
        'redeemed' => 'Dùng điểm',
        'adjustment' => 'Điều chỉnh',
        'reversed' => 'Thu hồi điểm',
    ],

    'chat_role' => [
        'user' => 'Bạn',
        'assistant' => 'Trợ lý PetShop',
    ],

];
