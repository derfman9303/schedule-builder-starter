<template>
    <Head title="Create a Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Page Header -->
            <div class="flex flex-col space-y-2">
                <h1 class="text-3xl font-bold tracking-tight">Create New Schedule</h1>
                <p class="text-muted-foreground">
                    Build a weekly schedule by assigning shifts to your employees
                </p>
            </div>

            <!-- Loading State -->
            <div
                v-if="isLoading"
                class="flex justify-center items-center py-20">
                <LoaderCircle
                    :size="40"
                    class="animate-spin text-primary"
                />
            </div>

            <!-- Schedule Configuration -->
            <div v-else class="space-y-6">
                <!-- Schedule Settings Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <CalendarLucide class="h-5 w-5" />
                            Schedule Configuration
                        </CardTitle>
                        <CardDescription>
                            Set the date range and name for your schedule
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Start Date</label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn(
                                                'w-full justify-start text-left font-normal cursor-pointer',
                                                !startDate && 'text-muted-foreground',
                                            )"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ !!startDate ? df.format(startDate.toDate(getLocalTimeZone())) : "Select start date" }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="startDate" initial-focus />
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">End Date</label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn(
                                                'w-full justify-start text-left font-normal cursor-pointer',
                                                !endDate && 'text-muted-foreground',
                                            )"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ !!endDate ? df.format(endDate.toDate(getLocalTimeZone())) : "Select end date" }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="endDate" initial-focus />
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Schedule Name (Optional)</label>
                                <Input
                                    v-model="schedule.name"
                                    placeholder="Enter schedule name"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Schedule Grid Card -->
                <Card class="overflow-x-auto">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Employee Schedule Grid
                        </CardTitle>
                        <CardDescription>
                            Assign shifts to employees by day. Drag and drop shifts to move them between days.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[120px]">Employee</TableHead>
                                    <TableHead class="text-center">{{ weekDayShort(0) }}<br><span class="text-xs text-muted-foreground">{{ headerDateString(0) }}</span></TableHead>
                                    <TableHead class="text-center">{{ weekDayShort(1) }}<br><span class="text-xs text-muted-foreground">{{ headerDateString(1) }}</span></TableHead>
                                    <TableHead class="text-center">{{ weekDayShort(2) }}<br><span class="text-xs text-muted-foreground">{{ headerDateString(2) }}</span></TableHead>
                                    <TableHead class="text-center">{{ weekDayShort(3) }}<br><span class="text-xs text-muted-foreground">{{ headerDateString(3) }}</span></TableHead>
                                    <TableHead class="text-center">{{ weekDayShort(4) }}<br><span class="text-xs text-muted-foreground">{{ headerDateString(4) }}</span></TableHead>
                                    <TableHead class="text-center">{{ weekDayShort(5) }}<br><span class="text-xs text-muted-foreground">{{ headerDateString(5) }}</span></TableHead>
                                    <TableHead class="text-center">{{ weekDayShort(6) }}<br><span class="text-xs text-muted-foreground">{{ headerDateString(6) }}</span></TableHead>
                                    <TableHead class="text-center w-[100px]">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="(work_week, index) in schedule.work_weeks"
                                    :key="work_week.employee_id"
                                    class="hover:bg-muted/50"
                                >
                                    <TableCell class="font-medium">{{ work_week.employee_name }}</TableCell>
                                    <TableCell v-for="day_offset in [0,1,2,3,4,5,6]" :key="day_offset" class="p-1">
                                        <div
                                            @dragover="handleDragOver"
                                            @dragleave="handleDragLeave"
                                            @drop="(event) => handleDrop(event, index, day_offset, schedule)"
                                            class="min-h-[45px] w-[125px] flex items-center justify-center transition-colors rounded-md border-2 border-dashed border-transparent hover:border-muted-foreground/25 m-auto"
                                        >
                                            <div
                                                v-if="getShift(work_week, day_offset)"
                                                draggable="true"
                                                @dragstart="(event) => handleDragStart(event, index, day_offset)"
                                                class="cursor-move w-full"
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
                                    <TableCell class="text-center">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="text-destructive hover:text-destructive cursor-pointer"
                                            @click="removeWorkWeek(work_week, schedule)"
                                        >
                                            Remove
                                        </Button>
                                    </TableCell>
                                </TableRow>
                                <!-- Add Employee Row -->
                                <TableRow>
                                    <TableCell>
                                        <Popover>
                                            <PopoverTrigger as-child>
                                                <Button
                                                    variant="outline"
                                                    class="w-full h-[45px] border-dashed cursor-pointer"
                                                >
                                                    <Plus class="h-4 w-4 mr-2" />
                                                    Add Employee
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="w-80">
                                                <div class="space-y-4">
                                                    <div class="space-y-2">
                                                        <h4 class="font-medium">Add Employee to Schedule</h4>
                                                        <p class="text-sm text-muted-foreground">
                                                            Select an existing employee or add a new one.
                                                        </p>
                                                    </div>
                                                    <Tabs default-value="existing">
                                                        <TabsList class="grid w-full grid-cols-2">
                                                            <TabsTrigger
                                                                value="existing"
                                                                class="cursor-pointer"
                                                            >
                                                                Existing
                                                            </TabsTrigger>
                                                            <TabsTrigger
                                                                value="new"
                                                                class="cursor-pointer"
                                                            >
                                                                New
                                                            </TabsTrigger>
                                                        </TabsList>
                                                        <TabsContent value="existing" class="space-y-2">
                                                            <Select
                                                                v-model="selectedEmployee"
                                                                :disabled="selectedEmployees.length === 0"
                                                            >
                                                                <SelectTrigger class="cursor-pointer">
                                                                    <SelectValue :placeholder="selectedEmployees.length === 0 ? 'No employees available' : 'Select an employee'" />
                                                                </SelectTrigger>
                                                                <SelectContent>
                                                                    <SelectItem
                                                                        v-for="employee in selectedEmployees"
                                                                        :key="employee.id"
                                                                        :value="employee"
                                                                        class="cursor-pointer"
                                                                    >
                                                                        {{ employee.first_name }} {{ employee.last_name }}
                                                                    </SelectItem>
                                                                </SelectContent>
                                                            </Select>
                                                            <PopoverClose as-child>
                                                                <Button
                                                                    @click="addWorkWeek(selectedEmployee, schedule, selectedEmployee, newEmployee)"
                                                                    :disabled="!selectedEmployee"
                                                                    class="w-full cursor-pointer bg-blue-500 hover:bg-blue-600"
                                                                >
                                                                    Add Employee
                                                                </Button>
                                                            </PopoverClose>
                                                        </TabsContent>
                                                        <TabsContent value="new" class="space-y-2">
                                                            <Input
                                                                v-model="newEmployee.full_name"
                                                                placeholder="Employee Name"
                                                            />
                                                            <PopoverClose as-child>
                                                                <Button
                                                                    @click="addWorkWeek(newEmployee, schedule, selectedEmployee, newEmployee)"
                                                                    :disabled="!newEmployee.full_name"
                                                                    class="w-full cursor-pointer bg-blue-500 hover:bg-blue-600"
                                                                >
                                                                    Add New Employee
                                                                </Button>
                                                            </PopoverClose>
                                                        </TabsContent>
                                                    </Tabs>
                                                </div>
                                            </PopoverContent>
                                        </Popover>
                                    </TableCell>
                                    <TableCell :colspan="8" class="bg-muted/30"></TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Save Actions -->
                <div class="flex justify-end space-x-4">
                    <Button
                        variant="outline"
                        @click="router.visit('/schedules')"
                        class="cursor-pointer"
                    >
                        Cancel
                    </Button>
                    <Button
                        @click="saveSchedule"
                        :disabled="buttonLoading"
                        class="min-w-[120px] cursor-pointer bg-blue-500 hover:bg-blue-600"
                    >
                        <Loader2
                            v-if="buttonLoading"
                            class="w-4 h-4 mr-2 animate-spin"
                        />
                        <Save
                            v-else
                            class="w-4 h-4 mr-2"
                        />
                        Save Schedule
                    </Button>
                </div>
            </div>
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
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { PopoverClose } from 'reka-ui';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Calendar } from '@/components/ui/calendar';
import AddShiftComponent from './AddShiftComponent.vue';
import EditShiftComponent from './EditShiftComponent.vue';
import { Plus, X, LoaderCircle, CalendarIcon, Loader2, Calendar as CalendarLucide, Users, Save } from 'lucide-vue-next';
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
    handleDragStart,
    handleDragOver,
    handleDragLeave,
    handleDrop,
} = useSchedule();

const isLoading = ref(false);
const buttonLoading = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Schedules',
        href: '/schedules',
    },
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