<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { PageProps as InertiaPageProps } from '@inertiajs/core';

interface Props {
    canLogin?: boolean;
    canRegister?: boolean;
}

interface Auth {
    user: {
        id: number;
        name: string;
        email: string;
    } | null;
}

interface CustomPageProps extends InertiaPageProps {
    auth: Auth;
    canLogin?: boolean;
    canRegister?: boolean;
}

const page = usePage<CustomPageProps>();

defineProps<Props>();
</script>

<template>
    <Head title="Schedule Builder - Simplify Your Workforce Management" />
    <div class="flex min-h-screen flex-col items-center bg-white p-6 text-gray-900 dark:bg-gray-900 dark:text-white lg:justify-center lg:p-8">
        <header class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md dark:bg-gray-900/80 z-50">
            <div class="mx-auto max-w-7xl px-6 py-4">
                <nav class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-xl font-bold text-blue-600 dark:text-blue-400">Schedule Builder</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <Link
                            v-if="page.props.auth?.user"
                            :href="route('dashboard')"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <div class="mt-32 px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-6xl bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-cyan-400">
                        Effortless Employee Scheduling
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Create, manage, and optimize your team's schedule with ease. Save time, reduce conflicts, and keep your workforce happy with our intuitive scheduling platform.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <Link
                            :href="route('register')"
                            class="rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600"
                        >
                            Start Free Trial
                        </Link>
                        <a
                            href="#features"
                            class="text-base font-semibold leading-6 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400"
                        >
                            Learn more <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>

                <!-- Features Section -->
                <div id="features" class="mt-32 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="rounded-2xl bg-gray-50 p-8 dark:bg-gray-800">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 dark:bg-blue-500">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold">Drag & Drop Scheduling</h2>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">Create and modify schedules with simple drag and drop actions. No more complex spreadsheets.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="rounded-2xl bg-gray-50 p-8 dark:bg-gray-800">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 dark:bg-blue-500">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold">Smart Notifications</h2>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">Automated alerts for schedule changes, conflict resolution, and shift reminders.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="rounded-2xl bg-gray-50 p-8 dark:bg-gray-800">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 dark:bg-blue-500">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold">Analytics & Reports</h2>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">Gain insights into scheduling patterns, labor costs, and team availability trends.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-32 border-t border-gray-200 dark:border-gray-800">
            <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8">
                <div class="flex justify-center space-x-6 md:order-2">
                    <a href="#" class="text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300">
                        <span class="sr-only">Features</span>
                        Features
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300">
                        <span class="sr-only">Help Center</span>
                        Help Center
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300">
                        <span class="sr-only">Contact</span>
                        Contact
                    </a>
                </div>
                <div class="mt-8 md:order-1 md:mt-0">
                    <p class="text-center text-xs leading-5 text-gray-500 dark:text-gray-400">
                        &copy; 2024 Schedule Builder. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
