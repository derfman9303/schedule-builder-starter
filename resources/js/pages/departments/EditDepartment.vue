<template>
    <Head title="Edit Department" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
            <h1 class="text-3xl font-bold tracking-tight">Edit Department</h1>

            <!-- Loading State -->
            <div
                v-if="isLoading"
                class="flex justify-center items-center py-20">
                <LoaderCircle
                    :size="40"
                    class="animate-spin text-primary"
                />
            </div>

            <!-- Department Form Card -->
            <Card v-else class="max-w-lg">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <PenTool class="h-5 w-5" />
                        Department Information
                    </CardTitle>
                    <CardDescription>
                        Update the details for this department
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Department Name</Label>
                            <Input
                                id="name"
                                type="text"
                                required
                                autofocus
                                v-model="form.name"
                                placeholder="Sales"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                        <Button
                            type="submit"
                            class="w-fit bg-blue-500 hover:bg-blue-600 cursor-pointer"
                            :disabled="form.processing"
                        >
                            <LoaderCircle
                                v-if="form.processing"
                                class="h-4 w-4 animate-spin mr-2"
                            />
                            Update Department
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>

</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { LoaderCircle, PenTool } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import axios from 'axios';

interface Department {
    id: number;
    name: string;
    user_id: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    department: string; // ID from the route parameter
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
    {
        title: 'Edit Department',
        href: `/departments/edit/${props.department}`,
    },
];

const form = useForm({
    name: '',
});

const isLoading = ref(true);
const department = ref<Department | null>(null);

function loadDepartment(): void {
    isLoading.value = true;

    axios.get(`/api/departments/${props.department}`)
        .then((response) => {
            department.value = response.data;
            form.name = response.data.name;
        })
        .catch((error) => {
            console.error('Error loading department:', error);
            router.get('/departments'); // Redirect if department not found
        })
        .finally(() => {
            isLoading.value = false;
        });
}

function submit(): void {
    form.errors = {};
    form.processing = true;

    axios.put(`/api/departments/${props.department}`, {
        name: form.name,
    })
    .then(() => {
        form.processing = false;
        router.get('/departments'); // Use Inertia for navigation
    })
    .catch((error) => {
        const errors = error.response?.data?.errors || {};

        // Convert validation errors to strings for compatibility
        Object.keys(errors).forEach((key) => {
            const typedKey = key as keyof typeof form.errors; // Explicitly cast key to the correct type
            if (Array.isArray(errors[typedKey])) {
                form.errors[typedKey] = errors[typedKey].join(' ');
            } else {
                form.errors[typedKey] = errors[typedKey];
            }
        });
        form.processing = false;
    });
}

onMounted(() => {
    loadDepartment();
});
</script>