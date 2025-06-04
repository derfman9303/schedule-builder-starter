<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h2 class="text-2xl font-bold mb-6">Employees</h2>
        <div class="overflow-x-auto">
            <Table class="min-w-full border border-gray-200">
                <TableHeader class="bg-gray-100">
                    <TableRow>
                        <TableHead class="text-left px-4 py-2">First Name</TableHead>
                        <TableHead class="text-left px-4 py-2">Last Name</TableHead>
                        <TableHead class="text-left px-4 py-2">Email</TableHead>
                        <TableHead class="text-left px-4 py-2">Phone</TableHead>
                        <TableHead class="text-left px-4 py-2">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="employee in employees" :key="employee.id" class="hover:bg-gray-50">
                        <TableCell class="border-t px-4 py-2">{{ employee.first_name }}</TableCell>
                        <TableCell class="border-t px-4 py-2">{{ employee.last_name }}</TableCell>
                        <TableCell class="border-t px-4 py-2">{{ employee.email }}</TableCell>
                        <TableCell class="border-t px-4 py-2">{{ employee.phone }}</TableCell>
                        <TableCell class="border-t px-4 py-2">
                            <Button
                                class="text-blue-500 hover:underline"
                                @click="editEmployee(employee.id)"
                                variant="link"
                                size="sm"
                            >
                                Edit
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { type Employee } from '@/types/Employee';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: '/employees',
    },
];

const employees = ref<Employee[]>([]);

onMounted(() => {
    axios.get('/api/employees')
        .then((response) => {
            employees.value = response.data;
        })
        .catch((error) => {
            console.error('Error fetching employees:', error);
        });
});

function editEmployee(employeeId: number) {
    window.location.href = `/employees/edit/${employeeId}`;
}
</script>