<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// MENERIMA PROPS DARI CONTROLLER
const props = defineProps({
    result: Object,
    errors: Object,
    motorTypes: Array
});

// INISIALISASI FORM
const form = useForm({
    motor_type: props.motorTypes && props.motorTypes.length > 0 ? props.motorTypes[0] : '',
    symptoms: []
});

// DATA GEJALA KELUHAN (Termasuk kategori Kelistrikan)
const categories = ref([
    {
        name: 'Area CVT / Transmisi (Metic)',
        symptoms: [
            { id: 'SYM-001', label: 'Tarikan awal terasa bergetar / gredek' },
            { id: 'SYM-002', label: 'Tenaga ngempos di tanjakan' },
            { id: 'SYM-003', label: 'Ada suara decit / kasar di area CVT' },
        ]
    },
    {
        name: 'Area Mesin',
        symptoms: [
            { id: 'SYM-004', label: 'Mesin susah dihidupkan di pagi hari' },
            { id: 'SYM-005', label: 'Mesin sering mati mendadak' },
        ]
    },
    {
        name: 'Area Pengereman',
        symptoms: [
            { id: 'SYM-006', label: 'Rem terasa kurang pakem / blong' },
            { id: 'SYM-007', label: 'Terdengar suara gesekan saat mengerem' },
        ]
    },
    {
        name: 'Area Kelistrikan & Injeksi',
        symptoms: [
            { id: 'SYM-008', label: 'Lampu utama sering redup / mati' },
            { id: 'SYM-009', label: 'Klakson bersuara pelan / mati' },
            { id: 'SYM-010', label: 'Indikator mesin (Check Engine) menyala' },
            { id: 'SYM-011', label: 'Starter tangan tidak berfungsi (bunyi cetek-cetek)' },
        ]
    }
]);

// FUNGSI SUBMIT FORM
const submitDiagnosis = () => {
    form.post(route('diagnosis.calculate'), {
        preserveScroll: true
    });
};

// FORMAT MATA UANG RUPIAH
const formatRupiah = (angka) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
};
</script>

<template>
    <Head title="Cek Estimasi Biaya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Diagnosis AI</h2>
        </template>

        <!-- Latar belakang sedikit diredupkan agar card putih lebih menonjol -->
        <div class="py-8 bg-gray-50 min-h-screen">
            <!-- max-w-7xl untuk layout yang lebih lebar dan proporsional -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Pembagian Grid: 7 Kolom Kiri, 5 Kolom Kanan -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- ============================== -->
                    <!-- KOLOM KIRI: FORM KELUHAN     -->
                    <!-- ============================== -->
                    <div class="lg:col-span-7 bg-white shadow-sm sm:rounded-2xl p-6 md:p-8 border border-gray-100">
                        
                        <!-- Header Form -->
                        <div class="mb-6 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900">Diagnosis Mandiri AI</h3>
                        </div>
                        <p class="mb-8 text-sm text-gray-600">Centang keluhan yang paling sesuai dengan kondisi sepeda motor Anda saat ini.</p>

                        <!-- Alert Error API Python -->
                        <div v-if="errors && errors.message" class="mb-6 p-4 text-red-700 bg-red-100 rounded-lg flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ errors.message }}</span>
                        </div>

                        <form @submit.prevent="submitDiagnosis">
                            
                            <!-- Dropdown Tipe Motor -->
                            <div class="mb-8">
                                <label for="motor_type" class="block text-sm font-semibold text-gray-700 mb-2">Tipe Sepeda Motor</label>
                                <select 
                                    id="motor_type" 
                                    v-model="form.motor_type"
                                    class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-gray-50 transition-colors"
                                >
                                    <option v-for="type in motorTypes" :key="type" :value="type">
                                        {{ type }}
                                    </option>
                                </select>
                            </div>

                            <!-- Looping Kategori & Checkbox Gejala (Format Grid Cards) -->
                            <div v-for="category in categories" :key="category.name" class="mb-8">
                                
                                <!-- Judul Kategori -->
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-1.5 h-5 bg-blue-600 rounded-full"></div>
                                    <h4 class="font-bold text-gray-800">{{ category.name }}</h4>
                                </div>
                                
                                <!-- Grid Cards 2 Kolom -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Kita gunakan tag <label> sebagai pembungkus utama agar seluruh area kotak bisa diklik -->
                                    <label 
                                        v-for="symptom in category.symptoms" 
                                        :key="symptom.id"
                                        :for="symptom.id"
                                        class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 group"
                                        :class="[
                                            form.symptoms.includes(symptom.id) 
                                                ? 'border-blue-600 bg-blue-50/50 ring-1 ring-blue-600 shadow-sm' 
                                                : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-gray-50'
                                        ]"
                                    >
                                        <div class="flex items-center h-5">
                                            <input 
                                                :id="symptom.id" 
                                                type="checkbox" 
                                                :value="symptom.id"
                                                v-model="form.symptoms"
                                                class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer transition-colors"
                                            >
                                        </div>
                                        <div class="ml-3 text-sm flex-1">
                                            <span 
                                                class="font-medium transition-colors"
                                                :class="form.symptoms.includes(symptom.id) ? 'text-blue-900' : 'text-gray-600 group-hover:text-gray-900'"
                                            >
                                                {{ symptom.label }}
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <button 
                                    type="submit" 
                                    class="w-full flex justify-center items-center gap-2 px-4 py-3.5 bg-blue-600 rounded-lg font-bold text-white text-sm hover:bg-blue-700 transition-all duration-200"
                                    :disabled="form.processing || form.symptoms.length === 0"
                                    :class="{ 'opacity-50 cursor-not-allowed': form.symptoms.length === 0 || form.processing }"
                                >
                                    <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span v-if="form.processing">Memproses AI...</span>
                                    <span v-else>Cek Estimasi Biaya</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- ============================== -->
                    <!-- KOLOM KANAN: NOTA / EMPTY      -->
                    <!-- ============================== -->
                    <div class="lg:col-span-5">
                        
                        <!-- JIKA ADA HASIL (NOTA GELAP) -->
                        <div v-if="result" class="bg-gray-800 text-white shadow-xl sm:rounded-2xl p-6 md:p-8 sticky top-6 border border-gray-700 max-h-[85vh] overflow-y-auto">
                            <h3 class="text-xl font-bold mb-6 border-b border-gray-700 pb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Hasil Multi-Diagnosis AI
                            </h3>
                            
                            <!-- LOOPING DAFTAR KERUSAKAN -->
                            <div v-for="(item, index) in result.details" :key="index" class="mb-5 bg-gray-900/60 p-5 rounded-xl border border-gray-700">
                                <div class="mb-3">
                                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Prediksi Kerusakan {{ index + 1 }}</p>
                                    <p class="text-lg font-bold text-red-400">{{ item.diagnosis_name }}</p>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between items-start gap-4">
                                        <div class="flex-1">
                                            <p class="text-gray-400 text-xs">Suku Cadang:</p>
                                            <p class="font-medium text-gray-200 mt-0.5 leading-snug">{{ item.part_name }}</p>
                                        </div>
                                        <p class="font-bold text-right">{{ formatRupiah(item.part_price) }}</p>
                                    </div>
                                    <div class="flex justify-between items-center gap-4 pt-3 mt-3 border-t border-gray-700/50">
                                        <p class="text-gray-400 text-xs">Biaya Jasa:</p>
                                        <p class="font-bold text-right">{{ formatRupiah(item.service_price) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- GRAND TOTAL KESELURUHAN -->
                            <div class="bg-blue-900/40 p-5 rounded-xl border border-blue-800/50 mt-2">
                                <p class="text-blue-300 text-sm mb-1">Total Estimasi Keseluruhan:</p>
                                <p class="text-3xl font-bold text-green-400">{{ formatRupiah(result.grand_total) }}</p>
                            </div>
                            
                            <div class="mt-6 p-3 bg-gray-900/50 rounded-lg flex gap-3 border border-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    Harga di atas hanya estimasi awal berdasarkan analisis gejala AI dan dapat berubah sesuai pemeriksaan aktual mekanik.
                                </p>
                            </div>
                        </div>

                        <!-- JIKA BELUM ADA HASIL (EMPTY STATE) -->
                        <div v-else class="border-2 border-dashed border-gray-300 bg-white sm:rounded-2xl p-12 flex flex-col items-center justify-center text-center min-h-[500px] sticky top-6">
                            <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Diagnosis</h3>
                            <p class="text-gray-500 text-base max-w-sm leading-relaxed">
                                Silakan pilih tipe motor dan centang gejala di samping untuk mendapatkan estimasi biaya perbaikan dari AI.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>