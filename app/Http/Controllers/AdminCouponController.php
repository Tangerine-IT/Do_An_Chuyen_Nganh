<?php

namespace App\Http\Controllers;

use App\Enums\CouponType;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCouponController extends Controller
{
    public function index(): View
    {
        return view('admin.coupons.index', ['coupons' => Coupon::query()->latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.coupons.form', ['coupon' => new Coupon(['is_active' => true]), 'types' => CouponType::cases(), 'categories' => Category::query()->active()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Coupon::create($this->validated($request));

        return redirect()->route('admin.coupons.index')->with('success', __('ui.admin.saved'));
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', ['coupon' => $coupon, 'types' => CouponType::cases(), 'categories' => Category::query()->active()->orderBy('name')->get()]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validated($request));

        return redirect()->route('admin.coupons.index')->with('success', __('ui.admin.saved'));
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('success', __('ui.admin.deleted'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'integer', 'min:1'],
            'min_order_total' => ['required', 'integer', 'min:0'],
            'max_discount' => ['nullable', 'integer', 'min:1'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
