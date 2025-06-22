import { type Schedule } from '@/types/Schedule';
import { type WorkWeek } from '@/types/WorkWeek';
import { type Employee } from '@/types/Employee';
import { type Shift } from '@/types/Shift';
import { type DateValue, getDayOfWeek, getLocalTimeZone, DateFormatter } from '@internationalized/date';

const weekDaysLowerCase = [
    'sunday',
    'monday',
    'tuesday',
    'wednesday',
    'thursday',
    'friday',
    'saturday'
];

const weekDaysShort = [
    'Sun.',
    'Mon.',
    'Tue.',
    'Wed.',
    'Thu.',
    'Fri.',
    'Sat.'
];

const colors = [
    'bg-sky-600',
    'bg-rose-700',
    'bg-violet-600',
    'bg-emerald-600',
    'bg-orange-600',
];

const header_df = new DateFormatter('en-US', {
    dateStyle: 'short',
});

const removeWorkWeek = (workWeek: WorkWeek, schedule: Schedule): void => {
    const index = schedule.work_weeks?.indexOf(workWeek);

    schedule.work_weeks = schedule.work_weeks?.filter((ww, i) => {
        return i !== index;
    });
}

const addWorkWeek = (employee: Employee|null, schedule: Schedule, selectedEmployee: Employee|null, newEmployee: Employee): void => {
    if (employee && schedule.work_weeks) {
        schedule.work_weeks.push({
            employee_id: employee.id,
            employee_name: employee.full_name || (employee.first_name + ' ' + employee.last_name),
            shifts: [],
        });
        selectedEmployee = null;
        newEmployee.full_name = '';
    }
}

const addShift = (workWeek: WorkWeek, dayOffset: number, shift: Shift, startDate: DateValue|undefined): void => {
    const day = weekDay(dayOffset, startDate);

    if (!workWeek.shifts) {
        workWeek.shifts = [];
    }

    const newShift = {
        week_day: day,
        date: startDate?.add({ days: dayOffset }).toString() || '',
        day_offset: dayOffset,
        start_time: shift.start_time,
        end_time: shift.end_time,
    };

    workWeek.shifts.push(newShift);
}

const removeShift = (workWeek: WorkWeek, dayOffset: number, startDate: DateValue|undefined): void => {
    if (workWeek.shifts) {
        const index = workWeek.shifts.findIndex(shift => shift.week_day === weekDay(dayOffset, startDate));
        if (index > -1) {
            workWeek.shifts.splice(index, 1);
        }
    }
}

const updateShiftDates = (newStart: DateValue|undefined, oldStart: DateValue|undefined, schedule: Schedule): void => {
    if (!newStart || !oldStart) {
        return;
    }

    if (schedule.work_weeks) {
        schedule.work_weeks.forEach(workWeek => {
            workWeek.shifts?.forEach(shift => {
                const shiftDate = newStart.add({ days: shift.day_offset });
                const weekDay = weekDaysLowerCase[getDayOfWeek(shiftDate, 'us')];

                shift.date = shiftDate.toString();
                shift.week_day = weekDay;
            });
        });
    }
}

const weekDay = (dayOffset: number, startDate: DateValue|undefined): string => {
    return startDate ? weekDaysLowerCase[getDayOfWeek(startDate.add({days: dayOffset}), 'us')] : weekDaysLowerCase[dayOffset];
}

const weekDayShort = (dayOffset: number, startDate: DateValue|undefined): string => {
    return startDate ? weekDaysShort[getDayOfWeek(startDate.add({days: dayOffset}), 'us')] : '';
}

const getColor = (index: number): string => {
    return colors[index % colors.length];
}

const headerDateString = (dayOffset: number, startDate: DateValue|undefined): string => {
    return startDate ? header_df.format(startDate.add({days: dayOffset}).toDate(getLocalTimeZone())) : '';
}

export function useSchedule() {
    return {
        weekDaysLowerCase,
        weekDaysShort,
        colors,
        removeWorkWeek,
        addWorkWeek,
        addShift,
        removeShift,
        weekDay,
        weekDayShort,
        updateShiftDates,
        getColor,
        headerDateString,
    };
}
