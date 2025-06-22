<template>
        <Head title="Create a Schedule" />

        <AppLayout :breadcrumbs="breadcrumbs">
            <h1>schedules list</h1>
        </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Schedules',
        href: '/schedules',
    },
];
</script>