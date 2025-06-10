import type { WorkWeek } from './WorkWeek';

export interface Schedule {
    id?: number;
    name: string;
    start_date: string;
    end_date: string;
    work_weeks?: WorkWeek[];
}
