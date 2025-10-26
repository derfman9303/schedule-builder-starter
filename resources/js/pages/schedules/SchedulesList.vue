<template>
    <Head title="Schedules" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Page Header -->
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Schedules</h1>
                        <p class="text-muted-foreground">
                            Manage and organize your work schedules
                        </p>
                    </div>
                    <Button asChild>
                        <Link href="/schedules/new">
                            <Plus class="h-4 w-4 mr-2" />
                            New Schedule
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Loading State -->
            <div
                v-if="isLoading"
                class="flex justify-center items-center py-20">
                <LoaderCircle
                    :size="40"
                    class="animate-spin text-primary"
                />
            </div>

            <!-- Schedules Content -->
            <div v-else>
                <!-- Empty State -->
                <Card v-if="schedules.length === 0">
                    <CardContent class="pt-6">
                        <div class="text-center py-12">
                            <Calendar class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                            <h3 class="text-lg font-semibold mb-2">No schedules found</h3>
                            <p class="text-muted-foreground mb-4">
                                Get started by creating your first schedule
                            </p>
                            <Button asChild>
                                <Link href="/schedules/new">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Create Your First Schedule
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Schedules Table -->
                <Card v-else>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            Your Schedules
                        </CardTitle>
                        <CardDescription>
                            View and manage all your created schedules
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Schedule Name</TableHead>
                                        <TableHead>Date Range</TableHead>
                                        <TableHead>Employees</TableHead>
                                        <TableHead class="text-center">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="schedule in schedules"
                                        :key="schedule.id"
                                        class="hover:bg-muted/50"
                                    >
                                        <TableCell class="font-medium">
                                            {{ schedule.name || 'Weekly Schedule' }}
                                        </TableCell>
                                        <TableCell class="text-muted-foreground">
                                            {{ formatDate(schedule.start_date) }} - {{ formatDate(schedule.end_date) }}
                                        </TableCell>
                                        <TableCell class="text-muted-foreground">
                                            {{ schedule.work_weeks?.length || 0 }} assigned
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button
                                                    asChild
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    <Link :href="`/schedules/edit/${schedule.id}`">
                                                        Edit
                                                    </Link>
                                                </Button>
                                                <Button
                                                    @click="exportSchedule(schedule)"
                                                    variant="outline"
                                                    size="sm"
                                                    class="cursor-pointer"
                                                >
                                                    <Download class="h-4 w-4 mr-1" />
                                                    Export
                                                </Button>
                                                <Button
                                                    @click="openDialog(schedule)"
                                                    variant="outline"
                                                    size="sm"
                                                    class="text-destructive hover:text-destructive cursor-pointer"
                                                >
                                                    <Trash2 class="h-4 w-4 mr-1" />
                                                    Delete
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="dialogOpen">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Confirm Deletion</AlertDialogTitle>
                    <AlertDialogDescription>
                        <span v-html="deleteMessage"></span>
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel class="cursor-pointer">
                        Cancel
                    </AlertDialogCancel>
                    <AlertDialogAction
                        @click="deleteSchedule"
                        class="bg-destructive text-white hover:bg-destructive/90 cursor-pointer"
                    >
                        Delete Schedule
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
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { AlertDialog, AlertDialogContent, AlertDialogHeader, AlertDialogFooter, AlertDialogCancel, AlertDialogAction, AlertDialogTitle, AlertDialogDescription } from '@/components/ui/alert-dialog';
import { type BreadcrumbItem } from '@/types'
import { type Schedule } from '@/types/Schedule';
import { Plus, LoaderCircle, Calendar, FileText, Download, Trash2 } from 'lucide-vue-next';
import { useSchedule } from '@/composables/useSchedule';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Schedules', href: '/schedules' },
]

const { formatDate } = useSchedule();

const schedules = ref<Schedule[]>([]);
const isLoading = ref<boolean>(true);
const dialogOpen = ref<boolean>(false);
const deleteMessage = ref<string>('');
const deleteScheduleId = ref<number | undefined>(undefined);

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
    deleteMessage.value = `
        <div class="space-y-2 text-black">
            <p>Are you sure you want to delete this schedule? This action cannot be undone.</p>
            <div class="bg-muted/50 p-3 rounded-md space-y-1">
                <p><strong>Schedule:</strong> ${schedule.name || 'Weekly Schedule'}</p>
                <p><strong>Date Range:</strong> ${formatDate(schedule.start_date)} - ${formatDate(schedule.end_date)}</p>
                <p><strong>Employees:</strong> ${schedule.work_weeks?.length || 0} assigned</p>
            </div>
        </div>
    `;
    dialogOpen.value = true;
};



onMounted(() => {
    fetchData();
});
</script>