<x-app-layout :title="__('ui.design.title')">
    <div class="container-page space-y-16 py-12">
        <header>
            <h1 class="text-4xl">{{ __('ui.design.title') }}</h1>
            <p class="mt-2 text-ink-muted">{{ __('ui.design.intro') }}</p>
            <p class="mt-4 text-sm">
                <a href="{{ url('/design/admin') }}" class="font-semibold text-primary-700 hover:underline">Xem layout admin &rarr;</a>
            </p>
        </header>

        {{-- Colors --}}
        <section aria-labelledby="d-colors">
            <h2 id="d-colors" class="mb-6 text-2xl">Màu sắc</h2>
            @php
                $palettes = [
                    'cream' => ['bg-cream-50', 'bg-cream-100', 'bg-cream-200', 'bg-cream-300'],
                    'primary' => ['bg-primary-50', 'bg-primary-100', 'bg-primary-200', 'bg-primary-300', 'bg-primary-400', 'bg-primary-500', 'bg-primary-600', 'bg-primary-700', 'bg-primary-800', 'bg-primary-900', 'bg-primary-950'],
                    'accent' => ['bg-accent-50', 'bg-accent-100', 'bg-accent-200', 'bg-accent-300', 'bg-accent-400', 'bg-accent-500', 'bg-accent-600', 'bg-accent-700', 'bg-accent-800', 'bg-accent-900'],
                ];
            @endphp
            <div class="space-y-4">
                @foreach ($palettes as $name => $swatches)
                    <div>
                        <p class="mb-2 text-sm font-semibold capitalize text-primary-900">{{ $name }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($swatches as $swatch)
                                <div class="text-center text-[11px] text-ink-muted">
                                    <div class="{{ $swatch }} h-14 w-14 rounded-xl ring-1 ring-line"></div>
                                    {{ \Illuminate\Support\Str::afterLast($swatch, '-') }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Typography --}}
        <section aria-labelledby="d-type">
            <h2 id="d-type" class="mb-6 text-2xl">Kiểu chữ</h2>
            <x-card class="space-y-3">
                <p class="text-5xl font-heading font-extrabold text-primary-900">Nunito 800: Tiêu đề lớn</p>
                <p class="text-3xl font-heading font-extrabold text-primary-900">Tiêu đề phần: Chăm bé yêu</p>
                <p class="text-xl font-heading font-bold text-primary-900">Tiêu đề thẻ: Hạt cho chó trưởng thành</p>
                <p class="text-base">Be Vietnam Pro 400: Nội dung chính dễ đọc, hỗ trợ đầy đủ dấu tiếng Việt như ắ, ằ, ẳ, ẵ, ặ, ơ, ư.</p>
                <p class="text-sm text-ink-muted">Chữ phụ (ink-muted): mô tả ngắn, ghi chú, thời gian.</p>
            </x-card>
        </section>

        {{-- Buttons --}}
        <section aria-labelledby="d-buttons">
            <h2 id="d-buttons" class="mb-6 text-2xl">Nút bấm</h2>
            <x-card class="space-y-6">
                @foreach (['primary', 'accent', 'secondary', 'ghost', 'danger'] as $variant)
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="w-24 text-sm font-semibold capitalize text-ink-muted">{{ $variant }}</span>
                        <x-button :variant="$variant" size="sm">Nhỏ</x-button>
                        <x-button :variant="$variant">Vừa</x-button>
                        <x-button :variant="$variant" size="lg">Lớn</x-button>
                        <x-button :variant="$variant" disabled>Vô hiệu</x-button>
                    </div>
                @endforeach
                <div class="flex flex-wrap items-center gap-3">
                    <span class="w-24 text-sm font-semibold text-ink-muted">Dạng liên kết</span>
                    <x-button href="#d-buttons" variant="accent">Thẻ &lt;a&gt;<x-icon name="arrow-right" class="h-4 w-4" /></x-button>
                </div>
            </x-card>
        </section>

        {{-- Forms --}}
        <section aria-labelledby="d-forms">
            <h2 id="d-forms" class="mb-6 text-2xl">Biểu mẫu</h2>
            <x-card>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-input name="demo_name" label="Họ và tên" placeholder="Nguyễn Văn A" hint="Tên hiển thị trên đơn hàng." required />
                    <x-field label="Email" for="demo-error" error="Địa chỉ email không hợp lệ." required>
                        <input id="demo-error" type="email" value="abc@" class="form-control" aria-invalid="true" aria-describedby="demo-error-error">
                    </x-field>
                    <x-select name="demo_species" label="Loài thú cưng">
                        <option value="dog">Chó</option>
                        <option value="cat">Mèo</option>
                        <option value="bird">Chim</option>
                    </x-select>
                    <x-input name="demo_disabled" label="Ô bị vô hiệu" value="Không thể sửa" disabled />
                    <x-textarea name="demo_notes" label="Ghi chú" rows="3" wrapper-class="md:col-span-2" placeholder="Bé hay sợ tiếng ồn..." />
                    <div class="md:col-span-2">
                        <x-checkbox name="demo_remember" label="Ghi nhớ lựa chọn của tôi" />
                    </div>
                </div>
            </x-card>
        </section>

        {{-- Cards and badges --}}
        <section aria-labelledby="d-cards">
            <h2 id="d-cards" class="mb-6 text-2xl">Card, badge, pet-badge</h2>
            <div class="grid gap-5 md:grid-cols-2">
                <x-card><p class="font-semibold text-primary-900">Card thường</p><p class="mt-1 text-sm text-ink-muted">Bóng đổ nhẹ, bo góc lớn.</p></x-card>
                <x-card hover><p class="font-semibold text-primary-900">Card có hiệu ứng hover</p><p class="mt-1 text-sm text-ink-muted">Di chuột để thấy nổi lên.</p></x-card>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-2">
                @foreach (['neutral', 'primary', 'accent', 'success', 'warning', 'danger', 'info'] as $variant)
                    <x-badge :variant="$variant">{{ ucfirst($variant) }}</x-badge>
                @endforeach
                <x-badge size="sm">Nhỏ</x-badge>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @foreach (['dog', 'cat', 'bird', 'fish', 'small_pet', 'other', 'all'] as $species)
                    <x-pet-badge :species="$species" />
                @endforeach
            </div>
        </section>

        {{-- Rating and price --}}
        <section aria-labelledby="d-rating">
            <h2 id="d-rating" class="mb-6 text-2xl">Đánh giá và giá</h2>
            <x-card class="grid gap-6 md:grid-cols-2">
                <div class="space-y-3">
                    <x-rating-stars :rating="5" :count="128" />
                    <x-rating-stars :rating="4.3" :count="42" />
                    <x-rating-stars :rating="2.5" :count="7" size="h-6 w-6" />
                    <x-rating-stars :rating="0" :count="0" />
                </div>
                <div class="space-y-3">
                    <x-price :price="289000" />
                    <x-price :price="329000" :sale-price="289000" />
                    <x-price :price="1050000" :sale-price="899000" size="lg" />
                    <x-price :price="35000" size="sm" />
                </div>
            </x-card>
        </section>

        {{-- Product card --}}
        <section aria-labelledby="d-products">
            <h2 id="d-products" class="mb-6 text-2xl">Product card và skeleton</h2>
            <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <p class="mb-3 mt-8 text-sm font-semibold text-ink-muted">Đang tải (skeleton)</p>
            <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @foreach (range(1, 4) as $i)
                    <x-product-card-skeleton />
                @endforeach
            </div>
        </section>

        {{-- Empty state --}}
        <section aria-labelledby="d-empty">
            <h2 id="d-empty" class="mb-6 text-2xl">Trạng thái trống</h2>
            <x-empty-state title="Không tìm thấy sản phẩm" description="Thử bỏ bớt bộ lọc hoặc tìm với từ khóa khác nhé." icon="search">
                <x-button variant="secondary">Xóa bộ lọc</x-button>
            </x-empty-state>
        </section>

        {{-- Toast and modal --}}
        <section aria-labelledby="d-feedback">
            <h2 id="d-feedback" class="mb-6 text-2xl">Toast và modal</h2>
            <x-card x-data class="flex flex-wrap gap-3">
                <x-button variant="primary" @click="$dispatch('toast', { type: 'success', message: 'Đã thêm sản phẩm vào giỏ hàng.' })">Toast thành công</x-button>
                <x-button variant="danger" @click="$dispatch('toast', { type: 'error', message: 'Không thể kết nối. Vui lòng thử lại.' })">Toast lỗi</x-button>
                <x-button variant="secondary" @click="$dispatch('toast', { type: 'warning', message: 'Sản phẩm này sắp hết hàng.' })">Toast cảnh báo</x-button>
                <x-button variant="secondary" @click="$dispatch('toast', { type: 'info', message: 'Đơn hàng của bạn đang được xử lý.' })">Toast thông tin</x-button>
                <x-button variant="accent" @click="$dispatch('open-modal', 'demo-modal')">Mở modal</x-button>
            </x-card>

            <x-modal name="demo-modal" label="Xác nhận xóa" maxWidth="md" focusable>
                <div class="p-6">
                    <h2 class="text-xl">Xóa sản phẩm này?</h2>
                    <p class="mt-2 text-sm text-ink-muted">Hành động này không thể hoàn tác. Dữ liệu đơn hàng cũ vẫn được giữ nguyên.</p>
                    <div class="mt-6 flex justify-end gap-3">
                        <x-button variant="secondary" x-on:click="$dispatch('close')">{{ __('ui.common.cancel') }}</x-button>
                        <x-button variant="danger" x-on:click="$dispatch('close')">{{ __('ui.common.confirm') }}</x-button>
                    </div>
                </div>
            </x-modal>
        </section>

        {{-- Pagination --}}
        <section aria-labelledby="d-pagination">
            <h2 id="d-pagination" class="mb-6 text-2xl">Phân trang</h2>
            {{ $paginator->links() }}
        </section>
    </div>
</x-app-layout>
