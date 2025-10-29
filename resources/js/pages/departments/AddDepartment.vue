<template>
    <Head title="Departments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <h1 class="text-3xl font-bold tracking-tight">New Department</h1>
            <form @submit.prevent="submit" class="flex flex-col gap-6 max-w-lg">
                <input
                    type="text"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none"
                />
            </form>
        </div>
    </AppLayout>

</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },

    {
        title: 'New Department',
        href: '/departments/new',
    },
];

const isLoading = ref(false);

function submit(): void {

}
</script>