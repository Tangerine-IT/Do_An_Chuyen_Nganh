<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStaffScheduleController extends Controller
{
    public function edit(Staff $staff): View
    {
        return view('admin.staff.schedule', [
            'staff' => $staff,
            'schedules' => $staff->schedules()->get()->keyBy('weekday'),
        ]);
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $data = $request->validate([
            'schedule' => ['required', 'array'],
            'schedule.*.is_day_off' => ['nullable', 'boolean'],
            'schedule.*.open_time' => ['nullable', 'date_format:H:i'],
            'schedule.*.close_time' => ['nullable', 'date_format:H:i'],
        ]);

        foreach ($data['schedule'] as $weekday => $values) {
            $isDayOff = (bool) ($values['is_day_off'] ?? false);
            $staff->schedules()->updateOrCreate(
                ['weekday' => (int) $weekday],
                [
                    'is_day_off' => $isDayOff,
                    'open_time' => $isDayOff ? null : ($values['open_time'] ?? null),
                    'close_time' => $isDayOff ? null : ($values['close_time'] ?? null),
                ],
            );
        }

        return back()->with('success', __('ui.admin.saved'));
    }
}
