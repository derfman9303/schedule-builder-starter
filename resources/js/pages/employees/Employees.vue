<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h2>Employees</h2>
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-left">First Name</TableHead>
                    <TableHead class="text-left">Last Name</TableHead>
                    <TableHead class="text-left">Email</TableHead>
                    <TableHead class="text-left">Phone</TableHead>
                    <TableHead class="text-left">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="employee in employees" :key="employee.id">
                    <TableCell>{{ employee.first_name }}</TableCell>
                    <TableCell>{{ employee.last_name }}</TableCell>
                    <TableCell>{{ employee.email }}</TableCell>
                    <TableCell>{{ employee.phone }}</TableCell>
                    <TableCell>
                        <Button
                            class="text-blue-500 cursor-pointer"
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