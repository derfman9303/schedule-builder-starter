import { ref } from 'vue';
import { type Schedule } from '@/types/Schedule';
import { type WorkWeek } from '@/types/WorkWeek';
import { type Employee } from '@/types/Employee';
import { type Shift } from '@/types/Shift';
import { type DateValue, getDayOfWeek, getLocalTimeZone, DateFormatter } from '@internationalized/date';

export function useSchedule() {
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
        'Sun',
        'Mon',
        'Tue',
        'Wed',
        'Thu',
        'Fri',
        'Sat'
    ];

    const colors = [
        'bg-sky-600',
        'bg-rose-700',
        'bg-violet-600',
        'bg-emerald-600',
        'bg-orange-600',
    ];

    const startDate = ref<DateValue>();
    const endDate = ref<DateValue>();

    const header_df = new DateFormatter('en-US', {
        month: 'numeric',
        day: 'numeric',
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

    const getShift = (workWeek: WorkWeek, dayOffset: number): Shift | undefined => {
        return workWeek.shifts?.find(shift => shift.week_day === weekDay(dayOffset));
    }

    const updateShift = (workWeek: WorkWeek, dayOffset: number, shift: Shift): void => {
        const existingShift = getShift(workWeek, dayOffset);

        if (existingShift) {
            existingShift.start_time = shift.start_time;
            existingShift.end_time = shift.end_time;
        }
    }

    const addShift = (workWeek: WorkWeek, dayOffset: number, shift: Shift): void => {
        const day = weekDay(dayOffset);

        if (!workWeek.shifts) {
            workWeek.shifts = [];
        }

        const newShift = {
            week_day: day,
            date: startDate.value?.add({ days: dayOffset }).toString() || '',
            day_offset: dayOffset,
            start_time: shift.start_time,
            end_time: shift.end_time,
        };

        workWeek.shifts.push(newShift);
    }

    const removeShift = (workWeek: WorkWeek, dayOffset: number): void => {
        if (workWeek.shifts) {
            const index = workWeek.shifts.findIndex(shift => shift.week_day === weekDay(dayOffset));
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

    const weekDay = (dayOffset: number): string => {
        return startDate.value ? weekDaysLowerCase[getDayOfWeek(startDate.value.add({days: dayOffset}), 'us')] : weekDaysLowerCase[dayOffset];
    }

    const weekDayShort = (dayOffset: number): string => {
        return startDate.value ? weekDaysShort[getDayOfWeek(startDate.value.add({days: dayOffset}), 'us')] : '';
    }

    const getColor = (index: number): string => {
        return colors[index % colors.length];
    }

    const headerDateString = (dayOffset: number): string => {
        return startDate.value ? header_df.format(startDate.value.add({days: dayOffset}).toDate(getLocalTimeZone())) : '';
    }

    const moveShift = (
        sourceWorkWeek: WorkWeek, 
        sourceDayOffset: number, 
        targetWorkWeek: WorkWeek, 
        targetDayOffset: number
    ): boolean => {
        const sourceShift = getShift(sourceWorkWeek, sourceDayOffset);
        
        if (!sourceShift) {
            return false;
        }

        // Check if target already has a shift
        const targetShift = getShift(targetWorkWeek, targetDayOffset);
        if (targetShift) {
            return false; // Can't move to occupied slot
        }

        // Remove from source
        removeShift(sourceWorkWeek, sourceDayOffset);

        // Add to target with updated day information
        const day = weekDay(targetDayOffset);
        const newShift = {
            ...sourceShift,
            week_day: day,
            date: startDate.value?.add({ days: targetDayOffset }).toString() || '',
            day_offset: targetDayOffset,
        };

        if (!targetWorkWeek.shifts) {
            targetWorkWeek.shifts = [];
        }

        targetWorkWeek.shifts.push(newShift);
        return true;
    }

    // Drag and drop handlers
    const handleDragStart = (event: DragEvent, workWeekIndex: number, dayOffset: number) => {
        if (event.dataTransfer) {
            event.dataTransfer.setData('text/plain', JSON.stringify({
                sourceWorkWeekIndex: workWeekIndex,
                sourceDayOffset: dayOffset
            }));
            event.dataTransfer.effectAllowed = 'move';
        }
    };

    const handleDragOver = (event: DragEvent) => {
        event.preventDefault();
        if (event.dataTransfer) {
            event.dataTransfer.dropEffect = 'move';
        }
        // Add visual feedback with dotted border only, no background color
        const target = event.currentTarget as HTMLElement;
        target.classList.remove('border-transparent');
        target.classList.add('border-blue-400', 'border-2', 'border-dashed');
    };

    const handleDragLeave = (event: DragEvent) => {
        // Remove visual feedback
        const target = event.currentTarget as HTMLElement;
        target.classList.remove('border-blue-400', 'border-2', 'border-dashed');
        target.classList.add('border-transparent');
    };

    const handleDrop = (event: DragEvent, targetWorkWeekIndex: number, targetDayOffset: number, schedule: Schedule) => {
        event.preventDefault();
        
        // Remove visual feedback
        const target = event.currentTarget as HTMLElement;
        target.classList.remove('border-blue-400', 'border-2', 'border-dashed');
        target.classList.add('border-transparent');
        
        if (!event.dataTransfer) return;
        
        try {
            const data = JSON.parse(event.dataTransfer.getData('text/plain'));
            const { sourceWorkWeekIndex, sourceDayOffset } = data;
            
            // Don't do anything if dropping on the same cell
            if (sourceWorkWeekIndex === targetWorkWeekIndex && sourceDayOffset === targetDayOffset) {
                return;
            }
            
            // Check if target cell is occupied
            const targetWorkWeek = schedule.work_weeks?.[targetWorkWeekIndex];
            const targetShift = targetWorkWeek ? getShift(targetWorkWeek, targetDayOffset) : null;
            
            if (targetShift) {
                // Target cell is occupied, can't drop here
                return;
            }
            
            // Perform the move
            const sourceWorkWeek = schedule.work_weeks?.[sourceWorkWeekIndex];
            if (sourceWorkWeek && targetWorkWeek) {
                moveShift(sourceWorkWeek, sourceDayOffset, targetWorkWeek, targetDayOffset);
            }
        } catch (error) {
            console.error('Error handling drop:', error);
        }
    };

    /**
     * Format date to a more readable format without timezone conversion issues
     */
    const formatDate = (dateString: string): string => {
        // Parse date components manually to avoid any timezone conversion
        const [year, month, day] = dateString.split('-').map(Number);
        const date = new Date(year, month - 1, day);
        
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    };

    return {
        weekDaysLowerCase,
        weekDaysShort,
        colors,
        startDate,
        endDate,
        removeWorkWeek,
        addWorkWeek,
        getShift,
        updateShift,
        addShift,
        removeShift,
        weekDay,
        weekDayShort,
        updateShiftDates,
        getColor,
        headerDateString,
        moveShift,
        handleDragStart,
        handleDragOver,
        handleDragLeave,
        handleDrop,
        formatDate,
    };
}
