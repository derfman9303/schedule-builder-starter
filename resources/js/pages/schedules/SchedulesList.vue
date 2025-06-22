<template>
  <Head title="Schedules" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Your Schedules</h1>
                <Link
                    href="/schedules/create"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    + New Schedule
                </Link>
            </div>

            <Table class="min-w-full border border-gray-200">
                <TableHeader class="bg-gray-100">
                    <TableRow>
                        <TableHead class="text-left px-4 py-2">Name</TableHead>
                        <TableHead class="text-left px-4 py-2">Start Date</TableHead>
                        <TableHead class="text-left px-4 py-2">End Date</TableHead>
                        <TableHead class="text-center px-4 py-2">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="schedule in schedules" :key="schedule.id" class="hover:bg-gray-50">
                        <TableCell>{{ schedule.name }}</TableCell>
                        <TableCell>{{ formatDate(schedule.start_date) }}</TableCell>
                        <TableCell>{{ formatDate(schedule.end_date) }}</TableCell>
                        <TableCell class="text-center">
                            <Link
                                :href="`/schedules/${schedule.id}`"
                                class="text-gray-600 hover:text-gray-800 mr-2"
                            >
                                View
                            </Link>
                            <Link
                                :href="`/schedules/${schedule.id}/edit`"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                Edit
                            </Link>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { type BreadcrumbItem } from '@/types'
import { type Schedule } from '@/types/Schedule';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Schedules', href: '/schedules' },
]

const schedules = ref<Schedule[]>([]);

const isLoading = ref(true);

function fetchData() {
    isLoading.value = true;

    axios.get('/api/schedules')
        .then((response) => {
            schedules.value = response.data;
        })
        .catch((error) => {
            console.error('Error fetching schedules:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
}

/**
 * Simple date formatter: YYYY-MM-DD → M/D/YYYY
 */
function formatDate(d: string) {
    const dt = new Date(d)
    return `${dt.getMonth() + 1}/${dt.getDate()}/${dt.getFullYear()}`
}

onMounted(() => {
    fetchData();
});
</script>