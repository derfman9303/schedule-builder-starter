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

                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Plus, Store, LoaderCircle } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
];

const departments = ref([]);
const isLoading = ref(false);

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

onMounted(() => {
    fetchData();
});
</script>