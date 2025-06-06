<template>
    <Head title="Create a Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-5 py-5">
            <h2 class="mb-4 text-2xl font-bold">Create a Schedule</h2>

            <div class="mb-4 max-w-xs">
                <Label for="week_start">Week Starting</Label>
                <Input id="week_start" type="date" v-model="weekStart" />
            </div>

            <div class="mb-4 flex items-end gap-2 max-w-md">
                <div class="flex-1">
                    <Label for="employee_select">Add Employee</Label>
                    <select
                        id="employee_select"
                        v-model="selectedEmployeeId"
                        class="mt-1 block w-full rounded border-gray-300"
                    >
                        <option value="" disabled>Select employee</option>
                        <option
                            v-for="emp in availableEmployees"
                            :key="emp.id"
                            :value="emp.id"
                        >
                            {{ emp.first_name }} {{ emp.last_name }}
                        </option>
                    </select>
                </div>
                <Button @click="addEmployee" :disabled="!selectedEmployeeId" class="bg-blue-500 hover:bg-blue-600">
                    Add
                </Button>
            </div>

            <div v-if="isLoading" class="flex h-64 items-center justify-center">
                <LoaderCircle class="h-8 w-8 animate-spin text-blue-500" />
            </div>

            <div v-else>
                <Table class="min-w-full border border-gray-200">
                    <TableHeader class="bg-gray-100">
                        <TableRow>
                            <TableHead class="px-4 py-2 text-left">Employee</TableHead>
                            <template v-for="day in weekDays" :key="day">
                                <TableHead class="px-2 py-2 text-left">{{ formatDay(day) }} Start</TableHead>
                                <TableHead class="px-2 py-2 text-left">{{ formatDay(day) }} End</TableHead>
                            </template>
                            <TableHead class="px-2 py-2"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="emp in selectedEmployees" :key="emp.id" class="hover:bg-gray-50">
                            <TableCell class="border-t px-4 py-2">{{ emp.first_name }} {{ emp.last_name }}</TableCell>
                            <template v-for="day in weekDays" :key="emp.id + '-' + day">
                                <TableCell class="border-t px-2 py-2">
                                    <Input type="time" class="w-28" v-model="schedules[emp.id][day].start_time" />
                                </TableCell>
                                <TableCell class="border-t px-2 py-2">
                                    <Input type="time" class="w-28" v-model="schedules[emp.id][day].end_time" />
                                </TableCell>
                            </template>
                            <TableCell class="border-t px-2 py-2">
                                <Button variant="link" class="text-red-500" @click="removeEmployee(emp.id)">
                                    Remove
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <Button class="mt-4 bg-blue-500 hover:bg-blue-600" :disabled="saving" @click="saveSchedule">
                    <LoaderCircle v-if="saving" class="mr-2 h-4 w-4 animate-spin" />
                    Save Schedule
                </Button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import type { Employee } from '@/types/Employee';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { LoaderCircle } from 'lucide-vue-next';
import { onMounted, ref, computed, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a Schedule',
        href: '/schedules/new',
    },
];

const employees = ref<Employee[]>([]);
const selectedEmployees = ref<Employee[]>([]);
const selectedEmployeeId = ref<number | ''>('');
const schedules = ref<Record<number, Record<string, { start_time: string; end_time: string }>>>({});
const weekStart = ref('');
const isLoading = ref(true);
const saving = ref(false);

const weekDays = computed(() => {
    if (!weekStart.value) {
        return [] as string[];
    }
    const start = new Date(weekStart.value);
    return Array.from({ length: 7 }, (_, i) => {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        return d.toISOString().split('T')[0];
    });
});

const availableEmployees = computed(() => {
    return employees.value.filter((e) => !selectedEmployees.value.some((se) => se.id === e.id));
});

const fetchEmployees = () => {
    isLoading.value = true;
    axios
        .get('/api/employees')
        .then((response) => {
            employees.value = response.data;
        })
        .catch((error) => {
            console.error('Error fetching employees:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
};

const addEmployee = () => {
    if (!selectedEmployeeId.value) return;
    const emp = employees.value.find((e) => e.id === Number(selectedEmployeeId.value));
    if (emp && !selectedEmployees.value.some((e) => e.id === emp.id)) {
        selectedEmployees.value.push(emp);
        schedules.value[emp.id] = {};
        weekDays.value.forEach((day) => {
            schedules.value[emp.id][day] = { start_time: '', end_time: '' };
        });
    }
    selectedEmployeeId.value = '';
};

const removeEmployee = (id: number) => {
    selectedEmployees.value = selectedEmployees.value.filter((e) => e.id !== id);
    delete schedules.value[id];
};

watch(weekDays, (newDays) => {
    selectedEmployees.value.forEach((emp) => {
        const existing = schedules.value[emp.id] || {};
        const updated: Record<string, { start_time: string; end_time: string }> = {};
        newDays.forEach((d) => {
            updated[d] = existing[d] || { start_time: '', end_time: '' };
        });
        schedules.value[emp.id] = updated;
    });
});

const formatDay = (dateStr: string) => {
    const date = new Date(dateStr + 'T00:00:00');
    return date.toLocaleDateString(undefined, { weekday: 'short' });
};

const saveSchedule = () => {
    if (!weekStart.value) {
        return;
    }
    saving.value = true;
    const entries: Array<{ employee_id: number; date: string; start_time: string; end_time: string }> = [];
    selectedEmployees.value.forEach((emp) => {
        weekDays.value.forEach((day) => {
            const times = schedules.value[emp.id][day];
            entries.push({
                employee_id: emp.id,
                date: day,
                start_time: times.start_time,
                end_time: times.end_time,
            });
        });
    });

    const payload = {
        week_start: weekStart.value,
        entries,
    };
    axios
        .post('/api/schedules', payload)
        .catch((error) => {
            console.error('Error saving schedule:', error);
        })
        .finally(() => {
            saving.value = false;
        });
};

onMounted(fetchEmployees);
</script>
