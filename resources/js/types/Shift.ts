import { Department } from './Department';

export interface Shift {
    id?: number;
    work_week_id?: number;
    start_time?: string;
    end_time?: string;
    week_day?: string;
    date?: string;
    day_offset?: number;
    department_id?: number | null;
    department?: Department;
    created_at?: string;
    updated_at?: string;
}