<x-admin-layout :title="__('ui.admin.nav.orders')">
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['Doanh thu hôm nay', '4.280.000 ₫', 'chart'], ['Đơn mới', '12', 'cart'], ['Đang chờ xác nhận', '5', 'calendar'], ['Khách hàng mới', '3', 'users']] as [$label, $value, $icon])
                <x-card class="flex items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-100 text-primary-700">
                        <x-icon :name="$icon" class="h-6 w-6" />
                    </span>
                    <div>
                        <p class="text-sm text-ink-muted">{{ $label }}</p>
                        <p class="font-heading text-2xl font-extrabold text-primary-900">{{ $value }}</p>
                    </div>
                </x-card>
            @endforeach
        </div>

        <x-card :padded="false">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line p-4">
                <h2 class="text-lg">Đơn hàng gần đây</h2>
                <form role="search" class="w-full sm:w-72">
                    <label for="admin-search" class="sr-only">{{ __('ui.search.label') }}</label>
                    <input id="admin-search" type="search" class="form-control rounded-full py-2" placeholder="Tìm theo mã đơn, tên khách...">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[40rem] text-left text-sm">
                    <caption class="sr-only">Danh sách đơn hàng mẫu</caption>
                    <thead class="bg-cream-100 text-xs uppercase tracking-wide text-ink-muted">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Mã đơn</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Khách hàng</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Trạng thái</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">Tổng tiền</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Thao tác</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ([
                            ['PS261019000123', 'Nguyễn Minh Anh', 'pending', 'warning', 618000],
                            ['PS261019000122', 'Trần Quốc Bảo', 'shipping', 'info', 1249000],
                            ['PS261018000121', 'Lê Thu Hà', 'completed', 'success', 389000],
                            ['PS261018000120', 'Phạm Đức Huy', 'cancelled', 'danger', 215000],
                        ] as [$code, $customer, $status, $tone, $total])
                            <tr class="hover:bg-cream-50">
                                <th scope="row" class="px-4 py-3 font-semibold text-primary-900">{{ $code }}</th>
                                <td class="px-4 py-3">{{ $customer }}</td>
                                <td class="px-4 py-3"><x-badge :variant="$tone">{{ __('enums.order_status.'.$status) }}</x-badge></td>
                                <td class="px-4 py-3 text-right font-semibold">{{ format_vnd($total) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-button variant="ghost" size="sm" x-data @click="$dispatch('open-modal', 'confirm-delete')">Xóa</x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <x-modal name="confirm-delete" label="Xác nhận xóa" maxWidth="md" focusable>
        <div class="p-6">
            <h2 class="text-xl">Xóa đơn hàng này?</h2>
            <p class="mt-2 text-sm text-ink-muted">Mọi thao tác xóa trong trang quản trị đều cần xác nhận như thế này.</p>
            <div class="mt-6 flex justify-end gap-3">
                <x-button variant="secondary" x-on:click="$dispatch('close')">{{ __('ui.common.cancel') }}</x-button>
                <x-button variant="danger" x-on:click="$dispatch('close')">{{ __('ui.common.confirm') }}</x-button>
            </div>
        </div>
    </x-modal>
</x-admin-layout>
