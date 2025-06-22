import { type Schedule } from '@/types/Schedule';
import { type WorkWeek } from '@/types/WorkWeek';
import { type Employee } from '@/types/Employee';
import { type Shift } from '@/types/Shift';
import { type DateValue, getDayOfWeek } from '@internationalized/date';

const weekDaysLowerCase = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

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

const weekDay = (dayOffset: number, startDate: DateValue|undefined): string => {
    return startDate ? weekDaysLowerCase[getDayOfWeek(startDate.add({days: dayOffset}), 'us')] : weekDaysLowerCase[dayOffset];
}

export function useSchedule() {
    return {
        removeWorkWeek,
        addWorkWeek,
        addShift,
        weekDay,
        weekDaysLowerCase
    };
}
