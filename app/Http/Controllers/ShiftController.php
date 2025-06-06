<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * Store new shifts for a given date.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'entries' => 'required|array',
            'entries.*.employee_id' => 'required|integer|exists:employees,id',
            'entries.*.start_time' => 'nullable|date_format:H:i',
            'entries.*.end_time' => 'nullable|date_format:H:i',
        ]);

        $userId = Auth::id();

        $shifts = [];
        foreach ($validated['entries'] as $entry) {
            $shifts[] = Shift::create([
                Shift::USER_ID => $userId,
                Shift::EMPLOYEE_ID => $entry['employee_id'],
                Shift::DATE => $validated['date'],
                Shift::START_TIME => $entry['start_time'] ?? null,
                Shift::END_TIME => $entry['end_time'] ?? null,
            ]);
        }

        return $shifts;
    }
}
