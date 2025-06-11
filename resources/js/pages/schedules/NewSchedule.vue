<template>
    <Head title="Create a Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h2 class="text-2xl font-bold px-5">Create a Schedule</h2>
        <div class="overflow-x-auto px-5">
            <Table class="border border-gray-200 w-max m-auto">
                <TableHeader class="bg-gray-100">
                    <TableRow>
                        <TableHead class="text-left px-4 py-2">Employee</TableHead>
                        <TableHead class="text-left px-4 py-2">Mon.</TableHead>
                        <TableHead class="text-left px-4 py-2">Tue.</TableHead>
                        <TableHead class="text-left px-4 py-2">Wed.</TableHead>
                        <TableHead class="text-left px-4 py-2">Thu.</TableHead>
                        <TableHead class="text-left px-4 py-2">Fri.</TableHead>
                        <TableHead class="text-left px-4 py-2">Sat.</TableHead>
                        <TableHead class="text-left px-4 py-2">Sun.</TableHead>
                        <TableHead class="text-left px-4 py-2">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="work_week in schedule.work_weeks" :key="work_week.employee_id" class="hover:bg-gray-50 border-none">
                        <TableCell class="border px-4 py-2">{{ work_week.employee_name }}</TableCell>
                        <TableCell class="p-0">
                            <EditShiftComponent
                                v-if="getShift(work_week, 'monday')"
                                :shift="getShift(work_week, 'monday')"
                                @update-shift="(shift) => updateShift(work_week, 'monday', shift)"
                                @remove-shift="() => removeShift(work_week, 'monday')"
                            />
                            <AddShiftComponent
                                v-else
                                @add-shift="(shift) => addShift(work_week, 'monday', shift)"
                            />
                        </TableCell>
                        <TableCell class="p-0">
                            <EditShiftComponent
                                v-if="getShift(work_week, 'tuesday')"
                                :shift="getShift(work_week, 'tuesday')"
                                @update-shift="(shift) => updateShift(work_week, 'tuesday', shift)"
                                @remove-shift="() => removeShift(work_week, 'tuesday')"
                            />
                            <AddShiftComponent
                                v-else
                                @add-shift="(shift) => addShift(work_week, 'tuesday', shift)"
                            />
                        </TableCell>
                        <TableCell class="p-0">
                            <EditShiftComponent
                                v-if="getShift(work_week, 'wednesday')"
                                :shift="getShift(work_week, 'wednesday')"
                                @update-shift="(shift) => updateShift(work_week, 'wednesday', shift)"
                                @remove-shift="() => removeShift(work_week, 'wednesday')"
                            />
                            <AddShiftComponent
                                v-else
                                @add-shift="(shift) => addShift(work_week, 'wednesday', shift)"
                            />
                        </TableCell>
                        <TableCell class="p-0">
                            <EditShiftComponent
                                v-if="getShift(work_week, 'thursday')"
                                :shift="getShift(work_week, 'thursday')"
                                @update-shift="(shift) => updateShift(work_week, 'thursday', shift)"
                                @remove-shift="() => removeShift(work_week, 'thursday')"
                            />
                            <AddShiftComponent
                                v-else
                                @add-shift="(shift) => addShift(work_week, 'thursday', shift)"
                            />
                        </TableCell>
                        <TableCell class="p-0">
                            <EditShiftComponent
                                v-if="getShift(work_week, 'friday')"
                                :shift="getShift(work_week, 'friday')"
                                @update-shift="(shift) => updateShift(work_week, 'friday', shift)"
                                @remove-shift="() => removeShift(work_week, 'friday')"
                            />
                            <AddShiftComponent
                                v-else
                                @add-shift="(shift) => addShift(work_week, 'friday', shift)"
                            />
                        </TableCell>
                        <TableCell class="p-0">
                            <EditShiftComponent
                                v-if="getShift(work_week, 'saturday')"
                                :shift="getShift(work_week, 'saturday')"
                                @update-shift="(shift) => updateShift(work_week, 'saturday', shift)"
                                @remove-shift="() => removeShift(work_week, 'saturday')"
                            />
                            <AddShiftComponent
                                v-else
                                @add-shift="(shift) => addShift(work_week, 'saturday', shift)"
                            />
                        </TableCell>
                        <TableCell class="p-0">
                            <EditShiftComponent
                                v-if="getShift(work_week, 'sunday')"
                                :shift="getShift(work_week, 'sunday')"
                                @update-shift="(shift) => updateShift(work_week, 'sunday', shift)"
                                @remove-shift="() => removeShift(work_week, 'sunday')"
                            />
                            <AddShiftComponent
                                v-else
                                @add-shift="(shift) => addShift(work_week, 'sunday', shift)"
                            />
                        </TableCell>
                        <TableCell class="border p-0">
                            <Button
                                class="text-red-500 cursor-pointer hover:underline"
                                variant="link"
                                @click="removeWorkWeek(work_week)"
                            >
                                Remove
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <div class="mt-4">
                <Button
                    @click="addEmployee"
                    class="bg-blue-500 hover:bg-blue-600 text-white cursor-pointer"
                >
                    <Plus />
                    Add
                </Button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import AddShiftComponent from './AddShiftComponent.vue';
import EditShiftComponent from './EditShiftComponent.vue';
import { Plus } from 'lucide-vue-next';
import axios from 'axios';
import { type Employee } from '@/types/Employee';
import { type Schedule } from '@/types/Schedule';
import { type WorkWeek } from '@/types/WorkWeek';
import { type Shift } from '@/types/Shift';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a Schedule',
        href: '/schedules/new',
    },
];

const employees = ref<Employee[]>([]);

const schedule = ref<Schedule>({
    name: '',
    start_date: '',
    end_date: '',
    work_weeks: [],
});

function loadData() {
    axios.get('/api/employees')
        .then(response => {
            employees.value = response.data;
            initSchedule();
        })
        .catch(error => {
            console.error('Error loading employees:', error);
        });
}

function initSchedule() {
    employees.value.forEach(employee => {
        if (!schedule.value.work_weeks) {
            schedule.value.work_weeks = [];
        }

        schedule.value.work_weeks.push({
            employee_id: employee.id,
            employee_name: employee.first_name + ' ' + employee.last_name,
            shifts: [],
        });
    });
}

function getShift(workWeek: WorkWeek, day: string) {
    return workWeek.shifts?.find(shift => shift.week_day === day);
}

function addEmployee() {

}

function removeWorkWeek(workWeek: WorkWeek) {
    const index = schedule.value.work_weeks?.indexOf(workWeek);
    if (!!index && index > -1) {
        schedule.value.work_weeks?.splice(index, 1);
    }
}

function addShift(workWeek: WorkWeek, day: string, shift: Shift) {
    if (!workWeek.shifts) {
        workWeek.shifts = [];
    }

    workWeek.shifts.push({
        week_day: day,
        start_time: shift.start_time,
        end_time: shift.end_time,
    });
}

function updateShift(workWeek: WorkWeek, day: string, shift: Shift) {
    const existingShift = getShift(workWeek, day);
    if (existingShift) {
        existingShift.start_time = shift.start_time;
        existingShift.end_time = shift.end_time;
    }
}

function removeShift(workWeek: WorkWeek, day: string) {
    if (workWeek.shifts) {
        const index = workWeek.shifts.findIndex(shift => shift.week_day === day);
        if (index > -1) {
            workWeek.shifts.splice(index, 1);
        }
    }
}

onMounted(() => {
    loadData();
});
</script>