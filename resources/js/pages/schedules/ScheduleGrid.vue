<template>
    <Card class="overflow-x-auto">
        <CardHeader>
            <CardTitle class="flex items-center gap-2">
                <Users class="h-5 w-5" />
                Employee Schedule Grid
            </CardTitle>
            <CardDescription>
                {{ gridDescription }}
            </CardDescription>
        </CardHeader>
        <CardContent>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[120px]">Employee</TableHead>
                        <TableHead 
                            v-for="day in 7" 
                            :key="day - 1" 
                            class="text-center min-w-[125px]"
                        >
                            {{ weekDayShort(day - 1) }}<br>
                            <span class="text-xs text-muted-foreground">{{ headerDateString(day - 1) }}</span>
                        </TableHead>
                        <TableHead class="text-center w-[100px]">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="(work_week, index) in schedule.work_weeks"
                        :key="work_week.employee_id"
                        class="hover:bg-muted/50"
                    >
                        <TableCell class="font-medium">{{ work_week.employee_name }}</TableCell>
                        <TableCell v-for="day_offset in [0,1,2,3,4,5,6]" :key="day_offset" class="p-1">
                            <div
                                @dragover="handleDragOver"
                                @dragleave="handleDragLeave"
                                @drop="(event) => handleDrop(event, index, day_offset, schedule)"
                                class="min-h-[45px] flex items-center justify-center transition-colors rounded-md border-2 border-dashed border-transparent hover:border-muted-foreground/25 m-auto"
                            >
                                <div
                                    v-if="getShift(work_week, day_offset)"
                                    draggable="true"
                                    @dragstart="(event) => handleDragStart(event, index, day_offset)"
                                    class="cursor-move w-full"
                                >
                                    <EditShiftComponent
                                        :shift="getShift(work_week, day_offset)"
                                        :color="getColor(index)"
                                        @update-shift="(shift) => updateShift(work_week, day_offset, shift)"
                                        @remove-shift="() => removeShift(work_week, day_offset)"
                                    />
                                </div>
                                <AddShiftComponent
                                    v-else
                                    @add-shift="(shift) => addShift(work_week, day_offset, shift)"
                                />
                            </div>
                        </TableCell>
                        <TableCell class="text-center">
                            <Button
                                variant="outline"
                                size="sm"
                                class="text-destructive hover:text-destructive cursor-pointer"
                                @click="removeWorkWeek(work_week, schedule)"
                            >
                                Remove
                            </Button>
                        </TableCell>
                    </TableRow>
                    <!-- Add Employee Row -->
                    <TableRow>
                        <TableCell>
                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        class="w-full h-[45px] border-dashed cursor-pointer"
                                    >
                                        <Plus class="h-4 w-4 mr-2" />
                                        Add Employee
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-80">
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <h4 class="font-medium">Add Employee to Schedule</h4>
                                            <p class="text-sm text-muted-foreground">
                                                Select an existing employee or add a new one.
                                            </p>
                                        </div>
                                        <Tabs default-value="existing">
                                            <TabsList class="grid w-full grid-cols-2">
                                                <TabsTrigger
                                                    value="existing"
                                                    class="cursor-pointer"
                                                >
                                                    Existing
                                                </TabsTrigger>
                                                <TabsTrigger
                                                    value="new"
                                                    class="cursor-pointer"
                                                >
                                                    New
                                                </TabsTrigger>
                                            </TabsList>
                                            <TabsContent value="existing" class="space-y-2">
                                                <Select
                                                    v-model="selectedEmployee"
                                                    :disabled="selectedEmployees.length === 0"
                                                >
                                                    <SelectTrigger class="cursor-pointer">
                                                        <SelectValue :placeholder="selectedEmployees.length === 0 ? 'No employees available' : 'Select an employee'" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem
                                                            v-for="employee in selectedEmployees"
                                                            :key="employee.id"
                                                            :value="employee"
                                                            class="cursor-pointer"
                                                        >
                                                            {{ employee.first_name }} {{ employee.last_name }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                <PopoverClose as-child>
                                                    <Button
                                                        @click="addWorkWeek(selectedEmployee, schedule, selectedEmployee, newEmployee)"
                                                        :disabled="!selectedEmployee"
                                                        class="w-full cursor-pointer bg-blue-500 hover:bg-blue-600"
                                                    >
                                                        Add Employee
                                                    </Button>
                                                </PopoverClose>
                                            </TabsContent>
                                            <TabsContent value="new" class="space-y-2">
                                                <Input
                                                    v-model="newEmployee.full_name"
                                                    placeholder="Employee Name"
                                                />
                                                <PopoverClose as-child>
                                                    <Button
                                                        @click="addWorkWeek(newEmployee, schedule, selectedEmployee, newEmployee)"
                                                        :disabled="!newEmployee.full_name"
                                                        class="w-full cursor-pointer bg-blue-500 hover:bg-blue-600"
                                                    >
                                                        Add New Employee
                                                    </Button>
                                                </PopoverClose>
                                            </TabsContent>
                                        </Tabs>
                                    </div>
                                </PopoverContent>
                            </Popover>
                        </TableCell>
                        <TableCell :colspan="8" class="bg-muted/30"></TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Table, TableHeader, TableHead, TableRow, TableBody, TableCell } from '@/components/ui/table';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { PopoverClose } from 'reka-ui';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AddShiftComponent from './AddShiftComponent.vue';
import EditShiftComponent from './EditShiftComponent.vue';
import { Plus, Users } from 'lucide-vue-next';
import { useSchedule } from '@/composables/useSchedule';
import { type Employee } from '@/types/Employee';
import { type Schedule } from '@/types/Schedule';

interface Props {
    schedule: Schedule;
    employees: Employee[];
    gridDescription: string;
}

const props = defineProps<Props>();

const {
    headerDateString,
    weekDayShort,
    getShift,
    addShift,
    updateShift,
    removeShift,
    getColor,
    addWorkWeek,
    removeWorkWeek,
    handleDragStart,
    handleDragOver,
    handleDragLeave,
    handleDrop,
} = useSchedule();

const newEmployee = ref<Employee>({ 
    full_name: '',
});

const selectedEmployee = ref<Employee | null>(null);

const selectedEmployees = computed(() => {
    return props.employees.filter(emp => !props.schedule.work_weeks?.some(ww => ww.employee_id === emp.id));
});
</script>
