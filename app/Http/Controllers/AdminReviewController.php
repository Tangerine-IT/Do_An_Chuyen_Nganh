<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(): View
    {
        return view('admin.reviews.index', ['reviews' => Review::query()->with(['user', 'product'])->latest()->paginate(20)]);
    }

    public function toggle(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('success', __('ui.admin.saved'));
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', __('ui.admin.deleted'));
    }
}
