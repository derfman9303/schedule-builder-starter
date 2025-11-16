<template>
    <Popover>
        <PopoverTrigger>
            <div class="border rounded-xs p-2 w-[125px] min-h-[40px] flex cursor-pointer">
                <Plus :size="20" class="text-gray-300 m-auto" />
            </div>
        </PopoverTrigger>
        <PopoverContent class="w-64">
            <div class="flex flex-col items-end mr-[5px] mb-2">
                <PopoverClose>
                    <X :size="20" class="text-gray-400 m-auto cursor-pointer" />
                </PopoverClose>
            </div>
            <div class="flex flex-row justify-between">
                <label for="start-time" class="text-gray-700 p-[5px]">Start Time:</label>
                <input
                    v-model="startTime"
                    id="start-time"
                    class="p-[5px] cursor-pointer"
                    name="start time"
                    type="time"
                />
            </div>
            <div class="flex flex-row justify-between">
                <label for="end-time" class="text-gray-700 p-[5px]">End Time:</label>
                <input
                    v-model="endTime"
                    id="end-time"
                    class="p-[5px] cursor-pointer"
                    name="end time"
                    type="time"
                />
            </div>
            <PopoverClose class="w-full mt-4">
                <Button
                    class="bg-blue-500 hover:bg-blue-600 text-white cursor-pointer w-full"
                    @click="addShift"
                >
                    Add Shift
                </Button>
            </PopoverClose>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { ref, defineEmits } from 'vue';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { PopoverClose } from 'reka-ui';
import { Plus, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const startTime = ref('08:00:00');
const endTime = ref('17:00:00');

const emit = defineEmits(['add-shift']);

function addShift() {
    emit('add-shift', {
        start_time: startTime.value + ':00',
        end_time: endTime.value + ':00',
    });
}
</script>