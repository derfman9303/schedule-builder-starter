<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h2>Employees</h2>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: '/employees',
    },
];
</script>