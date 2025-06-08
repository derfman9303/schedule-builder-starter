<template>
    <Head title="Create a Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h2 class="text-2xl font-bold px-5">Create a Schedule</h2>
        <div class="overflow-x-auto px-5">
            <Table class="border border-gray-200 w-max">
                <TableHeader class="bg-gray-100">
                    <TableRow>
                        <TableHead class="text-left px-4 py-2">Employee</TableHead>
                        <TableHead class="text-left px-4 py-2">Mon.</TableHead>
                        <TableHead class="text-left px-4 py-2">Tue.</TableHead>
                        <TableHead class="text-left px-4 py-2">Wed.</TableHead>
                        <TableHead class="text-left px-4 py-2">Thu.</TableHead>
                        <TableHead class="text-left px-4 py-2">Fri.</TableHead>
                        <TableHead class="text-left px-4 py-2">Sat.</TableHead>
                        <TableHead class="text-left px-4 py-2">Sun.</TableHead>
                        <TableHead class="text-left px-4 py-2">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="employee in selectedEmployees" :key="employee.id" class="hover:bg-gray-50">
                        <TableCell class="border-t px-4 py-2">{{ employee.first_name }} {{ employee.last_name }}</TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px]">
                                <div>
                                    <input type="text" placeholder="Start" class="w-full" />
                                </div>
                                <div class="mt-2">
                                    <input type="text" placeholder="End" class="w-full" />
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px]">
                                <div>
                                    <input type="text" placeholder="Start" class="w-full" />
                                </div>
                                <div class="mt-2">
                                    <input type="text" placeholder="End" class="w-full" />
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px]">
                                <div>
                                    <input type="text" placeholder="Start" class="w-full" />
                                </div>
                                <div class="mt-2">
                                    <input type="text" placeholder="End" class="w-full" />
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px]">
                                <div>
                                    <input type="text" placeholder="Start" class="w-full" />
                                </div>
                                <div class="mt-2">
                                    <input type="text" placeholder="End" class="w-full" />
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px]">
                                <div>
                                    <input type="text" placeholder="Start" class="w-full" />
                                </div>
                                <div class="mt-2">
                                    <input type="text" placeholder="End" class="w-full" />
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px]">
                                <div>
                                    <input type="text" placeholder="Start" class="w-full" />
                                </div>
                                <div class="mt-2">
                                    <input type="text" placeholder="End" class="w-full" />
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">
                            <div class="border rounded p-2 w-[80px]">
                                <div>
                                    <input type="text" placeholder="Start" class="w-full" />
                                </div>
                                <div class="mt-2">
                                    <input type="text" placeholder="End" class="w-full" />
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="border-t p-0">action buttons</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <div class="mt-4">
                <Button
                    @click="addEmployee"
                    class="bg-blue-500 hover:bg-blue-600 text-white cursor-pointer"
                >
                    <Plus />
                    Add
                </Button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import axios from 'axios';
import { type Employee } from '@/types/Employee';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a Schedule',
        href: '/schedules/new',
    },
];

const employees = ref<Employee[]>([]);
const selectedEmployees = ref<Employee[]>([]);

function loadData() {
    axios.get('/api/employees')
        .then(response => {
            employees.value = response.data;
            selectedEmployees.value = response.data;
            console.log('Employees loaded:', employees.value);
        })
        .catch(error => {
            console.error('Error loading employees:', error);
        });
}

function addEmployee() {

}

onMounted(() => {
    loadData();
});
</script>