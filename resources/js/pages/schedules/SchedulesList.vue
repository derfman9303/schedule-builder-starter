<template>
  <Head title="Schedules" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-6 py-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Your Schedules</h1>
        <Link
          href="/schedules/create"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          + New Schedule
        </Link>
      </div>

      <table class="min-w-full border-collapse border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Start Date</th>
            <th class="px-4 py-2 text-left">End Date</th>
            <th class="px-4 py-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="schedule in schedules"
            :key="schedule.id"
            class="hover:bg-gray-50"
          >
            <td class="border-t px-4 py-2">
              <Link
                :href="`/schedules/${schedule.id}/edit`"
                class="text-blue-600 hover:underline"
              >
                {{ schedule.name }}
              </Link>
            </td>
            <td class="border-t px-4 py-2">
              {{ formatDate(schedule.start_date) }}
            </td>
            <td class="border-t px-4 py-2">
              {{ formatDate(schedule.end_date) }}
            </td>
            <td class="border-t px-4 py-2 text-center">
              <Link
                :href="`/schedules/${schedule.id}`"
                class="text-gray-600 hover:text-gray-800 mr-2"
              >
                View
              </Link>
              <Link
                :href="`/schedules/${schedule.id}/edit`"
                class="text-blue-600 hover:text-blue-800"
              >
                Edit
              </Link>
            </td>
          </tr>
          <tr v-if="!schedules.length">
            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
              No schedules found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { type Schedule } from '@/types/Schedule';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Schedules', href: '/schedules' },
]

const schedules = ref<Schedule[]>([]);

const isLoading = ref(true);

function fetchData() {
    isLoading.value = true;

    axios.get('/api/schedules')
        .then((response) => {
            schedules.value = response.data;
        })
        .catch((error) => {
            console.error('Error fetching schedules:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
}

/**
 * Simple date formatter: YYYY-MM-DD → M/D/YYYY
 */
function formatDate(d: string) {
    const dt = new Date(d)
    return `${dt.getMonth() + 1}/${dt.getDate()}/${dt.getFullYear()}`
}

onMounted(() => {
    fetchData();
});
</script>