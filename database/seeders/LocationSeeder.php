<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::query()->firstOrCreate(
            ['name' => 'PetCare Shop - Quận 1'],
            [
                'type' => 'shop',
                'description' => 'Cửa hàng PetCare và điểm tư vấn trực tiếp.',
                'address' => '12 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh',
                'phone' => config('petshop.contact.phone'),
                'latitude' => 10.7743,
                'longitude' => 106.7040,
            ],
        );
        Location::query()->firstOrCreate(
            ['name' => 'Phòng khám thú y Happy Paws'],
            [
                'type' => 'vet',
                'description' => 'Phòng khám và chăm sóc sức khỏe thú cưng.',
                'address' => '88 Võ Văn Tần, Quận 3, TP. Hồ Chí Minh',
                'phone' => '02838220000',
                'latitude' => 10.7769,
                'longitude' => 106.6866,
            ],
        );
        Location::query()->firstOrCreate(
            ['name' => 'Pet-friendly Cafe Nhà Mình'],
            [
                'type' => 'pet_friendly',
                'description' => 'Không gian thư giãn thân thiện với thú cưng.',
                'address' => '25 Thảo Điền, TP. Thủ Đức, TP. Hồ Chí Minh',
                'phone' => '0909123456',
                'latitude' => 10.8038,
                'longitude' => 106.7340,
            ],
        );
    }
}
