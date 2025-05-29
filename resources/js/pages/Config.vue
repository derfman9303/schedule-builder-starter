<template>
    <Head title="Configuration" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h2>Configuration</h2>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Configuration',
        href: '/config',
    },
];
</script>