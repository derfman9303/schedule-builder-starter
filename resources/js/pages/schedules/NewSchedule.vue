<template>
    <Head title="Create a Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-5 py-5">
            <h2 class="mb-4 text-2xl font-bold">Create a Schedule</h2>

            <div class="mb-4 max-w-xs">
                <Label for="schedule_date">Date</Label>
                <Input id="schedule_date" type="date" v-model="scheduleDate" />
            </div>

            <div v-if="isLoading" class="flex h-64 items-center justify-center">
                <LoaderCircle class="h-8 w-8 animate-spin text-blue-500" />
            </div>

            <div v-else>
                <Table class="min-w-full border border-gray-200">
                    <TableHeader class="bg-gray-100">
                        <TableRow>
                            <TableHead class="px-4 py-2 text-left">Employee</TableHead>
                            <TableHead class="px-4 py-2 text-left">Start Time</TableHead>
                            <TableHead class="px-4 py-2 text-left">End Time</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="emp in employees" :key="emp.id" class="hover:bg-gray-50">
                            <TableCell class="border-t px-4 py-2">{{ emp.first_name }} {{ emp.last_name }}</TableCell>
                            <TableCell class="border-t px-4 py-2">
                                <Input type="time" class="w-32" v-model="schedules[emp.id].start_time" />
                            </TableCell>
                            <TableCell class="border-t px-4 py-2">
                                <Input type="time" class="w-32" v-model="schedules[emp.id].end_time" />
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
import { onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a Schedule',
        href: '/schedules/new',
    },
];

const employees = ref<Employee[]>([]);
const schedules = ref<Record<number, { start_time: string; end_time: string }>>({});
const scheduleDate = ref('');
const isLoading = ref(true);
const saving = ref(false);

const fetchEmployees = () => {
    isLoading.value = true;
    axios
        .get('/api/employees')
        .then((response) => {
            employees.value = response.data;
            employees.value.forEach((emp) => {
                schedules.value[emp.id] = { start_time: '', end_time: '' };
            });
        })
        .catch((error) => {
            console.error('Error fetching employees:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
};

const saveSchedule = () => {
    saving.value = true;
    const payload = {
        date: scheduleDate.value,
        entries: Object.entries(schedules.value).map(([id, times]) => ({
            employee_id: Number(id),
            start_time: times.start_time,
            end_time: times.end_time,
        })),
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
