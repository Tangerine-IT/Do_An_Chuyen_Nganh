<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Staff;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use RuntimeException;

class BookingWizardController extends Controller
{
    public function create(Request $request, AvailabilityService $availability): View
    {
        $service = Service::query()->active()->findOrFail($request->integer('service'));
        $date = Carbon::parse($request->input('date', today()->toDateString()));
        $staff = $availability->staffFor($service);

        return view('booking.create', [
            'service' => $service,
            'pets' => $request->user()->pets()->orderBy('name')->get(),
            'staff' => $staff,
            'date' => $date,
            'slots' => $availability->slots($service, $date, $request->integer('staff') ? Staff::find($request->integer('staff')) : null),
        ]);
    }

    public function store(Request $request, BookingService $bookingService): RedirectResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'pet_id' => ['required', 'exists:pets,id'],
            'staff_id' => ['required', 'exists:staff,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $booking = $bookingService->create(
                $request->user(),
                Pet::findOrFail($data['pet_id']),
                Service::findOrFail($data['service_id']),
                Staff::findOrFail($data['staff_id']),
                Carbon::parse($data['starts_at']),
                $data['notes'] ?? null,
            );
        } catch (RuntimeException $exception) {
            return back()->withErrors(['booking' => $exception->getMessage()])->withInput();
        }

        Mail::to($request->user()->email)->queue(new BookingConfirmation($booking->load(['pet', 'service', 'staff'])));

        return redirect()->route('bookings.show', $booking)->with('success', __('ui.booking.created'));
    }
}
