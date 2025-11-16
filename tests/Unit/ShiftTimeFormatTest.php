<?php

namespace Tests\Unit;

use App\Models\Shift;
use App\Models\WorkWeek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftTimeFormatTest extends TestCase
{
    use RefreshDatabase;
    public function test_shift_stores_time_in_database_as_h_i_s_format(): void
    {
        $workWeek = WorkWeek::factory()->create();
        
        $shift = Shift::create([
            'work_week_id' => $workWeek->id,
            'week_day' => 'monday',
            'start_time' => '08:00',
            'end_time' => '17:00',
        ]);

        // Refresh to get the raw database value
        $shift->refresh();
        
        // Check the raw database value includes seconds
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}:\d{2}$/', $shift->getRawOriginal('start_time'));
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}:\d{2}$/', $shift->getRawOriginal('end_time'));
    }

    public function test_shift_returns_time_in_h_i_format(): void
    {
        $workWeek = WorkWeek::factory()->create();
        
        $shift = Shift::create([
            'work_week_id' => $workWeek->id,
            'week_day' => 'monday',
            'start_time' => '08:00',
            'end_time' => '17:00',
        ]);

        // Access through attribute accessor
        $this->assertEquals('08:00', $shift->start_time);
        $this->assertEquals('17:00', $shift->end_time);
    }

    public function test_shift_handles_midnight_correctly(): void
    {
        $workWeek = WorkWeek::factory()->create();
        
        $shift = Shift::create([
            'work_week_id' => $workWeek->id,
            'week_day' => 'monday',
            'start_time' => '00:00',
            'end_time' => '23:59',
        ]);

        $this->assertEquals('00:00', $shift->start_time);
        $this->assertEquals('23:59', $shift->end_time);
    }

    public function test_shift_accepts_h_i_s_format_directly(): void
    {
        $workWeek = WorkWeek::factory()->create();
        
        $shift = Shift::create([
            'work_week_id' => $workWeek->id,
            'week_day' => 'monday',
            'start_time' => '08:30:00',
            'end_time' => '17:45:00',
        ]);

        // Should still return HH:MM format
        $this->assertEquals('08:30', $shift->start_time);
        $this->assertEquals('17:45', $shift->end_time);
    }
}
