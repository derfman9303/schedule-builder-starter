<template>
    <Head title="Create a Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            v-if="isLoading"
            class="flex justify-center items-center h-64">
            <LoaderCircle class="h-8 w-8 animate-spin text-blue-500" />
        </div>
        <div
            v-else
            class="overflow-x-auto"
        >
            <Table class="border border-gray-200 w-max m-auto">
                <TableHeader class="bg-gray-100">
                    <TableRow>
                        <TableHead class="text-left px-4 py-2 text-center">Employee</TableHead>
                        <TableHead class="text-left px-4 py-2 text-center">Mon.</TableHead>
                        <TableHead class="text-left px-4 py-2 text-center">Tue.</TableHead>
                        <TableHead class="text-left px-4 py-2 text-center">Wed.</TableHead>
                        <TableHead class="text-left px-4 py-2 text-center">Thu.</TableHead>
                        <TableHead class="text-left px-4 py-2 text-center">Fri.</TableHead>
                        <TableHead class="text-left px-4 py-2 text-center">Sat.</TableHead>
                        <TableHead class="text-left px-4 py-2 text-center">Sun.</TableHead>
                        <TableHead class="text-left px-4 py-2 text-center">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="(work_week, index) in schedule.work_weeks"
                        :key="work_week.employee_id"
                        class="hover:bg-gray-50 border-none"
                    >
                        <TableCell class="border-2 px-4 py-2">{{ work_week.employee_name }}</TableCell>
                        <TableCell class="p-0">
                            <EditShiftComponent
                                v-if="getShift(work_week, 'monday')"
                                :shift="getShift(work_week, 'monday')"
                                :color="colors[index % colors.length]"
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
                                :color="colors[index % colors.length]"
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
                                :color="colors[index % colors.length]"
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
                                :color="colors[index % colors.length]"
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
                                :color="colors[index % colors.length]"
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
                                :color="colors[index % colors.length]"
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
                                :color="colors[index % colors.length]"
                                @update-shift="(shift) => updateShift(work_week, 'sunday', shift)"
                                @remove-shift="() => removeShift(work_week, 'sunday')"
                            />
                            <AddShiftComponent
                                v-else
                                @add-shift="(shift) => addShift(work_week, 'sunday', shift)"
                            />
                        </TableCell>
                        <TableCell class="border-2 p-0">
                            <Button
                                class="text-red-500 cursor-pointer hover:underline"
                                variant="link"
                                @click="removeWorkWeek(work_week)"
                            >
                                Remove
                            </Button>
                        </TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="border-2 p-0">
                            <Popover>
                                <PopoverTrigger class="w-full cursor-pointer h-[45px]">
                                    <div class="flex items-center justify-center text-sm text-blue-500">
                                        <Plus :size="16" />
                                        Add
                                    </div>
                                </PopoverTrigger>
                                <PopoverContent>
                                    <div class="flex flex-col items-end mr-[5px] mb-2">
                                        <PopoverClose>
                                            <X :size="20" class="text-gray-400 m-auto cursor-pointer" />
                                        </PopoverClose>
                                    </div>
                                    <Tabs default-value="existing">
                                        <TabsList>
                                            <TabsTrigger value="existing">Existing Employee</TabsTrigger>
                                            <TabsTrigger value="new">New Employee</TabsTrigger>
                                        </TabsList>
                                        <TabsContent
                                            class="mt-2"
                                            value="existing"
                                        >
                                            <Select
                                                class="w-full"
                                                v-model="selectedEmployee"
                                                :disabled="selectedEmployees.length === 0"
                                            >
                                                <SelectTrigger class="w-full">
                                                    <SelectValue :placeholder="selectedEmployees.length === 0 ? 'No employees available' : 'Select an employee'" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem
                                                        v-for="employee in selectedEmployees"
                                                        :key="employee.id"
                                                        :value="employee"
                                                    >
                                                        {{ employee.first_name }} {{ employee.last_name }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <PopoverClose>
                                                <Button
                                                    @click="addEmployee(selectedEmployee)"
                                                    :disabled="!selectedEmployee"
                                                    class="mt-4 bg-blue-500 hover:bg-blue-600 text-white cursor-pointer"
                                                >
                                                    Add
                                                </Button>
                                            </PopoverClose>
                                        </TabsContent>
                                        <TabsContent
                                            class="mt-2"
                                            value="new"
                                        >
                                            <Input
                                                v-model="newEmployee.full_name"
                                                placeholder="Employee Name"
                                            />
                                            <PopoverClose>
                                                <Button
                                                    @click="addEmployee(newEmployee)"
                                                    :disabled="!newEmployee.full_name"
                                                    class="mt-4 bg-blue-500 hover:bg-blue-600 text-white cursor-pointer"
                                                >
                                                    Add
                                                </Button>
                                            </PopoverClose>
                                        </TabsContent>
                                    </Tabs>
                                </PopoverContent>
                            </Popover>
                        </TableCell>
                        <TableCell class="border-2 bg-gray-100" :colspan="8"></TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { PopoverClose } from 'reka-ui';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AddShiftComponent from './AddShiftComponent.vue';
import EditShiftComponent from './EditShiftComponent.vue';
import { Plus, X, LoaderCircle } from 'lucide-vue-next';
import axios from 'axios';
import { type Employee } from '@/types/Employee';
import { type Schedule } from '@/types/Schedule';
import { type WorkWeek } from '@/types/WorkWeek';
import { type Shift } from '@/types/Shift';

const isLoading = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a Schedule',
        href: '/schedules/new',
    },
];

const colors = [
    'bg-sky-600',
    'bg-rose-700',
    'bg-violet-600',
    'bg-emerald-600',
    'bg-orange-600',
];

const employees = ref<Employee[]>([]);

const schedule = ref<Schedule>({
    name: '',
    start_date: '',
    end_date: '',
    work_weeks: [],
});

const newEmployee = ref<Employee>({ 
    full_name: '',
});

const selectedEmployee = ref<Employee | null>(null);

const selectedEmployees = computed(() => {
    return employees.value.filter(emp => !schedule.value.work_weeks?.some(ww => ww.employee_id === emp.id));
});

function loadData() {
    isLoading.value = true;

    axios.get('/api/employees')
        .then(response => {
            employees.value = response.data;
            initSchedule();
        })
        .catch(error => {
            console.error('Error loading employees:', error);
        })
        .finally(() => {
            isLoading.value = false;
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

function addEmployee(employee: Employee | null) {
    if (employee && schedule.value.work_weeks) {
        schedule.value.work_weeks.push({
            employee_id: employee.id,
            employee_name: employee.full_name || (employee.first_name + ' ' + employee.last_name),
            shifts: [],
        });
        selectedEmployee.value = null;
    }
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