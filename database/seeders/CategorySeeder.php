<?php

namespace Database\Seeders;

use App\Models\Category;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * 6 top-level categories, each with a few subcategories. Products attach to the subcategories.
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->tree() as $order => $root) {
            $parent = Category::create([
                'name' => $root['name'],
                'slug' => Str::slug($root['name']),
                'description' => $root['description'],
                'image_path' => PlaceholderImage::make(
                    'placeholders/categories/'.Str::slug($root['name']).'.svg',
                    $root['emoji'],
                    $root['name'],
                    $root['background'],
                ),
                'sort_order' => $order,
            ]);

            foreach ($root['children'] as $childOrder => $childName) {
                $parent->children()->create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'sort_order' => $childOrder,
                ]);
            }
        }
    }

    /**
     * @return list<array{name: string, description: string, emoji: string, background: string, children: list<string>}>
     */
    private function tree(): array
    {
        return [
            [
                'name' => 'Thức ăn cho chó',
                'description' => 'Hạt khô, pate và bánh thưởng cho chó mọi lứa tuổi và kích cỡ.',
                'emoji' => '🐶',
                'background' => '#FCE7C8',
                'children' => ['Hạt khô cho chó', 'Pate cho chó', 'Bánh thưởng cho chó'],
            ],
            [
                'name' => 'Thức ăn cho mèo',
                'description' => 'Dinh dưỡng đầy đủ cho mèo con, mèo trưởng thành và mèo lớn tuổi.',
                'emoji' => '🐱',
                'background' => '#FBD9C4',
                'children' => ['Hạt khô cho mèo', 'Pate cho mèo', 'Snack cho mèo'],
            ],
            [
                'name' => 'Chim cảnh',
                'description' => 'Thức ăn, lồng và phụ kiện cho vẹt, yến, chào mào và các loài chim cảnh.',
                'emoji' => '🦜',
                'background' => '#DDEFD3',
                'children' => ['Thức ăn cho chim', 'Lồng & phụ kiện chim'],
            ],
            [
                'name' => 'Cá cảnh',
                'description' => 'Thức ăn và thiết bị cho bể cá cảnh, cá Koi và cá Betta.',
                'emoji' => '🐟',
                'background' => '#D2E8F2',
                'children' => ['Thức ăn cho cá', 'Phụ kiện bể cá'],
            ],
            [
                'name' => 'Đồ chơi & phụ kiện',
                'description' => 'Đồ chơi, vòng cổ, giường nằm, cát vệ sinh và nhiều phụ kiện tiện ích.',
                'emoji' => '🧸',
                'background' => '#EFE3F5',
                'children' => ['Đồ chơi', 'Vòng cổ & dây dắt', 'Giường & nhà cho thú cưng', 'Cát vệ sinh'],
            ],
            [
                'name' => 'Chăm sóc & sức khỏe',
                'description' => 'Sữa tắm, vitamin, thực phẩm chức năng và sản phẩm phòng ve rận.',
                'emoji' => '💊',
                'background' => '#E4F1E8',
                'children' => ['Sữa tắm & vệ sinh', 'Vitamin & thực phẩm chức năng', 'Trị ve rận'],
            ],
        ];
    }
}
