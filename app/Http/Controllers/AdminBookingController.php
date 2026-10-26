<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use App\Services\BookingService;
use App\Services\LoyaltyService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookingController extends Controller
{
    public function create(): View
    {
        return view('admin.bookings.create', [
            'users' => User::query()->where('role', 'customer')->orderBy('name')->get(),
            'services' => Service::query()->active()->orderBy('name')->get(),
            'staff' => Staff::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, BookingService $bookingService): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'pet_id' => ['required', 'exists:pets,id'],
            'service_id' => ['required', 'exists:services,id'],
            'staff_id' => ['required', 'exists:staff,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $user = User::findOrFail($data['user_id']);

        try {
            $bookingService->create(
                $user,
                Pet::findOrFail($data['pet_id']),
                Service::findOrFail($data['service_id']),
                Staff::findOrFail($data['staff_id']),
                Carbon::parse($data['starts_at']),
                $data['notes'] ?? null,
            );
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['booking' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('admin.bookings.index')->with('success', __('ui.admin.saved'));
    }

    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['user', 'pet', 'service', 'staff'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('starts_at', $request->input('date')))
            ->orderBy('starts_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'scheduleStaff' => Staff::query()->active()->orderBy('name')->first(),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking, LoyaltyService $loyalty): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show']]);
        $booking->update(['status' => BookingStatus::from($data['status'])]);
        if ($booking->status === BookingStatus::Completed) {
            $loyalty->earnBooking($booking->fresh('user'));
        }

        return back()->with('success', __('ui.admin.saved'));
    }
}
