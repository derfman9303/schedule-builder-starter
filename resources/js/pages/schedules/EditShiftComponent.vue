<template>
        <Popover>
        <PopoverTrigger>
            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                {{ shift?.start_time }}
                <br />
                {{ shift?.end_time }}
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
                    class="p-[5px]"
                    name="start time"
                    type="time"
                />
            </div>
            <div class="flex flex-row justify-between">
                <label for="end-time" class="text-gray-700 p-[5px]">End Time:</label>
                <input
                    v-model="endTime"
                    id="end-time"
                    class="p-[5px]"
                    name="end time"
                    type="time"
                />
            </div>
            <PopoverClose class="w-full mt-4">
                <Button
                    class="bg-blue-500 hover:bg-blue-600 text-white cursor-pointer w-full"
                    @click="updateShift"
                >
                    Update Shift
                </Button>
            </PopoverClose>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { defineProps, ref } from 'vue';
import { type Shift } from '@/types/Shift';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { PopoverClose } from 'reka-ui';
import { Plus, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    shift: Shift | undefined;
    onUpdateShift: (shift: Shift) => void;
}>();

const startTime = ref(props.shift?.start_time);
const endTime = ref(props.shift?.end_time);

function updateShift() {
    props.onUpdateShift({
        ...props.shift,
        start_time: startTime.value,
        end_time: endTime.value,
    });
}
</script>
