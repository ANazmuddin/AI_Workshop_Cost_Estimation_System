<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <!-- Logo & Branding -->
        <div>
            <Link href="/" class="flex items-center gap-3">
                <ApplicationLogo class="w-12 h-12 fill-current text-blue-600" />
                <span class="font-bold text-3xl text-gray-900 tracking-tight">Smart<span class="text-blue-600">Bengkel</span></span>
            </Link>
        </div>

        <!-- Form Container -->
        <div class="w-full sm:max-w-md mt-10 px-8 py-10 bg-white shadow-xl border border-gray-100 overflow-hidden sm:rounded-2xl">
            <slot />
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-400">
            &copy; {{ new Date().getFullYear() }} SmartBengkel AI. All rights reserved.
        </div>
    </div>
</template>