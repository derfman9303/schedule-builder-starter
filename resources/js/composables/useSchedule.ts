import { type Schedule } from '@/types/Schedule';
import { type WorkWeek } from '@/types/WorkWeek';
import { type Employee } from '@/types/Employee';

const removeWorkWeek = (workWeek: WorkWeek, schedule: Schedule): void => {
    const index = schedule.work_weeks?.indexOf(workWeek);

    schedule.work_weeks = schedule.work_weeks?.filter((ww, i) => {
        return i !== index;
    });
}

const addEmployee = (employee: Employee|null, schedule: Schedule, selectedEmployee: Employee|null, newEmployee: Employee): void => {
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

export function useSchedule() {
    return {
        removeWorkWeek,
        addEmployee
    };
}
