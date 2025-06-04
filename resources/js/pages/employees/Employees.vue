<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex justify-between items-center mb-6 px-4 pt-5">
            <h2 class="text-2xl font-bold">Employees</h2>
            <Button
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                @click="addEmployee"
            >
                Add New Employee
            </Button>
        </div>
        <div class="overflow-x-auto px-4">
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

function addEmployee() {
    window.location.href = '/employees/add';
}
</script>