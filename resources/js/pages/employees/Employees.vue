<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Page Header -->
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Employees</h1>
                        <p class="text-muted-foreground">
                            Manage your team members and their information
                        </p>
                    </div>
                    <Button asChild>
                        <Link href="/employees/add">
                            <Plus class="h-4 w-4 mr-2" />
                            New Employee
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

            <!-- Employees Content -->
            <div v-else>
                <!-- Empty State -->
                <Card v-if="employees.length === 0">
                    <CardContent class="pt-6">
                        <div class="text-center py-12">
                            <Users class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                            <h3 class="text-lg font-semibold mb-2">No employees found</h3>
                            <p class="text-muted-foreground mb-4">
                                Get started by adding your first team member
                            </p>
                            <Button asChild>
                                <Link href="/employees/add">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Your First Employee
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Employees Table -->
                <Card v-else>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Your Team
                        </CardTitle>
                        <CardDescription>
                            View and manage all your employees
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Phone</TableHead>
                                        <TableHead class="text-center">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="employee in paginatedEmployees"
                                        :key="employee.id"
                                        class="hover:bg-muted/50"
                                    >
                                        <TableCell class="font-medium">
                                            {{ employee.first_name }} {{ employee.last_name }}
                                        </TableCell>
                                        <TableCell class="text-muted-foreground">
                                            {{ employee.email }}
                                        </TableCell>
                                        <TableCell class="text-muted-foreground">
                                            {{ employee.phone }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button
                                                    asChild
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    <Link :href="`/employees/edit/${employee.id}`">
                                                        <Edit class="h-4 w-4 mr-1" />
                                                        Edit
                                                    </Link>
                                                </Button>
                                                <Button
                                                    @click="employee.id && openDialog(employee.id, employee.first_name || '', employee.last_name || '')"
                                                    variant="outline"
                                                    size="sm"
                                                    class="text-destructive hover:text-destructive cursor-pointer"
                                                    :disabled="!employee.id"
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
                            v-if="employees.length"
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
                            <span class="text-muted-foreground">Page {{ currentPage }} of {{ totalPages }}</span>
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
                        @click="deleteEmployee(selectedEmployeeId)"
                        class="bg-destructive text-white hover:bg-destructive/90 cursor-pointer"
                    >
                        Delete Employee
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { type Employee } from '@/types/Employee';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Plus, LoaderCircle, Users, Edit, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { AlertDialog, AlertDialogContent, AlertDialogHeader, AlertDialogFooter, AlertDialogCancel, AlertDialogAction, AlertDialogTitle, AlertDialogDescription } from '@/components/ui/alert-dialog';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: '/employees',
    },
];

const employees = ref<Employee[]>([]);
const currentPage = ref(1);
const itemsPerPage = 10;
const isLoading = ref(true);
const dialogOpen = ref(false);
const selectedEmployeeId = ref<number | null>(null);
const deleteMessage = ref('');

const paginatedEmployees = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return employees.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(employees.value.length / itemsPerPage);
});

function fetchEmployees() {
    isLoading.value = true;
    axios.get('/api/employees')
        .then((response) => {
            employees.value = response.data;
        })
        .catch((error) => {
            console.error('Error fetching employees:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
};

const openDialog = (employeeId: number, firstName: string, lastName: string) => {
    selectedEmployeeId.value = employeeId;
    deleteMessage.value = `
        <div class="space-y-2 text-black">
            <p>Are you sure you want to delete this employee? This action cannot be undone.</p>
            <div class="bg-muted/50 p-3 rounded-md space-y-1">
                <p><strong>Employee:</strong> ${lastName}, ${firstName}</p>
            </div>
        </div>
    `;
    dialogOpen.value = true;
};

const deleteEmployee = (employeeId: number|null) => {
    if (!!employeeId) {
        axios.delete(`/api/employees/${employeeId}`)
            .then(() => {
                employees.value = employees.value.filter(employee => employee.id !== employeeId);
                dialogOpen.value = false;
                selectedEmployeeId.value = null;
                deleteMessage.value = '';
            })
            .catch((error) => {
                console.error('Error deleting employee:', error);
            });
    } else {
        console.error('No employee ID provided for deletion.');
    }
};

onMounted(() => {
    fetchEmployees();
});
</script>