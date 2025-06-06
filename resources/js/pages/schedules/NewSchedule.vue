<template>
    <Head title="Create a Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h2>Create a Schedule</h2>
        <Table class="min-w-full border border-gray-200">
            <TableHeader class="bg-gray-100">
                <TableRow>
                    <TableHead class="text-left px-4 py-2"></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="employee in selectedEmployees" :key="employee.id" class="hover:bg-gray-50">
                    <TableCell class="border-t px-4 py-2">{{ employee.first_name }}</TableCell>
                    <TableCell class="border-t px-4 py-2">{{ employee.last_name }}</TableCell>
                    <TableCell class="border-t px-4 py-2">{{ employee.email }}</TableCell>
                    <TableCell class="border-t px-4 py-2">{{ employee.phone }}</TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import axios from 'axios';
import { type Employee } from '@/types/Employee';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a Schedule',
        href: '/schedules/new',
    },
];

const employees = ref<Employee[]>([]);
const selectedEmployees = ref<Employee[]>([]);

function loadData() {
    axios.get('/api/employees')
        .then(response => {
            employees.value = response.data;
            console.log('Employees loaded:', employees.value);
        })
        .catch(error => {
            console.error('Error loading employees:', error);
        });
}

onMounted(() => {
    loadData();
});
</script>