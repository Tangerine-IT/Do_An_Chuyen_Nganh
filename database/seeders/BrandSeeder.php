<?php

namespace Database\Seeders;

use App\Models\Brand;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Fictional but believable brand names, so demo data never implies real product claims.
 */
class BrandSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->brands() as $name => $description) {
            Brand::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'logo_path' => PlaceholderImage::make(
                    'placeholders/brands/'.Str::slug($name).'.svg',
                    '🐾',
                    $name,
                    '#FBEFD9',
                ),
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    private function brands(): array
    {
        return [
            'NutriPaws' => 'Thương hiệu dinh dưỡng cân bằng cho chó mèo, công thức xây dựng cùng chuyên gia thú y.',
            'Happy Tail' => 'Thức ăn và bánh thưởng giúp thú cưng vui vẻ mỗi ngày với nguyên liệu chọn lọc.',
            'Meowtopia' => 'Chuyên gia dinh dưỡng dành riêng cho mèo, từ mèo con đến mèo lớn tuổi.',
            'Kitty Joy' => 'Thức ăn, cát vệ sinh và đồ chơi vui nhộn cho các bé mèo.',
            'AquaLife' => 'Thức ăn và thiết bị cho bể cá cảnh, cá Koi và cá Betta.',
            'Aviary Gold' => 'Thức ăn hạt cao cấp cho vẹt, chim yến và các loài chim cảnh.',
            'VetCare Plus' => 'Sản phẩm chăm sóc sức khỏe, vitamin và phòng ký sinh trùng cho thú cưng.',
            'Bobby Pet' => 'Phụ kiện bền đẹp: vòng cổ, dây dắt, lồng, giường và đồ chơi.',
            'PetSpa Garden' => 'Sữa tắm và sản phẩm vệ sinh dịu nhẹ chiết xuất từ thiên nhiên.',
            'Green Paw' => 'Dòng sản phẩm hữu cơ, không ngũ cốc, dành cho các bé dễ dị ứng.',
        ];
    }
}
