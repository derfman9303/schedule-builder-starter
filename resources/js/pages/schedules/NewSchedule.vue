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
            <div class="m-auto w-max mt-4">
                <Popover>
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            :class="cn(
                            'w-[200px] justify-start text-left font-normal mr-4',
                            !startDate && 'text-muted-foreground',
                            )"
                        >
                            <CalendarIcon class="mr-2 h-4 w-4" />
                            {{ !!startDate ? df.format(startDate.toDate(getLocalTimeZone())) : "Start Date" }}
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                        <Calendar v-model="startDate" initial-focus />
                    </PopoverContent>
                </Popover>
                <Popover>
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            :class="cn(
                            'w-[200px] justify-start text-left font-normal',
                            !endDate && 'text-muted-foreground',
                            )"
                        >
                            <CalendarIcon class="mr-2 h-4 w-4" />
                            {{ !!endDate ? df.format(endDate.toDate(getLocalTimeZone())) : "End Date" }}
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                        <Calendar v-model="endDate" initial-focus />
                    </PopoverContent>
                </Popover>
                <Table class="border border-gray-200 w-max mt-4">
                    <TableHeader class="bg-gray-100">
                        <TableRow>
                            <TableHead class="text-left px-4 py-2 text-center"></TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ headerDateString(0) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ headerDateString(1) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ headerDateString(2) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ headerDateString(3) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ headerDateString(4) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ headerDateString(5) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ headerDateString(6) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center"></TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="text-left px-4 py-2 text-center">Employee</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ weekDayShort(0) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ weekDayShort(1) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ weekDayShort(2) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ weekDayShort(3) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ weekDayShort(4) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ weekDayShort(5) }}</TableHead>
                            <TableHead class="text-left px-4 py-2 text-center">{{ weekDayShort(6) }}</TableHead>
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
                                    v-if="getShift(work_week, 0)"
                                    :shift="getShift(work_week, 0)"
                                    :color="getColor(index)"
                                    @update-shift="(shift) => updateShift(work_week, 0, shift)"
                                    @remove-shift="() => removeShift(work_week, 0)"
                                />
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, 0, shift)"
                                />
                            </TableCell>
                            <TableCell class="p-0">
                                <EditShiftComponent
                                    v-if="getShift(work_week, 1)"
                                    :shift="getShift(work_week, 1)"
                                    :color="getColor(index)"
                                    @update-shift="(shift) => updateShift(work_week, 1, shift)"
                                    @remove-shift="() => removeShift(work_week, 1)"
                                />
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, 1, shift)"
                                />
                            </TableCell>
                            <TableCell class="p-0">
                                <EditShiftComponent
                                    v-if="getShift(work_week, 2)"
                                    :shift="getShift(work_week, 2)"
                                    :color="getColor(index)"
                                    @update-shift="(shift) => updateShift(work_week, 2, shift)"
                                    @remove-shift="() => removeShift(work_week, 2)"
                                />
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, 2, shift)"
                                />
                            </TableCell>
                            <TableCell class="p-0">
                                <EditShiftComponent
                                    v-if="getShift(work_week, 3)"
                                    :shift="getShift(work_week, 3)"
                                    :color="getColor(index)"
                                    @update-shift="(shift) => updateShift(work_week, 3, shift)"
                                    @remove-shift="() => removeShift(work_week, 3)"
                                />
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, 3, shift)"
                                />
                            </TableCell>
                            <TableCell class="p-0">
                                <EditShiftComponent
                                    v-if="getShift(work_week, 4)"
                                    :shift="getShift(work_week, 4)"
                                    :color="getColor(index)"
                                    @update-shift="(shift) => updateShift(work_week, 4, shift)"
                                    @remove-shift="() => removeShift(work_week, 4)"
                                />
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, 4, shift)"
                                />
                            </TableCell>
                            <TableCell class="p-0">
                                <EditShiftComponent
                                    v-if="getShift(work_week, 5)"
                                    :shift="getShift(work_week, 5)"
                                    :color="getColor(index)"
                                    @update-shift="(shift) => updateShift(work_week, 5, shift)"
                                    @remove-shift="() => removeShift(work_week, 5)"
                                />
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, 5, shift)"
                                />
                            </TableCell>
                            <TableCell class="p-0">
                                <EditShiftComponent
                                    v-if="getShift(work_week, 6)"
                                    :shift="getShift(work_week, 6)"
                                    :color="getColor(index)"
                                    @update-shift="(shift) => updateShift(work_week, 6, shift)"
                                    @remove-shift="() => removeShift(work_week, 6)"
                                />
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, 6, shift)"
                                />
                            </TableCell>
                            <TableCell class="border-2 p-0">
                                <Button
                                    class="text-red-500 cursor-pointer hover:underline"
                                    variant="link"
                                    @click="removeWorkWeek(work_week, schedule)"
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
                                                        @click="addWorkWeek(selectedEmployee, schedule, selectedEmployee, newEmployee)"
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
                                                        @click="addWorkWeek(newEmployee, schedule, selectedEmployee, newEmployee)"
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
                <Button class="my-4 bg-blue-500 hover:bg-blue-600 text-white" @click="saveSchedule">
                    Save Schedule
                </Button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { cn } from '../../lib/utils';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useSchedule } from '@/composables/useSchedule';
import { DateFormatter, getLocalTimeZone, today } from '@internationalized/date';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { PopoverClose } from 'reka-ui';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Calendar } from '@/components/ui/calendar';
import AddShiftComponent from './AddShiftComponent.vue';
import EditShiftComponent from './EditShiftComponent.vue';
import { Plus, X, LoaderCircle, CalendarIcon } from 'lucide-vue-next';
import axios from 'axios';
import { type Employee } from '@/types/Employee';
import { type Schedule } from '@/types/Schedule';

const {
    startDate,
    endDate,
    headerDateString,
    weekDayShort,
    getShift,
    addShift,
    updateShift,
    removeShift,
    updateShiftDates,
    getColor,
    addWorkWeek,
    removeWorkWeek,
} = useSchedule();

const isLoading = ref(false);

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

const newEmployee = ref<Employee>({ 
    full_name: '',
});

const selectedEmployee = ref<Employee | null>(null);

const selectedEmployees = computed(() => {
    return employees.value.filter(emp => !schedule.value.work_weeks?.some(ww => ww.employee_id === emp.id));
});

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

function loadData() {
    isLoading.value = true;

    startDate.value = today(getLocalTimeZone());
    endDate.value = startDate.value.copy().add({ days: 6 });

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

function saveSchedule() {
    isLoading.value = true;

    schedule.value.start_date = startDate.value?.toString() || '';
    schedule.value.end_date = endDate.value?.toString() || '';

    axios.post('/api/schedules', schedule.value)
        .then(response => {
            console.log('Schedule saved successfully:', response.data);
        })
        .catch(error => {
            console.error('Error saving schedule:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
}

watch(
    () => [ startDate.value, endDate.value ],
    ([ newStart, newEnd ], [ oldStart, oldEnd ]) => {
        if (newStart?.day && newStart?.day !== oldStart?.day) {
            endDate.value = newStart.copy().add({days: 6});
            updateShiftDates(newStart, oldStart, schedule.value);
        } else if (newEnd?.day && newEnd?.day !== oldEnd?.day) {
            startDate.value = newEnd.copy().subtract({days: 6});
            updateShiftDates(newStart, oldStart, schedule.value);
        }
    },
    { flush: 'post' }
);

onMounted(() => {
    loadData();
});
</script>