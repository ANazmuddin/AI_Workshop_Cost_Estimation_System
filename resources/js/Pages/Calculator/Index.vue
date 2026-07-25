<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Nilai Default (Contoh: Spesifikasi standar Honda Vario 125)
const bore = ref(52.4);  // Diameter Piston (mm)
const stroke = ref(57.9); // Langkah Piston (mm)
const vc = ref(11.3);    // Volume Ruang Bakar (cc)

// KALKULASI REAL-TIME
// Rumus Volume Silinder (CC): (Pi / 4) x (Bore/10)^2 x (Stroke/10)
const engineCC = computed(() => {
    if (!bore.value || !stroke.value) return 0;
    const b = bore.value / 10; // Konversi mm ke cm
    const s = stroke.value / 10; // Konversi mm ke cm
    return (Math.PI / 4) * Math.pow(b, 2) * s;
});

// Rumus Rasio Kompresi (CR): (Volume Silinder + Volume Ruang Bakar) / Volume Ruang Bakar
const compressionRatio = computed(() => {
    if (!vc.value || vc.value <= 0) return 0;
    return (engineCC.value + vc.value) / vc.value;
});
</script>

<template>
    <Head title="Kalkulator Kompresi Mesin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Alat Mekanik</h2>
        </template>

        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- ============================== -->
                    <!-- KOLOM KIRI: FORM INPUT         -->
                    <!-- ============================== -->
                    <div class="lg:col-span-7 bg-white shadow-sm sm:rounded-2xl p-6 md:p-8 border border-gray-100">
                        
                        <div class="mb-6 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900">Kalkulator Kompresi & Kapasitas (CC)</h3>
                        </div>
                        <p class="mb-8 text-sm text-gray-600">Masukkan ukuran diameter piston, langkah piston, dan volume ruang bakar untuk menghitung kapasitas mesin (displacement) dan rasio kompresi secara instan.</p>

                        <div class="space-y-6">
                            <!-- Input Bore -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Diameter Piston / Bore (mm)</label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.1"
                                        v-model.number="bore"
                                        class="block w-full pl-4 pr-12 py-3 text-lg font-medium border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg bg-gray-50 transition-colors"
                                    >
                                    <span class="absolute right-4 top-3.5 text-gray-400 font-bold">mm</span>
                                </div>
                            </div>

                            <!-- Input Stroke -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Langkah Piston / Stroke (mm)</label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.1"
                                        v-model.number="stroke"
                                        class="block w-full pl-4 pr-12 py-3 text-lg font-medium border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg bg-gray-50 transition-colors"
                                    >
                                    <span class="absolute right-4 top-3.5 text-gray-400 font-bold">mm</span>
                                </div>
                            </div>

                            <!-- Input Volume Ruang Bakar (Vc) -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Volume Ruang Bakar / Vc (cc)</label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.1"
                                        v-model.number="vc"
                                        class="block w-full pl-4 pr-12 py-3 text-lg font-medium border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg bg-gray-50 transition-colors"
                                    >
                                    <span class="absolute right-4 top-3.5 text-gray-400 font-bold">cc</span>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">*Gunakan cairan buret untuk mengukur volume kubah head saat piston berada di Titik Mati Atas (TMA).</p>
                            </div>
                        </div>

                    </div>

                    <!-- ============================== -->
                    <!-- KOLOM KANAN: HASIL REAL-TIME   -->
                    <!-- ============================== -->
                    <div class="lg:col-span-5">
                        
                        <div class="bg-gray-800 text-white shadow-xl sm:rounded-2xl p-6 md:p-8 sticky top-6 border border-gray-700">
                            <h3 class="text-xl font-bold mb-6 border-b border-gray-700 pb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Hasil Perhitungan
                            </h3>
                            
                            <!-- Kartu Kapasitas Mesin (CC) -->
                            <div class="mb-5 bg-gray-900/60 p-5 rounded-xl border border-gray-700">
                                <p class="text-gray-400 text-sm mb-1">Kapasitas Mesin (Displacement):</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-4xl font-black text-blue-400">{{ engineCC.toFixed(1) }}</p>
                                    <span class="text-lg font-bold text-gray-500">cc</span>
                                </div>
                            </div>

                            <!-- Kartu Rasio Kompresi -->
                            <div class="mb-5 bg-blue-900/40 p-5 rounded-xl border border-blue-800/50">
                                <p class="text-blue-300 text-sm mb-1">Rasio Kompresi:</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-4xl font-black text-green-400">{{ compressionRatio.toFixed(2) }}</p>
                                    <span class="text-xl font-bold text-gray-400">: 1</span>
                                </div>
                            </div>
                            
                            <div class="mt-6 p-4 bg-gray-900/50 rounded-lg border border-gray-700">
                                <h4 class="text-sm font-bold text-gray-300 mb-2">Rekomendasi Bahan Bakar:</h4>
                                <ul class="text-xs text-gray-400 space-y-1.5">
                                    <li class="flex gap-2"><span class="text-blue-400 font-bold">9.0 - 10.0 : 1</span> = Pertalite (RON 90)</li>
                                    <li class="flex gap-2"><span class="text-green-400 font-bold">10.0 - 11.0 : 1</span> = Pertamax (RON 92)</li>
                                    <li class="flex gap-2"><span class="text-red-400 font-bold">> 11.0 : 1</span> = Pertamax Turbo (RON 98)</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>