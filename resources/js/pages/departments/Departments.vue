<template>
    <Head title="Departments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Page Header -->
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Departments</h1>
                        <p class="text-muted-foreground">
                            Manage and organize your work departments
                        </p>
                    </div>
                    <Button asChild class="bg-blue-500 hover:bg-blue-600">
                        <Link href="/departments/new">
                            <Plus class="h-4 w-4 mr-2" />
                            New Department
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

            <!-- Departments Content -->
            <div v-else>
                <!-- Empty State -->
                <Card v-if="departments.length === 0">
                    <CardContent class="pt-6">
                        <div class="text-center py-12">
                            <Store class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                            <h3 class="text-lg font-semibold mb-2">No departments found</h3>
                            <p class="text-muted-foreground mb-4">
                                Get started by creating your first department
                            </p>
                            <Button asChild class="bg-blue-500 hover:bg-blue-600">
                                <Link href="/departments/new">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Create Your First Department
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Departments Table -->
                <Card v-else>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Building2 class="h-5 w-5" />
                            Your Departments
                        </CardTitle>
                        <CardDescription>
                            View and manage all your departments
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Department Name</TableHead>
                                        <TableHead>Created</TableHead>
                                        <TableHead class="text-center">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="department in paginatedDepartments"
                                        :key="department.id"
                                        class="hover:bg-muted/50"
                                    >
                                        <TableCell class="font-medium">
                                            {{ department.name }}
                                        </TableCell>
                                        <TableCell class="text-muted-foreground">
                                            {{ formatDate(department.created_at) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button
                                                    asChild
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    <Link :href="`/departments/edit/${department.id}`">
                                                        <Edit class="h-4 w-4 mr-1" />
                                                        Edit
                                                    </Link>
                                                </Button>
                                                <Button
                                                    @click="department.id && openDialog(department.id, department.name)"
                                                    variant="outline"
                                                    size="sm"
                                                    class="text-destructive hover:text-destructive cursor-pointer"
                                                    :disabled="!department.id"
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

                        <!-- Pagination -->
                        <div
                            v-if="departments.length"
                            class="flex justify-between items-center mt-4"
                        >
                            <Button
                                variant="outline"
                                :disabled="currentPage === 1"
                                @click="currentPage--"
                                class="cursor-pointer"
                            >
                                Previous
                            </Button>
                            <span class="text-sm text-muted-foreground">
                                Page {{ currentPage }} of {{ totalPages }}
                            </span>
                            <Button
                                variant="outline"
                                :disabled="currentPage === totalPages"
                                @click="currentPage++"
                                class="cursor-pointer"
                            >
                                Next
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Delete Dialog -->
                <AlertDialog :open="dialogOpen" @update:open="dialogOpen = $event">
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Delete Department</AlertDialogTitle>
                            <AlertDialogDescription>
                                Are you sure you want to delete "{{ departmentToDelete.name }}"? This action cannot be undone.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel class="cursor-pointer">Cancel</AlertDialogCancel>
                            <AlertDialogAction
                                @click="deleteDepartment"
                                class="bg-destructive text-destructive-foreground text-white cursor-pointer hover:bg-destructive/90"
                            >
                                Delete
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Department {
    id: number;
    name: string;
    user_id: number;
    created_at: string;
    updated_at: string;
}
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { AlertDialog, AlertDialogContent, AlertDialogHeader, AlertDialogFooter, AlertDialogCancel, AlertDialogAction, AlertDialogTitle, AlertDialogDescription } from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Plus, Store, LoaderCircle, Building2, Edit, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
];

const departments = ref<Department[]>([]);
const isLoading = ref(false);
const currentPage = ref(1);
const itemsPerPage = ref(10);
const dialogOpen = ref(false);
const departmentToDelete = ref<{ id: number | null, name: string }>({ id: null, name: '' });

// Computed properties
const totalPages = computed(() => Math.ceil(departments.value.length / itemsPerPage.value));
const paginatedDepartments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return departments.value.slice(start, end);
});

function fetchData() {
    isLoading.value = true;

    axios.get('/api/departments')
        .then((response) => {
            departments.value = response.data;
        })
        .catch((error) => {
            console.error('Error fetching departments:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
}

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function openDialog(id: number, name: string): void {
    departmentToDelete.value = { id, name };
    dialogOpen.value = true;
}

function deleteDepartment() {
    if (!departmentToDelete.value.id) return;

    axios.delete(`/api/departments/${departmentToDelete.value.id}`)
        .then(() => {
            // Remove the department from the local array
            departments.value = departments.value.filter(d => d.id !== departmentToDelete.value.id);
            
            // Adjust current page if necessary
            if (paginatedDepartments.value.length === 0 && currentPage.value > 1) {
                currentPage.value--;
            }
            
            dialogOpen.value = false;
            departmentToDelete.value = { id: null, name: '' };
        })
        .catch((error) => {
            console.error('Error deleting department:', error);
            // You could add a toast notification here for error handling
        });
}

onMounted(() => {
    fetchData();
});
</script>