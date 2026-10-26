<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.bookings', ['bookings' => $request->user()->bookings()->with(['pet', 'service', 'staff'])->latest('starts_at')->paginate(10)]);
    }

    public function show(Booking $booking): View
    {
        Gate::authorize('view', $booking);

        return view('account.bookings-show', ['booking' => $booking->load(['pet', 'service', 'staff'])]);
    }

    public function cancel(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        Gate::authorize('update', $booking);
        try {
            $service->cancel($request->user(), $booking);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['booking' => $exception->getMessage()]);
        }

        return back()->with('success', __('ui.booking.cancelled'));
    }

    public function reschedule(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        Gate::authorize('update', $booking);
        $data = $request->validate(['starts_at' => ['required', 'date', 'after:now']]);

        try {
            $service->reschedule($request->user(), $booking, Carbon::parse($data['starts_at']));
        } catch (RuntimeException $exception) {
            return back()->withErrors(['booking' => $exception->getMessage()])->withInput();
        }

        return back()->with('success', __('ui.booking.rescheduled'));
    }
}
