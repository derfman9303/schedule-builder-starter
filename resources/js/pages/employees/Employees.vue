<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex justify-between items-center mb-6 px-5 pt-5">
            <h2 class="text-2xl font-bold">Employees</h2>
            <Link
                href="/employees/add"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2"
            >
                <Plus :size="18"/>
                New Employee
            </Link>
        </div>
        <div v-if="isLoading" class="flex justify-center items-center h-64">
            <LoaderCircle
                :size="40"
                class="animate-spin text-blue-500"
            />
        </div>
        <div v-else>
            <div class="overflow-x-auto px-5">
                <Table class="min-w-full border border-gray-200">
                    <TableHeader class="bg-gray-100">
                        <TableRow>
                            <TableHead class="text-left px-4 py-2">First Name</TableHead>
                            <TableHead class="text-left px-4 py-2">Last Name</TableHead>
                            <TableHead class="text-left px-4 py-2">Email</TableHead>
                            <TableHead class="text-left px-4 py-2">Phone</TableHead>
                            <TableHead class="text-left px-4 py-2">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                        v-if="employees.length"
                            v-for="employee in paginatedEmployees"
                            :key="employee.id"
                            class="hover:bg-gray-50"
                        >
                            <TableCell class="border-t px-4 py-2">{{ employee.first_name }}</TableCell>
                            <TableCell class="border-t px-4 py-2">{{ employee.last_name }}</TableCell>
                            <TableCell class="border-t px-4 py-2">{{ employee.email }}</TableCell>
                            <TableCell class="border-t px-4 py-2">{{ employee.phone }}</TableCell>
                            <TableCell class="border-t px-4 py-2">
                                <Link
                                    :href="`/employees/edit/${employee.id}`"
                                    class="text-blue-500 hover:text-blue-700 hover:underline"
                                >
                                    Edit
                                </Link>
                                <Button
                                    @click="openDialog(employee.id, employee.first_name, employee.last_name)"
                                    variant="link"
                                    class="text-red-500 cursor-pointer hover:underline"
                                >
                                    Delete
                                </Button>
                            </TableCell>
                        </TableRow>
                            <TableRow v-else>
                            <TableCell colspan="5" class="text-center text-gray-500 py-4">
                                No employees found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <div
                v-if="employees.length"
                class="flex justify-between items-center mt-4 mb-4 px-5"
            >
                <Button
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 cursor-pointer"
                    :disabled="currentPage === 1"
                    @click="currentPage--"
                >
                    Previous
                </Button>
                <span>Page {{ currentPage }} of {{ totalPages }}</span>
                <Button
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 cursor-pointer"
                    :disabled="currentPage === totalPages"
                    @click="currentPage++"
                >
                    Next
                </Button>
            </div>
        </div>
        <AlertDialog v-model:open="dialogOpen">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle class="text-lg font-bold">Confirm Deletion</AlertDialogTitle>
                    <AlertDialogDescription>{{ deleteMessage }}</AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        @click="dialogOpen = false"
                        class="cursor-pointer"
                    >
                        Cancel
                    </AlertDialogCancel>
                    <AlertDialogAction
                        @click="deleteEmployee(selectedEmployeeId)"
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
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { type Employee } from '@/types/Employee';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { Plus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
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
    deleteMessage.value = `Are you sure you want to delete ${firstName} ${lastName}?`;
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