<template>
    <Head title="Add Department" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
            <h1 class="text-3xl font-bold tracking-tight">Add New Department</h1>

            <!-- Department Form Card -->
            <Card class="max-w-lg">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Building2 class="h-5 w-5" />
                        Department Information
                    </CardTitle>
                    <CardDescription>
                        Enter the details for your new department
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
                            Save Department
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>

</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { LoaderCircle, Building2 } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
    {
        title: 'Add Department',
        href: '/departments/add',
    },
];

const form = useForm({
    name: '',
});

function submit() {
    form.errors = {};
    form.processing = true;

    axios.post('/api/departments', {
        name: form.name,
    })
    .then(() => {
        form.processing = false;
        router.get('/departments'); // Use Inertia for navigation
    })
    .catch((error) => {
        const errors = error.response.data.errors || {};

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
</script>