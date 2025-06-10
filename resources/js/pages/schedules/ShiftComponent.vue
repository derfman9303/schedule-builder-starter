<template>
    <Popover>
        <PopoverTrigger>
            <div class="border rounded p-2 w-[80px] min-h-[60px] flex cursor-pointer hover:bg-gray-100">
                <Plus class="text-gray-400 m-auto" />
            </div>
        </PopoverTrigger>
        <PopoverContent class="w-64">
            <!-- Shift form for Monday -->
            <div>
                <!-- Form fields for shift start and end times -->
                <Button class="w-full mt-2">Save Shift</Button>
            </div>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { Plus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
</script>