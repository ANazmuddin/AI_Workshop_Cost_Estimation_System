<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-white flex">
        
        <!-- ============================== -->
        <!-- SISI KIRI: GAMBAR (HANYA DESKTOP) -->
        <!-- ============================== -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900 items-center justify-center overflow-hidden">
            <!-- Latar Belakang Gambar -->
            <!-- Ganti URL ini dengan path gambar ilustrasi bengkel/mekanik milikmu di folder public/images -->
            <img 
                src="/images/servis_motor.jpg" 
                alt="Ilustrasi Bengkel" 
                class="absolute inset-0 w-full h-full object-cover opacity-40"
            />
            
            <!-- Teks Overlay di atas Gambar -->
            <div class="relative z-10 p-12 text-center text-white">
                <div class="flex justify-center mb-6">
                    <ApplicationLogo class="w-16 h-16 fill-current text-blue-500" />
                </div>
                <h1 class="text-4xl font-bold mb-4">Sistem Cerdas Estimasi Bengkel</h1>
                <p class="text-lg text-gray-300 max-w-md mx-auto">
                    Analisis kerusakan sepeda motor secara instan dan dapatkan estimasi biaya perbaikan paling akurat menggunakan teknologi Artificial Intelligence.
                </p>
            </div>
        </div>

        <!-- ============================== -->
        <!-- SISI KANAN: FORM OTENTIKASI    -->
        <!-- ============================== -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 sm:px-12 md:px-24 py-12">
            
            <!-- Logo untuk Mobile (Muncul jika gambar kiri hilang) -->
            <div class="lg:hidden flex justify-center mb-10">
                <Link href="/" class="flex items-center gap-3">
                    <ApplicationLogo class="w-12 h-12 fill-current text-blue-600" />
                    <span class="font-bold text-3xl text-gray-900 tracking-tight">Smart<span class="text-blue-600">Bengkel</span></span>
                </Link>
            </div>
            
            <!-- Container Form -->
            <div class="w-full max-w-md mx-auto">
                <slot />
            </div>
            
            <!-- Footer Form -->
            <div class="mt-12 text-center text-xs text-gray-400">
                &copy; {{ new Date().getFullYear() }} SmartBengkel AI. All rights reserved.
            </div>
        </div>

    </div>
</template>