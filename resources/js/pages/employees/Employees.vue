<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h2>Employees</h2>
        <ul>
            <li v-for="employee in employees" :key="employee.id">
                {{ employee.first_name }}
            </li>
        </ul>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { type Employee } from '@/types/Employee';

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
</script>