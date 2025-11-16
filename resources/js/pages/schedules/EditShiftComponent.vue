<template>
    <Popover>
        <PopoverTrigger>
            <div
                class="rounded-lg px-2 py-[3px] w-[125px] min-h-[40px] cursor-pointer text-xs flex flex-col justify-center"
                :class="color"
            >
                <span class="text-white">Retail</span>
                <span class="text-white">{{ formatTimeTo12Hour(props.shift?.start_time) }} - {{ formatTimeTo12Hour(props.shift?.end_time) }}</span>
            </div>
        </PopoverTrigger>
        <PopoverContent class="w-64">
            <div class="flex flex-col items-end mr-[5px] mb-2">
                <PopoverClose>
                    <X :size="20" class="text-gray-400 m-auto cursor-pointer" />
                </PopoverClose>
            </div>
            <div class="flex flex-row justify-between">
                <label for="start-time" class="text-gray-700 p-[5px] pl-0">Start Time:</label>
                <input
                    v-model="startTime"
                    @change="updateShift"
                    id="start-time"
                    class="p-[5px] cursor-pointer"
                    name="start time"
                    type="time"
                />
            </div>
            <div class="flex flex-row justify-between">
                <label for="end-time" class="text-gray-700 p-[5px] pl-0">End Time:</label>
                <input
                    v-model="endTime"
                    @change="updateShift"
                    id="end-time"
                    class="p-[5px] cursor-pointer"
                    name="end time"
                    type="time"
                />
            </div>
            <div class="flex flex-col gap-2 mt-2">
                <label for="department" class="text-gray-700 text-sm">Department (Optional):</label>
                <Select
                    v-model="departmentId"
                    @update:model-value="updateShift"
                    class="w-full"
                >
                    <SelectTrigger class="cursor-pointer w-full">
                        <SelectValue placeholder="Select a Department" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="null" class="cursor-pointer">
                            None
                        </SelectItem>
                        <SelectItem
                            v-for="department in departments"
                            :key="department.id"
                            :value="department.id"
                            class="cursor-pointer"
                        >
                            {{ department.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex justify-between w-full mt-4">
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
import { X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useDepartments } from '@/composables/useDepartments';

const props = defineProps<{
    shift: Shift | undefined;
    color: string;
    onUpdateShift: (shift: Shift) => void;
    onRemoveShift: (shift: Shift) => void;
}>();
const emit = defineEmits(['update-shift', 'remove-shift']);
const startTime = ref(props.shift?.start_time);
const endTime = ref(props.shift?.end_time);
const departmentId = ref<number | null>(props.shift?.department_id ?? null);

const { departments } = useDepartments();

function updateShift() {
    emit('update-shift', {
        ...props.shift,
        start_time: startTime.value,
        end_time: endTime.value,
        department_id: departmentId.value,
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
