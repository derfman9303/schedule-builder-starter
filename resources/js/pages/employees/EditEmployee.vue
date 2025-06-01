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
                        :disabled="form.processing || loading"
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
                        :disabled="form.processing || loading"
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
                        :disabled="form.processing || loading"
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
                        :disabled="form.processing || loading"
                        placeholder="(123) 456-7890"
                    />
                    <InputError :message="form.errors.phone" />
                </div>
                <Button type="submit" class="mt-4 w-fit" :tabindex="4" :disabled="form.processing || loading">
                    <LoaderCircle v-if="form.processing || loading" class="h-4 w-4 animate-spin" />
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
        href: '/employees/edit/' + window.location.href.split('/').pop(),
    },
];

const employee = ref({} as Employee);
const loading = ref(true);

breadcrumbs[1].href = `/employees/edit/${employee.value.id}`;

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

    form.errors = {};
    form.errors.phone = '';
    form.processing = true;

    axios.post('/api/employees/' + employee.value.id, {
        first_name: form.first_name,
        last_name: form.last_name,
        email: form.email,
        phone: form.phone,
    })
    .then(() => {
        form.processing = false;
        window.location.href = '/employees';
    })
    .catch((error) => {
        console.error('Error updating employee:', error.response.data);
        const errors = error.response.data.errors || {};

        // Convert validation errors to strings for compatibility
        Object.keys(errors).forEach((key) => {
            const typedKey = key as keyof typeof form.errors;
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
    const employeeId = window.location.href.split('/').pop();

    axios.get(`/api/employees/${employeeId}`)
        .then((response) => {
            employee.value = response.data;

            form.first_name = employee.value.first_name;
            form.last_name = employee.value.last_name;
            form.email = employee.value.email;
            form.phone = employee.value.phone;
        })
        .catch((error) => {
            console.error('Error fetching employee data:', error);
        })
        .finally(() => {
            loading.value = false;
        });
});
</script>