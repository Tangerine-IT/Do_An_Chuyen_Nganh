<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\User;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Blog posts (5 published, 1 draft) written by the admin. Requires UserSeeder.
 */
class PostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@petshop.test')->firstOrFail();

        foreach ($this->posts() as $index => $data) {
            $slug = Str::slug($data['title']);
            $published = $data['published'];

            $author->posts()->create([
                'title' => $data['title'],
                'slug' => $slug,
                'excerpt' => $data['excerpt'],
                'category' => $data['category'],
                'body' => $data['body'],
                'cover_image_path' => PlaceholderImage::make('placeholders/posts/'.$slug.'.svg', $data['emoji'], 'Góc chăm sóc thú cưng', '#FBEFD9'),
                'status' => $published ? PostStatus::Published : PostStatus::Draft,
                'published_at' => $published ? now()->subDays(($index + 1) * 6) : null,
                'meta_title' => $data['title'].' | '.config('petshop.name'),
                'meta_description' => $data['excerpt'],
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function posts(): array
    {
        return [
            [
                'title' => '5 dấu hiệu cho thấy chó mèo bị dị ứng thức ăn',
                'category' => 'Dinh dưỡng',
                'excerpt' => 'Ngứa, rụng lông, tiêu chảy: đây là những dấu hiệu thường gặp khi thú cưng dị ứng với một thành phần trong khẩu phần ăn.',
                'emoji' => '🔍',
                'published' => true,
                'body' => "Dị ứng thức ăn ở chó mèo thường bắt nguồn từ đạm động vật như gà, bò, cá hoặc từ ngũ cốc như lúa mì, ngô. Khác với không dung nạp, dị ứng liên quan đến phản ứng của hệ miễn dịch nên triệu chứng có thể xuất hiện ở da lẫn đường tiêu hóa.\n\nHãy chú ý khi bé gãi liên tục (nhất là tai, bụng, chân), da đỏ hoặc nổi mẩn, rụng lông từng mảng, viêm tai tái đi tái lại, hoặc nôn mửa và tiêu chảy kéo dài. Nếu có từ hai dấu hiệu trở lên, bạn nên đưa bé đi khám.\n\nCách xác định tác nhân phổ biến là cho bé ăn chế độ loại trừ trong 8 đến 12 tuần với một nguồn đạm mới như cừu hoặc vịt. Sau đó thêm lại từng nguyên liệu để tìm ra thủ phạm. Hãy ghi lại các nguyên liệu gây dị ứng vào hồ sơ thú cưng để PetShop gợi ý sản phẩm an toàn cho bé.",
            ],
            [
                'title' => 'Lịch tiêm phòng cho chó con từ 6 tuần tuổi',
                'category' => 'Sức khỏe',
                'excerpt' => 'Tiêm đúng lịch giúp chó con phòng được Care, Parvo, Viêm gan và Dại. Cùng xem lịch tiêm tham khảo.',
                'emoji' => '💉',
                'published' => true,
                'body' => "Chó con nhận kháng thể từ sữa mẹ trong những tuần đầu, nhưng lượng kháng thể này giảm dần nên cần tiêm phòng để bảo vệ bé. Lịch tham khảo: mũi 1 lúc 6 đến 8 tuần, mũi 2 sau mũi đầu 3 đến 4 tuần, mũi 3 sau mũi 2 khoảng 3 đến 4 tuần, kèm mũi phòng Dại từ 12 tuần tuổi.\n\nTrước khi tiêm, bé cần khỏe mạnh, không sốt, đã tẩy giun. Sau tiêm, hãy theo dõi bé 24 giờ, hạn chế tắm và cho đi chơi nơi đông chó trong khoảng 2 tuần.\n\nSau đó, chó trưởng thành cần tiêm nhắc lại mỗi năm một lần. Bạn có thể lưu ngày tiêm và ngày hẹn kế tiếp trong sổ sức khỏe điện tử, hệ thống sẽ nhắc bạn trước hạn 7 ngày.",
            ],
            [
                'title' => 'Cách chọn thức ăn phù hợp cho mèo triệt sản',
                'category' => 'Dinh dưỡng',
                'excerpt' => 'Mèo sau triệt sản dễ tăng cân. Chọn thức ăn ít béo, đủ đạm và bổ sung L-carnitine sẽ giúp bé giữ dáng khỏe mạnh.',
                'emoji' => '🐱',
                'published' => true,
                'body' => "Sau khi triệt sản, nhu cầu năng lượng của mèo giảm khoảng 20 đến 30%, trong khi cảm giác thèm ăn lại tăng. Đây là lý do mèo sau triệt sản thường tăng cân nhanh.\n\nHãy ưu tiên thức ăn có đạm cao, chất béo vừa phải và bổ sung xơ để bé no lâu. Các thành phần như L-carnitine hỗ trợ chuyển hóa mỡ, còn taurine giúp bảo vệ tim và mắt.\n\nNên chia khẩu phần thành 3 đến 4 bữa nhỏ mỗi ngày, cho bé vận động bằng đồ chơi như cần câu lông vũ và cân bé mỗi tháng. Bạn có thể ghi lại cân nặng trong sổ sức khỏe để theo dõi xu hướng.",
            ],
            [
                'title' => 'Chăm sóc cá Betta cho người mới bắt đầu',
                'category' => 'Chăm sóc',
                'excerpt' => 'Betta đẹp và dễ nuôi nhưng vẫn cần đúng nhiệt độ, nước sạch và chế độ ăn hợp lý.',
                'emoji' => '🐟',
                'published' => true,
                'body' => "Cá Betta thích nước ấm từ 25 đến 28 độ C, bể tối thiểu 10 lít, có nắp đậy vì cá có thể nhảy ra ngoài. Hãy dùng nước đã khử clo và thay khoảng 25% nước mỗi tuần.\n\nBetta là loài ăn thịt, nên chọn thức ăn dạng hạt nhỏ giàu đạm, cho ăn 2 lần mỗi ngày với lượng bé ăn hết trong 2 phút. Cho ăn quá nhiều dễ gây táo bón và ô nhiễm nước.\n\nTránh nuôi chung hai cá Betta đực. Bạn có thể thêm cây thủy sinh và hang nhỏ để cá có nơi trú ẩn, giảm stress và lên màu đẹp hơn.",
            ],
            [
                'title' => 'Mẹo tắm cho chó tại nhà đúng cách',
                'category' => 'Chăm sóc',
                'excerpt' => 'Tắm quá thường xuyên hay dùng sai sữa tắm đều có thể làm hỏng da. Cùng tìm hiểu cách tắm đúng cho chó.',
                'emoji' => '🛁',
                'published' => true,
                'body' => "Hầu hết chó khỏe mạnh chỉ cần tắm 2 đến 4 tuần một lần. Tắm quá nhiều làm mất lớp dầu tự nhiên, khiến da khô và ngứa. Luôn dùng sữa tắm dành riêng cho thú cưng vì độ pH da chó khác với người.\n\nChải lông trước khi tắm để gỡ rối, dùng nước ấm vừa phải, tránh để nước vào tai và mắt. Massage sữa tắm từ cổ xuống đuôi, xả thật sạch rồi dùng khăn thấm khô trước khi sấy ở mức gió ấm.\n\nVới bé có da nhạy cảm, hãy chọn sữa tắm yến mạch hoặc lô hội. Nếu bé có vấn đề về da kéo dài, hãy hỏi ý kiến bác sĩ thú y trước khi đổi sản phẩm.",
            ],
            [
                'title' => 'Chuẩn bị gì khi đưa thú cưng đi du lịch',
                'category' => 'Lối sống',
                'excerpt' => 'Danh sách đồ cần chuẩn bị và lưu ý di chuyển để chuyến đi an toàn cho cả bạn và thú cưng.',
                'emoji' => '🧳',
                'published' => false,
                'body' => 'Bài viết đang được hoàn thiện: danh sách vật dụng, giấy tờ tiêm phòng cần mang theo, mẹo giữ bình tĩnh cho thú cưng khi di chuyển bằng ô tô và các địa điểm thân thiện với thú cưng.',
            ],
        ];
    }
}
