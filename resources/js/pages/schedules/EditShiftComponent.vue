<template>
    <Popover>
        <PopoverTrigger>
            <div class="border rounded-lg p-2 w-[100px] min-h-[60px] cursor-pointer hover:bg-gray-100 text-sm flex flex-col justify-center">
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <ArrowBigRightDash :size="16" class="text-gray-500" />
                    </div>
                    <span class="text-gray-500">{{ formatTimeTo12Hour(props.shift?.start_time) }}</span>
                </div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <ArrowBigLeftDash :size="16" class="text-gray-500" />
                    </div>
                    <span class="text-gray-500">{{ formatTimeTo12Hour(props.shift?.end_time) }}</span>
                </div>
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
            <div class="flex justify-between w-full mt-4">
                <PopoverClose class="w-[48%]">
                    <Button
                        class="bg-blue-500 hover:bg-blue-600 text-white cursor-pointer w-full"
                        @click="updateShift"
                    >
                        Update
                    </Button>
                </PopoverClose>
                <PopoverClose class="w-[48%]">
                    <Button
                        class="bg-red-500 hover:bg-red-600 text-white cursor-pointer w-full"
                        @click="removeShift"
                    >
                        Remove
                    </Button>
                </PopoverClose>
            </div>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { defineProps, ref } from 'vue';
import { type Shift } from '@/types/Shift';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { PopoverClose } from 'reka-ui';
import { ArrowBigRightDash, ArrowBigLeftDash, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    shift: Shift | undefined;
    onUpdateShift: (shift: Shift) => void;
    onRemoveShift: (shift: Shift) => void;
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

function removeShift() {
    props.onRemoveShift({
        ...props.shift
    });
}

function formatTimeTo12Hour(time: string | undefined): string {
    if (!time) return '';

    const [hour, minute] = time.split(':').map(Number);
    const period = hour >= 12 ? ' PM' : ' AM';
    const formattedHour = hour % 12 || 12; // Convert 0 to 12 for midnight

    return `${formattedHour}:${minute.toString().padStart(2, '0')}${period}`;
}
</script>
