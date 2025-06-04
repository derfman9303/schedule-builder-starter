<template>
    <Head title="Add Employee" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submit" class="flex flex-col gap-6 max-w-lg">
            <div class="grid gap-6 p-4">
                <div class="grid gap-2">
                    <Label for="first_name">First Name</Label>
                    <Input
                        id="first_name"
                        type="text"
                        required
                        autofocus
                        v-model="form.first_name"
                        placeholder="John"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.first_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="last_name">Last Name</Label>
                    <Input
                        id="last_name"
                        type="text"
                        required
                        v-model="form.last_name"
                        placeholder="Doe"
                        :disabled="form.processing"
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
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.email" />
                </div>
                <div class="grid gap-2">
                    <Label for="phone">Phone Number</Label>
                    <Input
                        id="phone"
                        type="text"
                        v-model="form.phone"
                        placeholder="(123) 456-7890"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.phone" />
                </div>
                <Button
                    type="submit"
                    class="w-fit"
                    :disabled="form.processing"
                >
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Add Employee
                </Button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { watch } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: '/employees',
    },
    {
        title: 'Add Employee',
        href: '/employees/add',
    },
];

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

function submit() {
    const cleanedPhone = form.phone.replace(/\D/g, ''); // Remove non-digit characters

    if (cleanedPhone.length !== 10) {
        form.errors.phone = 'Phone number must be exactly 10 digits.';
        return;
    }

    form.errors = {};
    form.errors.phone = '';
    form.processing = true;

    axios.post('/api/employees', {
        first_name: form.first_name,
        last_name: form.last_name,
        email: form.email,
        phone: form.phone,
    })
    .then(() => {
        form.processing = false;
        router.get('/employees'); // Use Inertia for navigation
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