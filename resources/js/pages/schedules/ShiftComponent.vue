<template>
    <Popover>
        <PopoverTrigger>
            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                <Plus class="text-gray-400 m-auto" />
            </div>
        </PopoverTrigger>
        <PopoverContent class="w-64">
            <div class="flex flex-row justify-between">
                <label for="start-time" class="text-gray-700">Start Time:</label>
                <input
                    v-model="startTime"
                    id="start-time"
                    name="start time"
                    type="time"
                />
            </div>
            <div class="flex flex-row justify-between">
                <label for="end-time" class="text-gray-700">End Time:</label>
                <input
                    v-model="endTime"
                    id="end-time"
                    name="end time"
                    type="time"
                />
            </div>
            <Button class="w-full mt-2">Save Shift</Button>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { Plus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const startTime = ref('08:00');
const endTime = ref('17:00');
</script>