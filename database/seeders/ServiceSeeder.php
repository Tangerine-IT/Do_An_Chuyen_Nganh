<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * 8 services and 3 service providers. Requires UserSeeder (the staff account is linked to one provider).
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = collect($this->services())->map(function (array $data): Service {
            return Service::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'duration_minutes' => $data['duration'],
                'price' => $data['price'],
                'image_path' => PlaceholderImage::make(
                    'placeholders/services/'.Str::slug($data['name']).'.svg',
                    $data['emoji'],
                    $data['name'],
                    '#E4F1E8',
                ),
            ]);
        })->keyBy('slug');

        $staffUserId = User::where('email', 'staff@petshop.test')->value('id');

        foreach ($this->staff() as $data) {
            $staff = Staff::create([
                'user_id' => $data['linked_to_staff_account'] ? $staffUserId : null,
                'name' => $data['name'],
                'title' => $data['title'],
                'phone' => $data['phone'],
                'bio' => $data['bio'],
                'photo_path' => PlaceholderImage::make(
                    'placeholders/staff/'.Str::slug($data['name']).'.svg',
                    $data['emoji'],
                    $data['name'],
                    '#FBEFD9',
                ),
            ]);

            $staff->services()->attach(
                $services->only(array_map(fn (string $name): string => Str::slug($name), $data['services']))->pluck('id')
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function services(): array
    {
        return [
            ['name' => 'Tắm spa thư giãn', 'description' => 'Tắm, sấy, chải lông và massage thư giãn với sữa tắm chiết xuất thiên nhiên.', 'duration' => 60, 'price' => 180000, 'emoji' => '🛁'],
            ['name' => 'Cắt tỉa lông tạo kiểu', 'description' => 'Cắt tỉa, tạo kiểu theo giống và theo yêu cầu của chủ nuôi.', 'duration' => 90, 'price' => 250000, 'emoji' => '✂️'],
            ['name' => 'Combo tắm và cắt tỉa', 'description' => 'Trọn gói tắm spa, cắt tỉa lông, vệ sinh tai và cắt móng.', 'duration' => 120, 'price' => 380000, 'emoji' => '💇'],
            ['name' => 'Tiêm phòng', 'description' => 'Tiêm vắc-xin phòng bệnh theo lịch, kèm ghi vào sổ sức khỏe điện tử.', 'duration' => 30, 'price' => 150000, 'emoji' => '💉'],
            ['name' => 'Khám sức khỏe tổng quát', 'description' => 'Bác sĩ thú y thăm khám, đánh giá thể trạng và tư vấn dinh dưỡng.', 'duration' => 45, 'price' => 200000, 'emoji' => '🩺'],
            ['name' => 'Tẩy giun định kỳ', 'description' => 'Tẩy giun an toàn theo cân nặng, nhắc lịch lần tiếp theo.', 'duration' => 20, 'price' => 90000, 'emoji' => '💊'],
            ['name' => 'Vệ sinh tai và cắt móng', 'description' => 'Vệ sinh tai, cắt móng và làm sạch vùng mắt nhẹ nhàng.', 'duration' => 30, 'price' => 80000, 'emoji' => '🐾'],
            ['name' => 'Lưu trú theo ngày', 'description' => 'Phòng lưu trú sạch sẽ, có camera, cho ăn và dắt đi dạo mỗi ngày.', 'duration' => 1440, 'price' => 250000, 'emoji' => '🏠'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function staff(): array
    {
        return [
            [
                'name' => 'Nguyễn Thị Lan',
                'title' => 'Chuyên viên grooming',
                'phone' => '0903000001',
                'bio' => '8 năm kinh nghiệm cắt tỉa tạo kiểu cho chó mèo các giống.',
                'emoji' => '👩‍🔧',
                'linked_to_staff_account' => false,
                'services' => ['Tắm spa thư giãn', 'Cắt tỉa lông tạo kiểu', 'Combo tắm và cắt tỉa', 'Vệ sinh tai và cắt móng'],
            ],
            [
                'name' => 'Trần Văn Khoa',
                'title' => 'Bác sĩ thú y',
                'phone' => '0903000002',
                'bio' => 'Tốt nghiệp thú y, chuyên nội khoa và tiêm phòng cho thú cưng.',
                'emoji' => '👨‍⚕️',
                'linked_to_staff_account' => false,
                'services' => ['Tiêm phòng', 'Khám sức khỏe tổng quát', 'Tẩy giun định kỳ'],
            ],
            [
                'name' => 'Lê Hoàng Yến',
                'title' => 'Nhân viên spa & chăm sóc',
                'phone' => '0901000002',
                'bio' => 'Nhẹ nhàng, yêu động vật, phụ trách spa và chăm sóc lưu trú.',
                'emoji' => '👩',
                'linked_to_staff_account' => true,
                'services' => ['Tắm spa thư giãn', 'Combo tắm và cắt tỉa', 'Vệ sinh tai và cắt móng', 'Lưu trú theo ngày'],
            ],
        ];
    }
}
