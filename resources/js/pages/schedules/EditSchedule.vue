<template>
    <ScheduleForm mode="edit" :schedule-id="page.props.schedule_id" />
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import ScheduleForm from './ScheduleForm.vue';

const page = usePage<{ schedule_id: number }>();
</script>