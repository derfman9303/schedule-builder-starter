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
                    <TableRow v-for="work_week in schedule.work_weeks" :key="work_week.employee_id" class="hover:bg-gray-50">
                        <TableCell class="border-t px-4 py-2">{{ work_week.employee_name }}</TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                                <Plus class="text-gray-400 m-auto" />
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                                <Plus class="text-gray-400 m-auto" />
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                                <Plus class="text-gray-400 m-auto" />
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                                <Plus class="text-gray-400 m-auto" />
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                                <Plus class="text-gray-400 m-auto" />
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                                <Plus class="text-gray-400 m-auto" />
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                                <Plus class="text-gray-400 m-auto" />
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
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
import { Plus } from 'lucide-vue-next';
import axios from 'axios';
import { type Employee } from '@/types/Employee';
import { type Schedule } from '@/types/Schedule';
import { type WorkWeek } from '@/types/WorkWeek';

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
            shifts: [
                
            ],
        });
    });
}

function addEmployee() {

}

function removeWorkWeek(workWeek: WorkWeek) {
    const index = schedule.value.work_weeks?.indexOf(workWeek);
    if (!!index && index > -1) {
        schedule.value.work_weeks?.splice(index, 1);
    }
}

onMounted(() => {
    loadData();
});
</script>