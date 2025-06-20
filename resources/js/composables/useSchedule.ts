import { type Schedule } from '@/types/Schedule';
import { type WorkWeek } from '@/types/WorkWeek';

export function removeWorkWeek(workWeek: WorkWeek, schedule: Schedule): void {
    const index = schedule.work_weeks?.indexOf(workWeek);

    schedule.work_weeks = schedule.work_weeks?.filter((ww, i) => {
        return i !== index;
    });
}

export function useSchedule() {
    return { removeWorkWeek };
}
