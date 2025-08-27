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
    <Head title="Timelio - Simplify Your Workforce Management" />
    <div class="flex min-h-screen flex-col items-center bg-gradient-to-br from-slate-50 text-gray-900 dark:from-gray-900 dark:via-gray-900 dark:to-blue-950 dark:text-white overflow-x-hidden">
        <header class="fixed top-0 left-0 right-0 bg-white/70 backdrop-blur-xl border-b border-white/20 dark:bg-gray-900/70 dark:border-gray-800/20 z-50 transition-all duration-300">
            <div class="mx-auto max-w-7xl px-6 py-4">
                <nav class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-cyan-400">Timelio</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            v-if="page.props.auth?.user"
                            :href="route('dashboard')"
                            class="relative rounded-xl px-6 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-200 dark:hover:text-white transition-all duration-200 hover:bg-gray-100/80 dark:hover:bg-gray-800/80 group"
                        >
                            <span class="relative z-10">Dashboard</span>
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-600/0 to-cyan-600/0 group-hover:from-blue-600/5 group-hover:to-cyan-600/5 transition-all duration-200"></div>
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="relative rounded-xl px-6 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-200 dark:hover:text-white transition-all duration-200 hover:bg-gray-100/80 dark:hover:bg-gray-800/80 group"
                            >
                                <span class="relative z-10">Log in</span>
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-600/0 to-cyan-600/0 group-hover:from-blue-600/5 group-hover:to-cyan-600/5 transition-all duration-200"></div>
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="relative rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-200 hover:scale-105 group"
                            >
                                <span class="relative z-10">Get Started</span>
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-700 to-cyan-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </Link>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <div class="relative mt-24 px-6 lg:px-8 pt-16 pb-8">
            <div class="mx-auto max-w-7xl">
                <div class="text-center relative">
                    
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight leading-tight">
                        <span class="block bg-gradient-to-r from-gray-900 via-blue-800 to-gray-900 dark:from-white dark:via-blue-300 dark:to-white bg-clip-text text-transparent">
                            Effortless
                        </span>
                        <span class="block bg-gradient-to-r from-blue-600 via-cyan-500 to-purple-600 dark:from-blue-400 dark:via-cyan-400 dark:to-purple-400 bg-clip-text text-transparent mt-2">
                            Employee Scheduling
                        </span>
                    </h1>
                    <p class="mt-8 text-xl leading-8 text-gray-600 dark:text-gray-300 max-w-3xl mx-auto font-light">
                        Transform your workforce management with intelligent scheduling. 
                        <span class="font-medium text-gray-800 dark:text-gray-200">Save time, reduce conflicts,</span> 
                        and keep your team happy with our intuitive platform designed for modern businesses.
                    </p>
                    <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                        <Link
                            :href="route('register')"
                            class="group relative rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-cyan-600 px-8 py-4 text-lg font-semibold text-white shadow-2xl shadow-blue-500/30 hover:shadow-blue-500/40 transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-500/50 w-full sm:w-auto"
                        >
                            <span class="relative z-10 flex items-center justify-center space-x-2">
                                <span>Start Free Trial</span>
                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-700 via-blue-800 to-cyan-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </Link>
                    </div>

                    <!-- Trust indicators -->
                    <div class="mt-16 flex items-center justify-center space-x-8 text-gray-500 dark:text-gray-400">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium">14-day free trial</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium">No credit card required</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium">Setup in 5 minutes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-10 border-t border-gray-200/50 dark:border-gray-800/50 bg-white/30 dark:bg-gray-900/30">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Product</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Features</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Pricing</a></li>
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Support</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-12 pt-8 border-t border-gray-200/50 dark:border-gray-800/50">
                    <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                        &copy; 2025 Timelio. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Enhanced hover effects */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

.group:hover .group-hover\:translate-x-1 {
  transform: translateX(0.25rem);
}

/* Glass morphism effect enhancement */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
}

/* Enhanced gradient text */
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

/* Scroll margin for smooth anchor scrolling */
.scroll-mt-24 {
  scroll-margin-top: 6rem;
}

/* Custom shadow enhancements */
.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.shadow-blue-500\/30 {
  box-shadow: 0 10px 25px -3px rgba(59, 130, 246, 0.3);
}

.shadow-blue-500\/40 {
  box-shadow: 0 20px 40px -12px rgba(59, 130, 246, 0.4);
}
</style>
