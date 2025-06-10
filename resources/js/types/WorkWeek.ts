import type { Shift } from './Shift';

export interface WorkWeek {
    id?: number;
    employee_id?: number;
    employee_name?: string;
    start_date?: string;
    end_date?: string;
    shifts?: Shift[];
}
