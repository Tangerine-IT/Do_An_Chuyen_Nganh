<?php

namespace App\Livewire;

use App\Mail\BookingConfirmation;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Staff;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use RuntimeException;

class BookingWizard extends Component
{
    public int $serviceId;

    public string $date;

    public string $staffId = '';

    public string $petId = '';

    public string $startsAt = '';

    public string $notes = '';

    public function mount(int $serviceId, ?string $date = null, ?string $staffId = null, ?string $petId = null, ?AvailabilityService $availability = null): void
    {
        $this->serviceId = $serviceId;
        $this->date = $date ?: today()->toDateString();
        $service = Service::query()->active()->findOrFail($serviceId);
        $staff = ($availability ?? app(AvailabilityService::class))->staffFor($service);
        $this->staffId = $staffId ?: (string) ($staff->first()?->id ?? '');
        if ($petId !== null && auth()->user()->pets()->whereKey($petId)->exists()) {
            $this->petId = $petId;
        }
    }

    public function updatedDate(): void
    {
        $this->startsAt = '';
    }

    public function updatedStaffId(): void
    {
        $this->startsAt = '';
    }

    public function submit(BookingService $bookingService): void
    {
        $this->validate([
            'petId' => ['required', 'exists:pets,id'],
            'staffId' => ['required', 'exists:staff,id'],
            'startsAt' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $booking = $bookingService->create(
                auth()->user(),
                Pet::findOrFail($this->petId),
                Service::findOrFail($this->serviceId),
                Staff::findOrFail($this->staffId),
                Carbon::parse($this->startsAt),
                $this->notes ?: null,
            );
        } catch (RuntimeException $exception) {
            $this->addError('startsAt', $exception->getMessage());

            return;
        }

        Mail::to(auth()->user()->email)->queue(new BookingConfirmation($booking->load(['pet', 'service', 'staff'])));
        $this->redirectRoute('bookings.show', $booking);
    }

    public function render(AvailabilityService $availability): View
    {
        $service = Service::query()->active()->findOrFail($this->serviceId);
        $staff = $availability->staffFor($service);
        $selectedStaff = $this->staffId !== '' ? Staff::find((int) $this->staffId) : null;

        return view('livewire.booking-wizard', [
            'service' => $service,
            'pets' => auth()->user()->pets()->orderBy('name')->get(),
            'staff' => $staff,
            'slots' => $availability->slots($service, Carbon::parse($this->date), $selectedStaff),
        ]);
    }
}
