<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Calendar, Users, Clock, FileText, Plus, TrendingUp } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Dashboard data
const recentSchedules = ref([]);
const totalEmployees = ref(0);
const totalSchedules = ref(0);
const upcomingShifts = ref(0);
const loading = ref(true);

// Fetch dashboard data
const fetchDashboardData = async () => {
    try {
        const [schedulesResponse, employeesResponse] = await Promise.all([
            axios.get('/api/schedules'),
            axios.get('/api/employees')
        ]);
        
        recentSchedules.value = schedulesResponse.data.slice(0, 5);
        totalSchedules.value = schedulesResponse.data.length;
        totalEmployees.value = employeesResponse.data.length;
        
        // Calculate upcoming shifts in the next 7 days
        const now = new Date();
        const nextWeek = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
        
        upcomingShifts.value = schedulesResponse.data.reduce((count, schedule) => {
            const scheduleStart = new Date(schedule.start_date);
            const scheduleEnd = new Date(schedule.end_date);
            
            if (scheduleStart <= nextWeek && scheduleEnd >= now) {
                return count + (schedule.work_weeks?.reduce((shiftCount, week) => {
                    return shiftCount + (week.shifts?.length || 0);
                }, 0) || 0);
            }
            return count;
        }, 0);
        
        loading.value = false;
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
        loading.value = false;
    }
};

// Navigation functions
const navigateToSchedules = () => {
    router.visit('/schedules');
};

const navigateToEmployees = () => {
    router.visit('/employees');
};

const navigateToNewSchedule = () => {
    router.visit('/schedules/new');
};

const navigateToAddEmployee = () => {
    router.visit('/employees/add');
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

onMounted(() => {
    fetchDashboardData();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Welcome Section -->
            <div class="flex flex-col space-y-2">
                <h1 class="text-3xl font-bold tracking-tight">Welcome to Timelio</h1>
                <p class="text-muted-foreground">
                    Manage your employees and schedules efficiently from your dashboard.
                </p>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Employees</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ loading ? '-' : totalEmployees }}</div>
                        <p class="text-xs text-muted-foreground">
                            Active team members
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Schedules</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ loading ? '-' : totalSchedules }}</div>
                        <p class="text-xs text-muted-foreground">
                            Created schedules
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Upcoming Shifts</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ loading ? '-' : upcomingShifts }}</div>
                        <p class="text-xs text-muted-foreground">
                            Next 7 days
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Productivity</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ loading ? '-' : Math.round((totalSchedules / Math.max(totalEmployees, 1)) * 100) }}%</div>
                        <p class="text-xs text-muted-foreground">
                            Schedule efficiency
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Plus class="h-5 w-5" />
                            Quick Actions
                        </CardTitle>
                        <CardDescription>
                            Common tasks to get you started
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-3">
                        <Button @click="navigateToNewSchedule" class="w-full justify-start" variant="outline">
                            <Calendar class="h-4 w-4 mr-2" />
                            Create New Schedule
                        </Button>
                        <Button @click="navigateToAddEmployee" class="w-full justify-start" variant="outline">
                            <Users class="h-4 w-4 mr-2" />
                            Add New Employee
                        </Button>
                        <Button @click="navigateToSchedules" class="w-full justify-start" variant="outline">
                            <FileText class="h-4 w-4 mr-2" />
                            View All Schedules
                        </Button>
                        <Button @click="navigateToEmployees" class="w-full justify-start" variant="outline">
                            <Users class="h-4 w-4 mr-2" />
                            Manage Employees
                        </Button>
                    </CardContent>
                </Card>

                <!-- Recent Schedules -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            Recent Schedules
                        </CardTitle>
                        <CardDescription>
                            Your latest schedule activities
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="loading" class="space-y-3">
                            <div v-for="i in 3" :key="i" class="flex items-center space-x-4">
                                <div class="h-4 w-4 bg-muted rounded animate-pulse"></div>
                                <div class="space-y-2 flex-1">
                                    <div class="h-4 bg-muted rounded w-3/4 animate-pulse"></div>
                                    <div class="h-3 bg-muted rounded w-1/2 animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="recentSchedules.length === 0" class="text-center py-8 text-muted-foreground">
                            No schedules created yet
                            <Button @click="navigateToNewSchedule" class="mt-3 w-full" variant="outline" size="sm">
                                Create Your First Schedule
                            </Button>
                        </div>
                        <div v-else class="space-y-3">
                            <div v-for="schedule in recentSchedules" :key="schedule.id" 
                                 class="flex items-center justify-between p-3 rounded-lg border hover:bg-accent/50 transition-colors cursor-pointer"
                                 @click="router.visit(`/schedules/edit/${schedule.id}`)">
                                <div class="flex-1">
                                    <p class="font-medium text-sm">
                                        {{ schedule.name || `Schedule #${schedule.id}` }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ formatDate(schedule.start_date) }} - {{ formatDate(schedule.end_date) }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ schedule.work_weeks?.length || 0 }} employees assigned
                                    </p>
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ new Date(schedule.created_at).toLocaleDateString() }}
                                </div>
                            </div>
                            <Button @click="navigateToSchedules" variant="ghost" size="sm" class="w-full mt-3">
                                View All Schedules
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Bottom Section - Tips and Getting Started -->
            <Card>
                <CardHeader>
                    <CardTitle>Getting Started</CardTitle>
                    <CardDescription>
                        Tips to help you get the most out of Schedule Builder
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="flex flex-col space-y-2">
                            <div class="flex items-center gap-2">
                                <Users class="h-4 w-4 text-primary" />
                                <h4 class="font-medium">1. Add Employees</h4>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                Start by adding your team members with their contact information.
                            </p>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <div class="flex items-center gap-2">
                                <Calendar class="h-4 w-4 text-primary" />
                                <h4 class="font-medium">2. Create Schedules</h4>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                Build weekly schedules by assigning shifts to your employees.
                            </p>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <div class="flex items-center gap-2">
                                <FileText class="h-4 w-4 text-primary" />
                                <h4 class="font-medium">3. Export & Share</h4>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                Export your schedules as PDF and share them with your team.
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
