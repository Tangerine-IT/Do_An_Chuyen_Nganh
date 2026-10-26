<?php

namespace Database\Seeders;

use App\Enums\ProductSpecies;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * 70+ products across dogs, cats, birds and fish. Ingredient lists deliberately include
 * chicken (gà), beef (bò), fish (cá) and grains (ngũ cốc, lúa mì, ngô, yến mạch) so
 * allergy matching can be tested, plus "safe" options (lamb, duck) for allergic pets.
 *
 * Requires CategorySeeder and BrandSeeder to have run first.
 */
class ProductSeeder extends Seeder
{
    /** Indexes (into products()) that are seeded as out of stock / low stock for demo purposes. */
    private const OUT_OF_STOCK = [17, 43];

    private const LOW_STOCK = [22, 55];

    public function run(): void
    {
        /** @var Collection<string, Category> $categories */
        $categories = Category::with('parent')->get()->keyBy('slug');
        /** @var Collection<string, Brand> $brands */
        $brands = Brand::all()->keyBy('slug');

        foreach ($this->products() as $index => $row) {
            [$brandSlug, $categorySlug, $species, $lifeStage, $name, $price, $salePrice, $ingredients, $short] = $row;
            $weights = $row[9] ?? null;

            $brand = $brands[$brandSlug];
            $category = $categories[$categorySlug];
            $speciesEnum = ProductSpecies::from($species);

            $product = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'sku' => 'PS-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'short_description' => $short,
                'description' => $this->description($short, $brand->name, $speciesEnum),
                'ingredients' => $ingredients,
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => $this->stockFor($index),
                'package_size_grams' => str_contains($categorySlug, 'thuc-an') ? 2000 : null,
                'daily_grams_per_kg' => str_contains($categorySlug, 'thuc-an') ? 20 : null,
                'species' => $species,
                'life_stage' => $lifeStage,
                'min_pet_weight_kg' => $weights[0] ?? null,
                'max_pet_weight_kg' => $weights[1] ?? null,
                'is_active' => true,
                'is_featured' => $index % 6 === 0,
                'meta_title' => $name.' | '.config('petshop.name'),
                'meta_description' => Str::limit($short, 160),
            ]);

            $product->images()->create([
                'path' => PlaceholderImage::make(
                    'placeholders/products/'.$product->slug.'.svg',
                    $this->emojiFor($speciesEnum),
                    $brand->name,
                    $this->backgroundFor($category),
                ),
                'alt_text' => $name,
                'sort_order' => 0,
                'is_primary' => true,
            ]);
        }
    }

    private function stockFor(int $index): int
    {
        return match (true) {
            in_array($index, self::OUT_OF_STOCK, true) => 0,
            in_array($index, self::LOW_STOCK, true) => 3,
            default => 20 + (($index * 37) % 180),
        };
    }

    private function description(string $short, string $brandName, ProductSpecies $species): string
    {
        return $short."\n\n"
            .'Thương hiệu: '.$brandName.".\n"
            .'Phù hợp cho: '.Str::lower($species->label()).".\n"
            .'Bảo quản nơi khô ráo, thoáng mát, tránh ánh nắng trực tiếp.';
    }

    private function emojiFor(ProductSpecies $species): string
    {
        return match ($species) {
            ProductSpecies::Dog => '🐶',
            ProductSpecies::Cat => '🐱',
            ProductSpecies::Bird => '🦜',
            ProductSpecies::Fish => '🐟',
            ProductSpecies::SmallPet => '🐹',
            ProductSpecies::Other, ProductSpecies::All => '🐾',
        };
    }

    private function backgroundFor(Category $category): string
    {
        return match ($category->parent?->slug) {
            'thuc-an-cho-cho' => '#FCE7C8',
            'thuc-an-cho-meo' => '#FBD9C4',
            'chim-canh' => '#DDEFD3',
            'ca-canh' => '#D2E8F2',
            'do-choi-phu-kien' => '#EFE3F5',
            default => '#E4F1E8',
        };
    }

    /**
     * Row: brand slug, category slug, species, life stage, name, price (VND), sale price,
     * ingredients, short description, [min kg, max kg] (optional).
     *
     * @return list<array<int, mixed>>
     */
    private function products(): array
    {
        return [
            // Dog dry food
            ['nutripaws', 'hat-kho-cho-cho', 'dog', 'adult', 'NutriPaws Hạt Chó Trưởng Thành Vị Gà & Gạo 2kg', 289000, null, 'Thịt gà (28%), gạo lứt, bột gà, ngô, dầu gà, củ cải đường, men bia, vitamin và khoáng chất', 'Công thức giàu đạm từ thịt gà, dễ tiêu hóa, giúp lông mượt và hệ tiêu hóa khỏe.'],
            ['nutripaws', 'hat-kho-cho-cho', 'dog', 'young', 'NutriPaws Hạt Chó Con Vị Gà & Sữa 1,5kg', 259000, 239000, 'Thịt gà, sữa bột, gạo, lúa mì, trứng sấy, dầu cá, DHA, canxi, vitamin', 'Bổ sung DHA và canxi giúp chó con phát triển trí não và xương chắc khỏe.'],
            ['nutripaws', 'hat-kho-cho-cho', 'dog', 'adult', 'NutriPaws Hạt Chó Giống Nhỏ Vị Bò 1,5kg', 275000, null, 'Thịt bò, gạo, ngô, mỡ bò, bột khoai tây, taurine, vitamin E', 'Viên hạt nhỏ vừa miệng cho các giống chó nhỏ như Poodle, Phốc sóc.', [1, 10]],
            ['happy-tail', 'hat-kho-cho-cho', 'dog', 'adult', 'Happy Tail Hạt Bò Nướng Cho Chó 3kg', 369000, 329000, 'Thịt bò, bột bò, ngô, lúa mì, dầu cọ, men tiêu hóa, khoáng chất', 'Hương vị bò nướng thơm ngon, phù hợp chó trưởng thành mọi giống.'],
            ['happy-tail', 'hat-kho-cho-cho', 'dog', 'adult', 'Happy Tail Hạt Cá Hồi & Khoai Lang Giống Lớn 12kg', 1050000, null, 'Cá hồi (30%), khoai lang, đậu Hà Lan, dầu cá hồi, glucosamine, chondroitin', 'Công thức không ngũ cốc giàu omega-3, hỗ trợ khớp cho chó giống lớn.', [20, 60]],
            ['happy-tail', 'hat-kho-cho-cho', 'dog', 'senior', 'Happy Tail Hạt Chó Lớn Tuổi Vị Gà & Rau Củ 2kg', 315000, null, 'Thịt gà, gạo, yến mạch, cà rốt, bí đỏ, glucosamine, L-carnitine', 'Ít béo, bổ sung glucosamine giúp xương khớp chó trên 7 tuổi.'],
            ['green-paw', 'hat-kho-cho-cho', 'dog', 'adult', 'Green Paw Hạt Không Ngũ Cốc Vị Cừu 2kg', 395000, null, 'Thịt cừu (35%), khoai lang, đậu Hà Lan, dầu hạt lanh, rau củ sấy, men vi sinh', 'Nguồn đạm mới từ cừu, không gà, bò, ngũ cốc: lựa chọn cho các bé dễ dị ứng.'],
            ['green-paw', 'hat-kho-cho-cho', 'dog', 'adult', 'Green Paw Hạt Hữu Cơ Cá Ngừ & Gạo Lứt 2kg', 359000, null, 'Cá ngừ, gạo lứt hữu cơ, cà rốt, dầu cá, rong biển, vitamin', 'Công thức hữu cơ nhẹ bụng với cá ngừ và gạo lứt, tốt cho da lông.'],
            ['nutripaws', 'hat-kho-cho-cho', 'dog', 'adult', 'NutriPaws Hạt Chó Giống Lớn Vị Bò & Ngũ Cốc 8kg', 749000, 689000, 'Thịt bò, bột bò, ngũ cốc (ngô, lúa mì, lúa mạch), dầu cá, glucosamine', 'Năng lượng cao cho chó giống lớn, hỗ trợ cơ bắp và khớp.', [25, 60]],
            ['happy-tail', 'hat-kho-cho-cho', 'dog', 'all', 'Happy Tail Hạt Mềm Vị Gà Nướng 1kg', 199000, null, 'Thịt gà, khoai tây, glycerin, bột gạo, dầu gà, chiết xuất rau củ', 'Hạt mềm dễ nhai, thích hợp cho chó kén ăn hoặc răng yếu.'],

            // Dog pate
            ['nutripaws', 'pate-cho-cho', 'dog', 'adult', 'NutriPaws Pate Chó Vị Bò Hầm Rau Củ 150g', 32000, null, 'Thịt bò (45%), nước hầm bò, cà rốt, đậu Hà Lan, bột năng, vitamin', 'Pate mềm thơm vị bò hầm, trộn cùng hạt khô để kích thích vị giác.'],
            ['nutripaws', 'pate-cho-cho', 'dog', 'adult', 'NutriPaws Pate Chó Vị Gà & Gan 150g', 30000, null, 'Thịt gà (40%), gan gà, nước hầm gà, gạo, dầu cá, khoáng chất', 'Pate gà và gan giàu đạm, bổ sung sắt và vitamin A.'],
            ['happy-tail', 'pate-cho-cho', 'dog', 'adult', 'Happy Tail Pate Cá Hồi Cho Chó 150g', 35000, null, 'Cá hồi (40%), nước dùng cá, khoai lang, dầu cá hồi, vitamin', 'Pate cá hồi giàu omega-3 cho bộ lông óng mượt.'],
            ['green-paw', 'pate-cho-cho', 'dog', 'adult', 'Green Paw Pate Cừu & Bí Đỏ 150g', 42000, null, 'Thịt cừu (42%), bí đỏ, khoai lang, nước hầm cừu, dầu hạt lanh, vitamin', 'Pate đơn đạm từ cừu, không gà, bò, ngũ cốc, nhẹ bụng cho bé nhạy cảm.'],
            ['nutripaws', 'pate-cho-cho', 'dog', 'young', 'NutriPaws Pate Chó Con Vị Gà & Trứng 100g', 28000, null, 'Thịt gà, trứng gà, sữa dê, nước hầm gà, DHA, canxi', 'Pate mịn dễ ăn, hỗ trợ giai đoạn cai sữa của chó con.'],

            // Dog treats
            ['happy-tail', 'banh-thuong-cho-cho', 'dog', 'adult', 'Happy Tail Xương Gặm Sạch Răng Vị Bò (7 cái)', 89000, 79000, 'Tinh bột khoai tây, thịt bò, glycerin, gelatin, chiết xuất trà xanh', 'Xương gặm giúp giảm mảng bám, làm sạch răng và thơm miệng.'],
            ['happy-tail', 'banh-thuong-cho-cho', 'dog', 'all', 'Happy Tail Bánh Quy Thưởng Vị Gà 200g', 59000, null, 'Bột mì, thịt gà, bột trứng, dầu gà, mật ong', 'Bánh quy giòn thơm vị gà, dùng làm phần thưởng khi huấn luyện.'],
            ['green-paw', 'banh-thuong-cho-cho', 'dog', 'all', 'Green Paw Thịt Gà Sấy Lạnh 100g', 129000, null, '100% thịt gà sấy lạnh', 'Thịt gà nguyên chất sấy lạnh giữ trọn dinh dưỡng, không chất bảo quản.'],
            ['green-paw', 'banh-thuong-cho-cho', 'dog', 'all', 'Green Paw Cá Hồi Sấy Giòn 80g', 99000, null, '100% cá hồi Na Uy sấy giòn', 'Snack cá hồi sấy giòn giàu omega-3, thích hợp thưởng hằng ngày.'],

            // Cat dry food
            ['meowtopia', 'hat-kho-cho-meo', 'cat', 'adult', 'Meowtopia Hạt Mèo Trưởng Thành Vị Cá Ngừ 1,2kg', 219000, null, 'Cá ngừ (26%), bột cá, gạo, ngô, mỡ gà, taurine, vitamin A, D3, E', 'Hương cá ngừ hấp dẫn, bổ sung taurine tốt cho mắt và tim mạch.'],
            ['meowtopia', 'hat-kho-cho-meo', 'cat', 'young', 'Meowtopia Hạt Mèo Con Vị Gà & Sữa 1,2kg', 235000, 209000, 'Thịt gà, bột gà, sữa bột, gạo, dầu cá, DHA, taurine', 'Dinh dưỡng đậm đặc cho mèo con từ 2 đến 12 tháng tuổi.'],
            ['meowtopia', 'hat-kho-cho-meo', 'cat', 'adult', 'Meowtopia Hạt Mèo Triệt Sản Vị Gà & Gạo 2kg', 345000, null, 'Thịt gà, gạo, bột đậu, xơ củ cải, L-carnitine, taurine, khoáng chất', 'Kiểm soát cân nặng với hàm lượng chất béo thấp và L-carnitine.'],
            ['meowtopia', 'hat-kho-cho-meo', 'cat', 'adult', 'Meowtopia Hạt Mèo Lông Dài Vị Cá Hồi 2kg', 369000, null, 'Cá hồi, bột cá, gạo, dầu cá hồi, men bia, biotin, taurine', 'Hỗ trợ tiêu búi lông, cho bộ lông bóng mượt ở mèo lông dài.'],
            ['kitty-joy', 'hat-kho-cho-meo', 'cat', 'adult', 'Kitty Joy Hạt Mèo Vị Hải Sản 1kg', 169000, 149000, 'Bột cá, tôm, ngô, lúa mì, đậu nành, dầu cá, taurine', 'Hương vị hải sản tươi ngon, giá dễ chịu cho mèo trưởng thành.'],
            ['kitty-joy', 'hat-kho-cho-meo', 'cat', 'senior', 'Kitty Joy Hạt Mèo Lớn Tuổi Vị Gà 1,5kg', 255000, null, 'Thịt gà, gạo, yến mạch, dầu cá, glucosamine, taurine, chất chống oxy hóa', 'Dễ tiêu hóa, hỗ trợ khớp và thận cho mèo trên 7 tuổi.'],
            ['green-paw', 'hat-kho-cho-meo', 'cat', 'adult', 'Green Paw Hạt Mèo Không Ngũ Cốc Vị Vịt 1,5kg', 329000, null, 'Thịt vịt (38%), khoai lang, đậu Hà Lan, dầu hạt lanh, taurine, men vi sinh', 'Đạm vịt không gà, bò, cá và ngũ cốc, phù hợp mèo dễ dị ứng.'],
            ['nutripaws', 'hat-kho-cho-meo', 'cat', 'adult', 'NutriPaws Hạt Mèo Vị Bò & Gan 1kg', 189000, null, 'Thịt bò, gan bò, gạo, ngô, mỡ bò, taurine, khoáng chất', 'Vị bò và gan đậm đà cho mèo trưởng thành.'],

            // Cat pate
            ['meowtopia', 'pate-cho-meo', 'cat', 'adult', 'Meowtopia Pate Mèo Vị Cá Ngừ 85g', 22000, null, 'Cá ngừ (50%), nước hầm cá, bột năng, taurine, vitamin', 'Pate cá ngừ nguyên miếng mềm mịn, nhiều nước giúp mèo uống đủ.'],
            ['meowtopia', 'pate-cho-meo', 'cat', 'adult', 'Meowtopia Pate Mèo Vị Gà & Cá Hồi 85g', 24000, null, 'Thịt gà, cá hồi, nước hầm gà, bột năng, taurine', 'Kết hợp gà và cá hồi thơm ngon, giàu đạm.'],
            ['kitty-joy', 'pate-cho-meo', 'cat', 'young', 'Kitty Joy Pate Mèo Con Vị Gà 85g', 21000, null, 'Thịt gà, gan gà, sữa dê, nước hầm gà, DHA, taurine', 'Pate mịn dành cho mèo con tập ăn dặm.'],
            ['kitty-joy', 'pate-cho-meo', 'cat', 'all', 'Kitty Joy Súp Thưởng Vị Cá Ngừ (Hộp 4 Thanh)', 39000, null, 'Cá ngừ, nước hầm cá, bột năng, taurine, vitamin B', 'Súp thưởng dạng thanh tiện lợi, bổ sung nước và tăng sự gắn kết.'],
            ['green-paw', 'pate-cho-meo', 'cat', 'adult', 'Green Paw Pate Mèo Vị Vịt & Bí Đỏ 85g', 29000, null, 'Thịt vịt (45%), bí đỏ, nước hầm vịt, dầu hạt lanh, taurine', 'Pate đơn đạm vịt, không gà, bò, cá, ngũ cốc.'],

            // Cat snacks
            ['kitty-joy', 'snack-cho-meo', 'cat', 'all', 'Kitty Joy Bánh Thưởng Nhân Kem Vị Cá Ngừ 60g', 45000, null, 'Bột mì, cá ngừ, kem sữa, dầu cá, taurine', 'Vỏ giòn nhân kem mềm, mèo nào cũng mê.'],
            ['kitty-joy', 'snack-cho-meo', 'cat', 'all', 'Kitty Joy Cỏ Mèo Catnip Hữu Cơ 30g', 49000, null, '100% lá catnip hữu cơ sấy khô', 'Cỏ mèo giúp thư giãn, kích thích vận động và giảm stress.'],
            ['green-paw', 'snack-cho-meo', 'cat', 'all', 'Green Paw Thịt Gà Sấy Lạnh Cho Mèo 50g', 99000, null, '100% thịt gà sấy lạnh', 'Thịt gà sấy lạnh nguyên chất, dùng làm phần thưởng hoặc trộn cơm.'],

            // Bird food and accessories
            ['aviary-gold', 'thuc-an-cho-chim', 'bird', 'adult', 'Aviary Gold Hạt Hỗn Hợp Cho Vẹt 800g', 149000, null, 'Hạt hướng dương, ngô, lúa mì, yến mạch, đậu phộng, trái cây sấy, vitamin', 'Hỗn hợp hạt và trái cây sấy đa dạng cho vẹt cỡ vừa và lớn.'],
            ['aviary-gold', 'thuc-an-cho-chim', 'bird', 'adult', 'Aviary Gold Hạt Kê Vàng Cho Chim Yến 1kg', 79000, null, 'Hạt kê vàng 100%, đã sàng lọc tạp chất', 'Kê vàng sạch, hạt đều, phù hợp chim yến, sẻ và các loài chim nhỏ.'],
            ['aviary-gold', 'thuc-an-cho-chim', 'bird', 'adult', 'Aviary Gold Cám Chào Mào Cao Cấp 500g', 119000, 99000, 'Bột ngô, bột cá, cám gạo, trứng sấy, côn trùng sấy, vitamin, khoáng chất', 'Cám giàu đạm giúp chào mào lên lông, giữ giọng hót tốt.'],
            ['bobby-pet', 'long-phu-kien-chim', 'bird', null, 'Bobby Pet Lồng Vẹt Mạ Chống Gỉ 60cm', 650000, null, null, 'Lồng thép mạ chống gỉ, khay đáy rút tiện vệ sinh, kèm cầu đậu và 2 chén ăn.'],
            ['bobby-pet', 'long-phu-kien-chim', 'bird', null, 'Bobby Pet Bình Nước Tự Động Cho Chim 250ml', 35000, null, null, 'Bình nước tự động giữ nước sạch, hạn chế rơi vãi.'],
            ['bobby-pet', 'long-phu-kien-chim', 'bird', null, 'Bobby Pet Xương Mực Mài Mỏ (Bộ 3 Cái)', 29000, null, 'Mai mực tự nhiên, giàu canxi', 'Giúp chim mài mỏ và bổ sung canxi tự nhiên.'],

            // Fish food and accessories
            ['aqualife', 'thuc-an-cho-ca', 'fish', 'adult', 'AquaLife Thức Ăn Cá Cảnh Dạng Vảy 100g', 45000, null, 'Bột cá, bột tôm, lúa mì, spirulina, men bia, vitamin C', 'Thức ăn dạng vảy cân bằng dinh dưỡng cho hầu hết các loài cá cảnh nhiệt đới.'],
            ['aqualife', 'thuc-an-cho-ca', 'fish', 'adult', 'AquaLife Thức Ăn Cá Koi Dạng Nổi 1kg', 189000, 169000, 'Bột cá, ngô, lúa mì, men bia, astaxanthin, spirulina', 'Hạt nổi giàu astaxanthin giúp cá Koi lên màu rực rỡ.'],
            ['aqualife', 'thuc-an-cho-ca', 'fish', 'adult', 'AquaLife Thức Ăn Cá Betta Dạng Hạt Nhỏ 20g', 35000, null, 'Bột cá, tôm, lúa mì, bột trứng, vitamin', 'Hạt nhỏ vừa miệng, giàu đạm cho cá Betta khỏe và lên màu.'],
            ['aqualife', 'thuc-an-cho-ca', 'fish', 'all', 'AquaLife Tép Sấy Khô Cho Cá 100g', 55000, null, '100% tép sấy khô tự nhiên', 'Tép sấy giàu đạm, dùng bổ sung cho cá ăn thịt và cá cảnh.'],
            ['aqualife', 'phu-kien-be-ca', 'fish', null, 'AquaLife Máy Lọc Nước Bể Cá Mini 5W', 189000, null, null, 'Máy lọc chạy êm cho bể 30-60 lít, kèm bông lọc thay thế.'],
            ['aqualife', 'phu-kien-be-ca', 'fish', null, 'AquaLife Đèn LED Bể Cá 30cm', 159000, null, null, 'Đèn LED tiết kiệm điện, ánh sáng làm nổi bật màu sắc cá và cây thủy sinh.'],
            ['aqualife', 'phu-kien-be-ca', 'fish', null, 'AquaLife Nước Khử Clo & Ổn Định Nước 250ml', 69000, null, 'Sodium thiosulfate, chiết xuất lô hội', 'Khử clo và kim loại nặng trong nước máy, an toàn cho cá.'],
            ['bobby-pet', 'phu-kien-be-ca', 'fish', null, 'Bobby Pet Bộ Vợt & Dụng Cụ Vệ Sinh Bể Cá', 99000, null, null, 'Bộ vợt, kẹp và cọ vệ sinh giúp bảo trì bể cá dễ dàng.'],

            // Toys
            ['bobby-pet', 'do-choi', 'dog', null, 'Bobby Pet Bóng Cao Su Gai Cho Chó', 39000, null, null, 'Bóng cao su bền, gai massage nướu, phù hợp chó nhỏ và vừa.'],
            ['bobby-pet', 'do-choi', 'dog', null, 'Bobby Pet Dây Thừng Cắn Chơi 3 Nút', 49000, null, null, 'Dây cotton thắt nút giúp chó giải trí và làm sạch răng.'],
            ['kitty-joy', 'do-choi', 'cat', null, 'Kitty Joy Cần Câu Lông Vũ Cho Mèo', 35000, null, null, 'Cần câu lông vũ kích thích bản năng săn mồi của mèo.'],
            ['kitty-joy', 'do-choi', 'cat', null, 'Kitty Joy Chuột Bông Catnip (Bộ 5 Con)', 59000, null, null, 'Bộ 5 chuột bông nhồi catnip, an toàn và dễ thương.'],
            ['bobby-pet', 'do-choi', 'small_pet', null, 'Bobby Pet Bánh Xe Chạy Cho Hamster', 79000, null, null, 'Bánh xe chạy êm, an toàn cho chân hamster.'],

            // Collars and leashes
            ['bobby-pet', 'vong-co-day-dat', 'dog', null, 'Bobby Pet Vòng Cổ Da Cho Chó Size M', 89000, null, null, 'Vòng cổ da mềm có thể điều chỉnh, phù hợp cổ 30-42cm.'],
            ['bobby-pet', 'vong-co-day-dat', 'dog', null, 'Bobby Pet Dây Dắt Tự Cuộn 5m', 149000, 129000, null, 'Dây dắt tự cuộn có nút khóa, tay cầm chống trượt.'],
            ['kitty-joy', 'vong-co-day-dat', 'cat', null, 'Kitty Joy Bộ Yếm Dắt Mèo Kèm Dây', 119000, null, null, 'Yếm ôm vừa vặn, chống thoát hộ khi dẫn mèo đi dạo.'],

            // Beds and carriers
            ['kitty-joy', 'giuong-nha-cho-thu-cung', 'cat', null, 'Kitty Joy Nệm Tròn Lông Mịn Cho Mèo', 179000, null, null, 'Nệm tròn lông mịn ấm áp, đáy chống trượt, giặt máy được.'],
            ['bobby-pet', 'giuong-nha-cho-thu-cung', 'dog', null, 'Bobby Pet Đệm Memory Foam Cho Chó Size L', 459000, 399000, null, 'Đệm memory foam nâng đỡ khớp, vỏ ngoài tháo giặt dễ dàng.'],
            ['bobby-pet', 'giuong-nha-cho-thu-cung', 'all', null, 'Bobby Pet Balo Vận Chuyển Phi Hành Gia', 299000, null, null, 'Balo có cửa sổ trong suốt thoáng khí, tiện đưa thú cưng đi chơi.'],

            // Litter
            ['kitty-joy', 'cat-ve-sinh', 'cat', null, 'Kitty Joy Cát Đậu Nành Hương Trà Xanh 6L', 119000, null, 'Bã đậu nành, tinh bột ngô, chiết xuất trà xanh', 'Cát đậu nành vón cục tốt, ít bụi, khử mùi hương trà xanh.'],
            ['kitty-joy', 'cat-ve-sinh', 'cat', null, 'Kitty Joy Cát Bentonite Vón Cục Hương Chanh 10L', 139000, 119000, 'Bentonite tự nhiên, hương chanh', 'Cát khoáng vón cục nhanh, khử mùi hiệu quả.'],
            ['kitty-joy', 'cat-ve-sinh', 'cat', null, 'Kitty Joy Khay Vệ Sinh Mèo Có Nắp', 249000, null, null, 'Khay vệ sinh có nắp đậy hạn chế mùi và văng cát.'],

            // Bath and hygiene
            ['petspa-garden', 'sua-tam-ve-sinh', 'dog', null, 'PetSpa Garden Sữa Tắm Yến Mạch Cho Chó Da Nhạy Cảm 500ml', 135000, null, 'Chiết xuất yến mạch, lô hội, glycerin, dầu dừa, hương liệu tự nhiên', 'Sữa tắm dịu nhẹ làm dịu da khô và ngứa, không chứa xà phòng.'],
            ['petspa-garden', 'sua-tam-ve-sinh', 'cat', null, 'PetSpa Garden Sữa Tắm Khử Mùi Cho Mèo 300ml', 115000, null, 'Chiết xuất trà xanh, lô hội, glycerin, hương liệu tự nhiên', 'Sữa tắm khử mùi nhẹ nhàng, lông mềm mượt sau khi tắm.'],
            ['petspa-garden', 'sua-tam-ve-sinh', 'all', null, 'PetSpa Garden Khăn Ướt Vệ Sinh Tai & Mắt (80 tờ)', 59000, null, 'Nước tinh khiết, chiết xuất lô hội, glycerin', 'Khăn ướt dịu nhẹ lau sạch tai, mắt và nếp gấp mặt.'],

            // Vitamins and supplements
            ['vetcare-plus', 'vitamin-thuc-pham-chuc-nang', 'all', 'all', 'VetCare Plus Men Tiêu Hóa Cho Chó Mèo 50g', 99000, null, 'Lactobacillus, Bacillus subtilis, inulin, bột gạo', 'Men vi sinh hỗ trợ hệ tiêu hóa, giảm tiêu chảy và đầy hơi.'],
            ['vetcare-plus', 'vitamin-thuc-pham-chuc-nang', 'dog', 'young', 'VetCare Plus Canxi & Vitamin D3 Cho Chó Con (100 viên)', 149000, null, 'Canxi carbonate, vitamin D3, phospho, men bia', 'Bổ sung canxi giúp xương và răng chó con phát triển chắc khỏe.'],
            ['vetcare-plus', 'vitamin-thuc-pham-chuc-nang', 'cat', 'all', 'VetCare Plus Gel Dinh Dưỡng Cho Mèo 120g', 129000, null, 'Dầu cá, mạch nha, taurine, lysine, vitamin tổng hợp, men tiêu hóa', 'Gel dinh dưỡng dễ ăn, giúp mèo tăng cân và phục hồi sau ốm.'],

            // Flea and tick
            ['vetcare-plus', 'tri-ve-ran', 'dog', null, 'VetCare Plus Thuốc Nhỏ Gáy Trị Ve Rận Cho Chó (Chai 3ml)', 159000, null, null, 'Thuốc nhỏ gáy phòng và trị ve, rận, bọ chét kéo dài 4 tuần.'],
            ['vetcare-plus', 'tri-ve-ran', 'cat', null, 'VetCare Plus Xịt Chống Ve Rận Cho Mèo 100ml', 139000, null, null, 'Dạng xịt tiện dụng, an toàn cho mèo từ 8 tuần tuổi.'],
        ];
    }
}
