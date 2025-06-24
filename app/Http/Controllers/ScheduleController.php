<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Schedule;
use App\Models\Employee;

class ScheduleController extends Controller
{
    public function index(): Collection
    {
        return Schedule::with(['workWeeks.employee', 'workWeeks.shifts'])
            ->where('user_id', auth()->id())
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function store(Request $request): Schedule
    {
        $request->validate([
            'name'                  => 'nullable|string|max:255',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date|after_or_equal:start_date',
            'work_weeks'            => 'required|array|min:1',
            'work_weeks.*.employee_id'   => 'nullable|integer|exists:employees,id',
            'work_weeks.*.employee_name' => 'required|string|max:255',
            'work_weeks.*.shifts'        => 'nullable|array',
            'work_weeks.*.shifts.*.week_day'   => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'work_weeks.*.shifts.*.date'       => 'nullable|date_format:Y-m-d',
            'work_weeks.*.shifts.*.start_time' => 'required|date_format:H:i:s',
            'work_weeks.*.shifts.*.end_time'   => 'required|date_format:H:i:s|after:work_weeks.*.shifts.*.start_time',
            'work_weeks.*.shifts.*.day_offset' => 'required|integer|in:0,1,2,3,4,5,6',
        ]);

        $schedule = new Schedule();
        $schedule->user_id = auth()->id();
        $schedule->name = $request->input('name', '');
        $schedule->start_date = $request->input('start_date');
        $schedule->end_date = $request->input('end_date');
        $schedule->save();

        foreach ($request->input('work_weeks') as $week) {
            $workWeek = $schedule->workWeeks()->create([
                'employee_id' => $week['employee_id'] ?? null,
                'employee_name' => $week['employee_name'],
            ]);

            foreach ($week['shifts'] as $shift) {
                $workWeek->shifts()->create([
                    'week_day' => $shift['week_day'],
                    'date' => $shift['date'],
                    'start_time' => $shift['start_time'],
                    'end_time' => $shift['end_time'],
                    'day_offset' => $shift['day_offset'],
                ]);
            }
        }

        return $schedule;
    }

    public function show(Schedule $schedule): Schedule
    {
        return $schedule->load([
            'workWeeks.employee',
            'workWeeks.shifts',
        ]);
    }

    public function update(Request $request, Schedule $schedule): Schedule
    {
        $request->validate([
            'name'                  => 'nullable|string|max:255',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date|after_or_equal:start_date',
            'work_weeks'            => 'required|array|min:1',
            'work_weeks.*.employee_id'   => 'nullable|integer|exists:employees,id',
            'work_weeks.*.employee_name' => 'required|string|max:255',
            'work_weeks.*.shifts'        => 'nullable|array',
            'work_weeks.*.shifts.*.week_day'   => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'work_weeks.*.shifts.*.date'       => 'nullable|date_format:Y-m-d',
            'work_weeks.*.shifts.*.start_time' => 'required|date_format:H:i:s',
            'work_weeks.*.shifts.*.end_time'   => 'required|date_format:H:i:s|after:work_weeks.*.shifts.*.start_time',
            'work_weeks.*.shifts.*.day_offset' => 'required|integer|in:0,1,2,3,4,5,6',
        ]);

        $schedule->update($request->only(['name', 'start_date', 'end_date']));

        // Clear existing work weeks and shifts
        $schedule->workWeeks()->delete();

        foreach ($request->input('work_weeks') as $week) {
            $workWeek = $schedule->workWeeks()->create([
                'employee_id' => $week['employee_id'] ?? null,
                'employee_name' => $week['employee_name'],
            ]);

            foreach ($week['shifts'] as $shift) {
                $workWeek->shifts()->create([
                    'week_day' => $shift['week_day'],
                    'date' => $shift['date'],
                    'start_time' => $shift['start_time'],
                    'end_time' => $shift['end_time'],
                    'day_offset' => $shift['day_offset'],
                ]);
            }
        }

        return $schedule;
    }
}
