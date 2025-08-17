<template>
    <Head title="Schedules" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            v-if="isLoading"
            class="flex justify-center items-center h-64">
            <LoaderCircle
                :size="40"
                class="animate-spin text-blue-500"
            />
        </div>
        <div
            v-else
            class="px-5 pt-5 overflow-x-auto w-max mx-auto"
        >
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Your Schedules</h1>
                <Link
                    href="/schedules/new"
                    class="inline-flex items-center px-4 py-2 text-white bg-blue-500 hover:bg-blue-600 rounded-lg"
                >
                    <Plus
                        :size="18"
                        class="mr-2"
                    />
                    New Schedule
                </Link>
            </div>
            <Table
                class="min-w-full border border-gray-200"
            >
                <TableHeader class="bg-gray-100">
                    <TableRow>
                        <TableHead class="text-center px-4 py-2 border-2">Date range</TableHead>
                        <TableHead class="text-center px-4 py-2 border-2">Name</TableHead>
                        <TableHead class="text-center px-4 py-2 border-2">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-if="schedules.length"
                        v-for="schedule in schedules"
                        :key="schedule.id"
                        class="hover:bg-gray-50"
                    >
                        <TableCell class="text-center text-gray-600 border-2 w-[200px]">{{ formatDate(schedule.start_date) }} &nbsp; - &nbsp; {{ formatDate(schedule.end_date) }}</TableCell>
                        <TableCell class="text-center text-gray-600 border-2 w-[400px]">{{ schedule.name ?? 'Weekly Schedule' }}</TableCell>
                        <TableCell class="text-center border-2 w-[200px]">
                            <Link
                                :href="`/schedules/edit/${schedule.id}`"
                                class="text-blue-500 hover:text-blue-700 hover:underline "
                            >
                                Edit
                            </Link>
                            <Button
                                @click="exportSchedule(schedule)"
                                variant="link"
                                class="text-green-500 cursor-pointer hover:underline ml-3"
                            >
                                Export
                            </Button>
                            <Button
                                @click="openDialog(schedule)"
                                variant="link"
                                class="text-red-500 cursor-pointer hover:underline ml-3"
                            >
                                Delete
                            </Button>
                        </TableCell>
                    </TableRow>
                    <TableRow v-else>
                        <TableCell colspan="4" class="text-center text-gray-500 py-4">
                            No schedules found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <AlertDialog v-model:open="dialogOpen">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle class="text-lg font-bold">Confirm Deletion</AlertDialogTitle>
                    <AlertDialogDescription>
                        <span v-html="deleteMessage">
                        </span>
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        @click="dialogOpen = false"
                        class="cursor-pointer"
                    >
                        Cancel
                    </AlertDialogCancel>
                    <AlertDialogAction
                        @click="deleteSchedule"
                        class="bg-red-500 hover:bg-red-600 cursor-pointer text-white"
                    >
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { AlertDialog, AlertDialogContent, AlertDialogHeader, AlertDialogFooter, AlertDialogCancel, AlertDialogAction, AlertDialogTitle, AlertDialogDescription } from '@/components/ui/alert-dialog';
import { type BreadcrumbItem } from '@/types'
import { type Schedule } from '@/types/Schedule';
import { Plus, LoaderCircle } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Schedules', href: '/schedules' },
]

const schedules = ref<Schedule[]>([]);

const isLoading = ref(true as boolean);

const dialogOpen = ref(false as boolean);
const deleteMessage = ref('' as string);
const deleteScheduleId = ref(undefined as number | undefined);

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

function exportSchedule(schedule: Schedule): void {
    const url = `/api/schedules/${schedule.id}/export`;
    window.open(url, '_blank');
}

function deleteSchedule(): void {
    isLoading.value = true;

    if (!deleteScheduleId.value) {
        console.error('No schedule ID to delete');
        dialogOpen.value = false;
        return;
    }

    axios.delete(`/api/schedules/${deleteScheduleId.value}`)
        .then(() => {
            // Remove the schedule from the list
            schedules.value = schedules.value.filter(s => s.id !== deleteScheduleId.value);
            dialogOpen.value = false;
            deleteScheduleId.value = undefined;
            deleteMessage.value = '';
        })
        .catch((error) => {
            console.error('Error deleting schedule:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
}

function openDialog(schedule: Schedule): void {
    deleteScheduleId.value = schedule.id;
    deleteMessage.value = 'Are you sure you want to delete this schedule?' +
        `<br><br><strong>Schedule Name: ${schedule.name ?? 'not set'}</strong>` +
        `<br><strong>Start Date: ${formatDate(schedule.start_date)}</strong>` +
        `<br><strong>End Date: ${formatDate(schedule.end_date)}</strong>`;

    dialogOpen.value = true;
};

/**
 * Simple date formatter: YYYY-MM-DD → M/D/YYYY
 */
function formatDate(d: string): string {
    const parts = d.split('-');
    return `${parseInt(parts[1])}/${parseInt(parts[2])}/${parts[0]}`;
}

onMounted(() => {
    fetchData();
});
</script>