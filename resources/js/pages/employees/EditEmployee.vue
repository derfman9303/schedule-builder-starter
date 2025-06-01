<template>
    <Head title="Edit Employee" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="save" class="flex flex-col gap-6 max-w-lg">
            <div class="grid gap-6 p-4">
                <div class="grid gap-2">
                    <Label for="first_name">First Name</Label>
                    <Input
                        id="first_name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        v-model="form.first_name"
                        placeholder="John"
                    />
                    <InputError :message="form.errors.first_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="last_name">Last Name</Label>
                    <Input
                        id="last_name"
                        type="text"
                        required
                        :tabindex="2"
                        v-model="form.last_name"
                        placeholder="Doe"
                    />
                    <InputError :message="form.errors.last_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>
                <div class="grid gap-2">
                    <Label for="phone">Phone Number</Label>
                    <Input
                        id="phone"
                        type="text"
                        :tabindex="3"
                        v-model="form.phone"
                        placeholder="(123) 456-7890"
                    />
                    <InputError :message="form.errors.phone" />
                </div>
                <Button type="submit" class="mt-4 w-fit" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Save
                </Button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { watch, onMounted, ref } from 'vue';
import axios from 'axios';
import { Employee } from '@/types/Employee';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: '/employees',
    },
    {
        title: 'Edit Employee',
        href: '/employees/edit',
    },
];

const employee = ref({} as Employee); // Initialize employee with empty object

breadcrumbs[1].href = `/employees/edit`; // Set breadcrumb dynamically

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
});

watch(() => form.phone, (newValue) => {
    const cleanedPhone = newValue.replace(/\D/g, ''); // Remove non-digit characters

    if (cleanedPhone.length > 10) {
        form.errors.phone = 'Phone number must be exactly 10 digits.';
        return;
    }

    form.errors.phone = '';

    if (cleanedPhone.length > 0) {
        form.phone = `(${cleanedPhone.slice(0, 3)}) ${cleanedPhone.slice(3, 6)}-${cleanedPhone.slice(6, 10)}`.trim();
    }
});

function save() {
    const cleanedPhone = form.phone.replace(/\D/g, ''); // Remove non-digit characters

    if (cleanedPhone.length !== 10) {
        form.errors.phone = 'Phone number must be exactly 10 digits.';
        return;
    }

    // Clear errors and reset processing state
    form.errors = {}; // Clear all API validation errors
    form.errors.phone = '';
    form.processing = true;

    axios.post('/api/employees/' + employee.value.id, {
        first_name: form.first_name,
        last_name: form.last_name,
        email: form.email,
        phone: form.phone,
    })
    .then(() => {
        console.log('Employee updated successfully');
        form.processing = false;
    })
    .catch((error) => {
        console.error('Error updating employee:', error.response.data);
        const errors = error.response.data.errors || {};

        // Convert validation errors to strings for compatibility
        Object.keys(errors).forEach((key) => {
            const typedKey = key as keyof typeof form.errors;
            if (Array.isArray(errors[typedKey])) {
                form.errors[typedKey] = errors[typedKey].join(' '); // Join array elements into a single string
            } else {
                form.errors[typedKey] = errors[typedKey];
            }
        });
        form.processing = false;
    });
}

onMounted(() => {
    const employeeId = 9; // Retrieve employee ID from route params

    axios.get(`/api/employees/${employeeId}`)
        .then((response) => {
            employee.value = response.data;
            form.first_name = employee.value.first_name;
            form.last_name = employee.value.last_name;
            form.email = employee.value.email;
            form.phone = employee.value.phone;
            form.id = employee.value.id; // Set the employee ID from the returned data
        })
        .catch((error) => {
            console.error('Error fetching employee data:', error);
        });
});
</script>