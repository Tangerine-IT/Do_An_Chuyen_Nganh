# PetShop – Website cửa hàng thú cưng

Ứng dụng Laravel cho cửa hàng thú cưng: bán sản phẩm, đặt lịch dịch vụ, hồ sơ thú cưng thông minh, sổ sức khỏe điện tử và trợ lý AI.

> Trạng thái hiện tại: **Phase 0 – khung dự án**. Chưa có tính năng nghiệp vụ, chỉ có xác thực (Breeze) và bộ công cụ.

## Công nghệ

| Thành phần | Phiên bản / công cụ |
|---|---|
| PHP | 8.2+ (đang dùng 8.2.12) |
| Framework | Laravel 12 |
| CSDL | MySQL (XAMPP), database `petshop` |
| Xác thực | Laravel Breeze (Blade) |
| Giao diện | Blade + Tailwind CSS 3 + Livewire 4 (kèm Alpine.js) + Vite |
| Test | PHPUnit 11 |
| Format code | Laravel Pint |

Lưu ý: Laravel 13 yêu cầu PHP 8.3 nên dự án dùng Laravel 12 (bản mới nhất hỗ trợ PHP 8.2).
Alpine.js đi kèm Livewire, không cài riêng (xem `resources/js/app.js`).

## Cài đặt trên Windows + XAMPP

### 1. Yêu cầu
- PHP 8.2+ (đi kèm XAMPP), bật extension `zip`, `pdo_mysql`, `mbstring`, `fileinfo`, `curl`, `openssl`.
  Bật `zip` bằng cách đổi `;extension=zip` thành `extension=zip` trong `php.ini` (XAMPP: *Config → PHP (php.ini)*).
- [Composer](https://getcomposer.org/), [Node.js](https://nodejs.org/) LTS, [Git](https://git-scm.com/).

### 2. Tạo database
1. Mở **XAMPP Control Panel** và bấm **Start** cho **MySQL**.
2. Vào http://localhost/phpmyadmin và chạy file `database/create_database.sql`
   (tab *Import*, hoặc dán nội dung vào tab *SQL*). Hoặc tạo thủ công database `petshop`
   với collation `utf8mb4_unicode_ci`.

### 3. Cài dự án
```bash
composer install
npm install
copy .env.example .env      # PowerShell/cmd. Git Bash dùng: cp .env.example .env
php artisan key:generate
php artisan migrate
```

`.env` mặc định đã trỏ tới MySQL XAMPP (`root`, mật khẩu rỗng, database `petshop`),
ngôn ngữ `vi`, múi giờ `Asia/Ho_Chi_Minh` (đặt trong `config/app.php`).
Không commit file `.env`.

### 4. Chạy ứng dụng
Mở hai terminal:
```bash
php artisan serve    # http://localhost:8000
npm run dev          # Vite dev server
```
Đăng ký tài khoản tại http://localhost:8000/register để thử Breeze.

## Các lệnh của dự án

| Việc cần làm | Lệnh |
|---|---|
| Cài thư viện PHP | `composer install` |
| Cài thư viện JS | `npm install` |
| Chạy server | `php artisan serve` |
| Chạy Vite (dev) | `npm run dev` |
| Build production | `npm run build` |
| Migrate + seed lại từ đầu | `php artisan migrate:fresh --seed` (xóa toàn bộ dữ liệu!) |
| Chạy test | `php artisan test` |
| Format code | `./vendor/bin/pint` (Git Bash) hoặc `vendor\bin\pint` (cmd/PowerShell) |
| Scheduler khi dev (nhắc lịch tiêm phòng) | `php artisan schedule:work` |

Test dùng SQLite in-memory (cấu hình trong `phpunit.xml`) nên **không cần MySQL** để chạy test.

### Sổ sức khỏe điện tử và scheduler

Phase 11 bổ sung hai tác vụ chạy hằng ngày:

- `pets:send-health-reminders` lúc 08:00: gửi nhắc lịch sức khỏe đến hạn trong 7 ngày hoặc đã quá hạn.
- `pets:send-birthday-coupons` lúc 08:05: tạo coupon sinh nhật và gửi email cho chủ nuôi.

Chạy thử thủ công:

```bash
php artisan pets:send-health-reminders
php artisan pets:send-birthday-coupons
```

Để chạy scheduler liên tục khi phát triển local:

```bash
php artisan schedule:work
```

Vì thông báo được queue, nếu `.env` dùng `QUEUE_CONNECTION=database`, mở thêm terminal:

```bash
php artisan queue:work
```

Nếu dùng `QUEUE_CONNECTION=sync`, email được xử lý ngay trong lệnh và không cần worker.

Trên Windows Task Scheduler, tạo task chạy mỗi phút với:

```text
Program: C:\path\to\php.exe
Arguments: artisan schedule:run
Start in: F:\Xampp\htdocs\Petcare_Shop
```

Để test QR, mở `/account/pets`, vào **Sổ sức khỏe**, quét mã QR bằng điện thoại và kiểm tra các trường được bật trong phần quyền riêng tư.

### Trợ lý AI

Trợ lý chat xuất hiện ở góc phải trên các trang public. Mặc định dùng `rule_based` để chạy local không cần API key. Để dùng API tương thích OpenAI, đặt trong `.env`:

```env
AI_PROVIDER=http
AI_API_KEY=your-server-side-key
AI_MODEL=gpt-4o-mini
AI_ENDPOINT=https://api.openai.com/v1/chat/completions
AI_TIMEOUT=15
```

Không đặt `AI_API_KEY` trong biến `VITE_*` và không gửi key xuống trình duyệt. Context gửi lên server chỉ gồm sản phẩm liên quan và thông tin hồ sơ thú cưng cần thiết; email, mật khẩu và địa chỉ không được gửi cho provider.

### Thanh toán VNPay sandbox

Thêm thông tin merchant vào `.env` (không đưa các giá trị này vào frontend):

```env
VNPAY_TMN_CODE=your-sandbox-tmn-code
VNPAY_HASH_SECRET=your-sandbox-hash-secret
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
```

Đặt `VNPAY_RETURNURL` trong cấu hình merchant là URL `/payment/vnpay/return` và
IPN URL là `/payment/vnpay/ipn` (dùng cùng host mà VNPay sandbox có thể truy cập).
Return URL chỉ hiển thị kết quả cho khách; IPN mới là callback được dùng để cập nhật
đơn hàng. Đơn online chưa thanh toán sẽ hết hạn sau 15 phút và được scheduler hủy,
hoàn lại tồn kho:

```bash
php artisan schedule:work
```

Khách có thể chọn **VNPay Sandbox** ở checkout hoặc bấm **Thanh toán lại** trong
đơn bị thất bại. Admin có thể đánh dấu đơn COD là đã thanh toán tại trang chi tiết
đơn hàng.

Email xác nhận đơn hàng, xác nhận booking và các nhắc nhở sức khỏe đều dùng giao
diện email thương hiệu và được đưa vào queue. Khi chạy local, có thể xem preview tại
`/email-preview`; route này chỉ được đăng ký khi `APP_ENV=local`.

### Góc nhận nuôi

Mở `/adoption` để lọc thú cưng theo loài, độ tuổi và khu vực. Người dùng đăng nhập
có thể mở hồ sơ và gửi một đăng ký đang chờ duyệt cho mỗi bé. Admin quản lý trạng
thái tin tại `/admin/adoption/listings` và duyệt đăng ký tại
`/admin/adoption/applications`; khi trạng thái đăng ký thay đổi, người đăng ký nhận
email và thông báo trong tài khoản. Email được queue nên dùng `php artisan queue:work`
khi `QUEUE_CONNECTION=database`.

### Loyalty và ước tính thức ăn

Điểm được ghi qua `LoyaltyService` vào ledger `loyalty_points`. Đơn hàng và booking
chỉ được cộng điểm khi chuyển sang `completed`; customer xem lịch sử tại
`/account/loyalty` và có thể dùng điểm ở checkout theo giới hạn trong
`config/loyalty.php`.

Trang `/account/feeding` ước tính ngày hết thức ăn dựa trên cân nặng thú cưng,
`package_size_grams` và `daily_grams_per_kg` của sản phẩm. Kết quả luôn là ước tính;
reminder chạy qua:

```bash
php artisan pets:send-feeding-reminders
```

Scheduler đã đăng ký command này lúc 08:10 mỗi ngày. Nút **Mua lại** thêm sản phẩm
vào giỏ với một click.

### Bản đồ thân thiện với thú cưng

Mở `/map` để xem cửa hàng, phòng khám thú y và địa điểm pet-friendly trên nền
Leaflet/OpenStreetMap. Bộ lọc loại địa điểm đồng bộ với marker và danh sách; nút
**Tìm quanh tôi** xin quyền định vị trình duyệt, sắp xếp danh sách theo khoảng cách
và xử lý trường hợp người dùng từ chối quyền. Admin quản lý dữ liệu tại
`/admin/locations`.

## Cấu hình cửa hàng

- `config/petshop.php`: tên cửa hàng, tiền tệ (VND), giờ làm việc, ngưỡng sắp hết hàng.
- Helper `format_vnd(int $amount): string` (`app/helpers.php`), ví dụ `format_vnd(12500000)` → `12.500.000 ₫`.
- Tiền luôn lưu là số nguyên VND, không dùng số thực.
- Ngôn ngữ giao diện: `lang/vi` (thông báo xác thực, validation, phân trang).

## Cấu trúc thư mục chính

```
app/helpers.php           Hàm tiện ích toàn cục (format_vnd)
config/petshop.php        Cài đặt cửa hàng
lang/vi/                  Bản dịch tiếng Việt
resources/js/app.js       Khởi động Livewire + Alpine
resources/views/          Blade views (Breeze)
tests/                    Feature + Unit tests
database/create_database.sql  Tạo database cho phpMyAdmin
```

## Xử lý sự cố

- **`Permission denied` khi `composer create-project` / `composer install` trong thư mục có dấu tiếng Việt hoặc ngoặc `( )`**:
  cài ở đường dẫn không dấu rồi chuyển file vào thư mục dự án.
- **`SQLSTATE[HY000] [2002]`** khi chạy `php artisan migrate`: MySQL chưa được Start trong XAMPP.
- **`MissingAppKeyException`**: chạy `php artisan key:generate`.
