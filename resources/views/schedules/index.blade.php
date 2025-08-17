<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - {{ $schedule->name ?: 'Weekly Schedule' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .schedule-table th,
        .schedule-table td {
            border: 1px solid #333;
            padding: 3px;
            text-align: center;
            vertical-align: top;
        }
        
        .schedule-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }
        
        .employee-name {
            background-color: #f9f9f9;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
            width: 120px;
        }

        .employee-phone {
            font-size: 10px;
        }
        
        .day-header {
            font-weight: bold;
            width: 100px;
        }
        
        .shift-cell {
            position: relative;
        }
        
        .shift-info {
            font-size: 11px;
        }
        
        .shift-time {
            font-weight: bold;
        }
        
        .empty-shift {
            color: #ccc;
            font-style: italic;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $schedule->name ?: 'Weekly Schedule' }}</h1>
        <div class="schedule-info">
            <strong>Schedule Period:</strong> 
            {{ \Carbon\Carbon::parse($schedule->start_date)->format('M j, Y') }} - 
            {{ \Carbon\Carbon::parse($schedule->end_date)->format('M j, Y') }}
        </div>
    </div>
    <table class="schedule-table">
        <thead>
            <tr>
                <th class="employee-name">Employee</th>
                @for ($day = 0; $day < 7; $day++)
                    @php
                        $currentDate = \Carbon\Carbon::parse($schedule->start_date)->addDays($day);
                        $dayName = $currentDate->format('D');
                        $dayDate = $currentDate->format('n/j');
                    @endphp
                    <th class="day-header">
                        {{ $dayName }}<br>
                        {{ $dayDate }}
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($schedule->workWeeks as $workWeek)
                <tr>
                    <td class="employee-name">
                        {{ $workWeek->employee_name }}
                        <br>
                        <span class="employee-phone">
                            {{ $workWeek->employee?->phone }}
                        </span>
                    </td>
                    @for ($day = 0; $day < 7; $day++)
                        @php
                            // Find shift for this day
                            $shift = $workWeek->shifts->where('day_offset', $day)->first();
                        @endphp
                        <td class="shift-cell">
                            @if ($shift)
                                <div class="shift-info">
                                    <div class="shift-time">
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }}<br>
                                        {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }}
                                    </div>
                                </div>
                            @else
                                <div class="empty-shift">Off</div>
                            @endif
                        </td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; font-style: italic; color: #666;">
                        No employees scheduled for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Created with Timelio®</p>
    </div>
</body>
</html>