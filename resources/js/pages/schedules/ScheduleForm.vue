<template>
    <Head :title="config.pageTitle" />

    <AppLayout :breadcrumbs="config.breadcrumbs">
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Page Header -->
            <div class="flex flex-col space-y-2">
                <h1 class="text-3xl font-bold tracking-tight">{{ config.heading }}</h1>
                <p class="text-muted-foreground">
                    {{ config.description }}
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
                            {{ config.cardDescription }}
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
                <ScheduleGrid
                    :schedule="schedule"
                    :employees="employees"
                    :grid-description="config.gridDescription"
                />

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
                        @click="handleSubmit"
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
                        {{ config.submitButtonText }}
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { cn } from '../../lib/utils';
import { parseDate } from '@internationalized/date';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { useSchedule } from '@/composables/useSchedule';
import { useDepartments } from '@/composables/useDepartments';
import { DateFormatter, getLocalTimeZone, today } from '@internationalized/date';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Calendar } from '@/components/ui/calendar';
import ScheduleGrid from './ScheduleGrid.vue';
import { LoaderCircle, CalendarIcon, Loader2, Calendar as CalendarLucide, Save } from 'lucide-vue-next';
import axios from 'axios';
import { type Employee } from '@/types/Employee';
import { type Schedule } from '@/types/Schedule';

interface Props {
    mode: 'create' | 'edit';
    scheduleId?: number;
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const isLoading = ref(false);
const buttonLoading = ref(false);

const {
    startDate,
    endDate,
    updateShiftDates,
} = useSchedule();

const { loadDepartments } = useDepartments();

const config = computed(() => ({
    create: {
        pageTitle: 'Create a Schedule',
        heading: 'Create New Schedule',
        description: 'Build a weekly schedule by assigning shifts to your employees',
        cardDescription: 'Set the date range and name for your schedule',
        gridDescription: 'Assign shifts to employees by day. Drag and drop shifts to move them between days.',
        submitButtonText: 'Save Schedule',
        breadcrumbs: [
            { title: 'Schedules', href: '/schedules' },
            { title: 'Create a Schedule', href: '/schedules/new' },
        ] as BreadcrumbItem[],
    },
    edit: {
        pageTitle: 'Edit Schedule',
        heading: 'Edit Schedule',
        description: 'Modify your schedule by updating shifts and employee assignments',
        cardDescription: 'Update the date range and name for your schedule',
        gridDescription: 'Update shifts for employees by day. Drag and drop shifts to move them between days.',
        submitButtonText: 'Update Schedule',
        breadcrumbs: [
            { title: 'Schedules', href: '/schedules' },
            { title: 'Edit Schedule', href: `/schedules/edit/${props.scheduleId}` },
        ] as BreadcrumbItem[],
    },
})[props.mode]);

const employees = ref<Employee[]>([]);

const schedule = ref<Schedule>({
    name: '',
    start_date: '',
    end_date: '',
    work_weeks: [],
});

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

async function loadData() {
    isLoading.value = true;

    loadDepartments();

    if (props.mode === 'create') {
        // New schedule: set dates to today + 6
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
    } else {
        // Edit mode: load schedule + employees
        const employeeRequest = axios.get('/api/employees')
            .then(response => {
                employees.value = response.data;
            })
            .catch(error => {
                console.error('Error loading employees:', error);
            });

        const scheduleRequest = axios.get(`/api/schedules/${props.scheduleId}`)
            .then(response => {
                schedule.value = response.data;
                startDate.value = parseDate(schedule.value.start_date);
                endDate.value = parseDate(schedule.value.end_date);
            })
            .catch(error => {
                console.error('Error loading schedule:', error);
            });

        await Promise.all([employeeRequest, scheduleRequest])
            .then(() => {
                isLoading.value = false;
            });
    }
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

function handleSubmit() {
    buttonLoading.value = true;

    schedule.value.start_date = startDate.value?.toString() || '';
    schedule.value.end_date = endDate.value?.toString() || '';

    const request = props.mode === 'create'
        ? axios.post('/api/schedules', schedule.value)
        : axios.put(`/api/schedules/${props.scheduleId}`, schedule.value);

    request
        .then(res => {
            router.visit('/schedules');
        })
        .catch(error => {
            console.error(`Error ${props.mode === 'create' ? 'saving' : 'updating'} schedule:`, error);
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
