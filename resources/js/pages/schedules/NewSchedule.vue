<template>
    <Head title="Create a Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            v-if="isLoading"
            class="flex justify-center items-center h-64">
            <LoaderCircle
                :size="40"
                class="animate-spin text-blue-500"
            />
        </div>
        <div
            v-else
            class="overflow-x-auto m-auto w-max mt-4"
        >
            <div class="flex items-center">
                <Popover>
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            :class="cn(
                            'w-[200px] justify-start text-left font-normal',
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
                            'w-[200px] justify-start text-left font-normal mx-4',
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
                <Input
                    v-model="schedule.name"
                    class="w-full"
                    placeholder="Schedule Name (Optional)"
                />
            </div>
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
                        <TableCell v-for="day_offset in [0,1,2,3,4,5,6]" class="p-0">
                            <div
                                @dragover="handleDragOver"
                                @dragleave="handleDragLeave"
                                @drop="(event) => handleDrop(event, index, day_offset)"
                                class="min-h-[45px] flex items-center justify-center transition-colors"
                            >
                                <div
                                    v-if="getShift(work_week, day_offset)"
                                    draggable="true"
                                    @dragstart="(event) => handleDragStart(event, index, day_offset)"
                                    class="cursor-move"
                                >
                                    <EditShiftComponent
                                        :shift="getShift(work_week, day_offset)"
                                        :color="getColor(index)"
                                        @update-shift="(shift) => updateShift(work_week, day_offset, shift)"
                                        @remove-shift="() => removeShift(work_week, day_offset)"
                                    />
                                </div>
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, day_offset, shift)"
                                />
                            </div>
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
            <Button
                class="my-4 bg-blue-500 hover:bg-blue-600 text-white"
                :loading="buttonLoading"
                @click="saveSchedule"
            >
                <Loader2
                    v-if="buttonLoading"
                    class="w-4 h-4 mr-2 animate-spin"
                />
                Save Schedule
            </Button>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { cn } from '../../lib/utils';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
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
import { Plus, X, LoaderCircle, CalendarIcon, Loader2 } from 'lucide-vue-next';
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
    moveShift,
} = useSchedule();

const isLoading = ref(false);
const buttonLoading = ref(false);

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
    // Add visual feedback
    const target = event.currentTarget as HTMLElement;
    target.classList.add('bg-blue-100', 'outline-2', 'outline-blue-300', 'outline-dashed');
};

const handleDragLeave = (event: DragEvent) => {
    // Remove visual feedback
    const target = event.currentTarget as HTMLElement;
    target.classList.remove('bg-blue-100', 'outline-2', 'outline-blue-300', 'outline-dashed');
};

const handleDrop = (event: DragEvent, targetWorkWeekIndex: number, targetDayOffset: number) => {
    event.preventDefault();
    
    // Remove visual feedback
    const target = event.currentTarget as HTMLElement;
    target.classList.remove('bg-blue-100', 'outline-2', 'outline-blue-300', 'outline-dashed');
    
    if (!event.dataTransfer) return;
    
    try {
        const data = JSON.parse(event.dataTransfer.getData('text/plain'));
        const { sourceWorkWeekIndex, sourceDayOffset } = data;
        
        // Don't do anything if dropping on the same cell
        if (sourceWorkWeekIndex === targetWorkWeekIndex && sourceDayOffset === targetDayOffset) {
            return;
        }
        
        // Check if target cell is occupied
        const targetWorkWeek = schedule.value.work_weeks?.[targetWorkWeekIndex];
        const targetShift = targetWorkWeek ? getShift(targetWorkWeek, targetDayOffset) : null;
        
        if (targetShift) {
            // Target cell is occupied, can't drop here
            return;
        }
        
        // Perform the move
        const sourceWorkWeek = schedule.value.work_weeks?.[sourceWorkWeekIndex];
        if (sourceWorkWeek && targetWorkWeek) {
            moveShift(sourceWorkWeek, sourceDayOffset, targetWorkWeek, targetDayOffset);
        }
    } catch (error) {
        console.error('Error handling drop:', error);
    }
};

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
    buttonLoading.value = true;

    schedule.value.start_date = startDate.value?.toString() || '';
    schedule.value.end_date = endDate.value?.toString() || '';

    axios.post('/api/schedules', schedule.value)
        .then(res => {
            router.visit('/schedules');
        })
        .catch(error => {
            console.error('Error saving schedule:', error);
        })
        .finally(() => {
            buttonLoading.value = false;
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