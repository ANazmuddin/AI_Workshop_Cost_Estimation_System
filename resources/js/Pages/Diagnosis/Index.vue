<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// MENERIMA PROPS DARI CONTROLLER
const props = defineProps({
    result: Object,
    errors: Object,
    motorTypes: Array // Menerima daftar tipe motor dari database
});

// INISIALISASI FORM
const form = useForm({
    // Jadikan motor urutan pertama sebagai nilai default, jika tidak ada biarkan kosong
    motor_type: props.motorTypes && props.motorTypes.length > 0 ? props.motorTypes[0] : '',
    symptoms: []
});

// DATA GEJALA KELUHAN
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
        name: 'Area Kelistrikan & Injeksi',
        symptoms: [
            { id: 'SYM-008', label: 'Lampu utama sering redup / mati' },
            { id: 'SYM-009', label: 'Klakson bersuara pelan / mati' },
            { id: 'SYM-010', label: 'Indikator mesin (Check Engine) menyala' },
            { id: 'SYM-011', label: 'Starter tangan tidak berfungsi (bunyi cetek-cetek)' },
        ]
    },
    {
        name: 'Area Pengereman',
        symptoms: [
            { id: 'SYM-006', label: 'Rem terasa kurang pakem / blong' },
            { id: 'SYM-007', label: 'Terdengar suara gesekan saat mengerem' },
        ]
    }
]);

// FUNGSI SUBMIT FORM
const submitDiagnosis = () => {
    form.post(route('diagnosis.calculate'), {
        preserveScroll: true // Mencegah layar scroll ke atas otomatis setelah loading
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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cek Estimasi Biaya Bengkel</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- KOLOM KIRI: FORM KELUHAN & MOTOR -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Pilih Gejala Kerusakan</h3>
                        <p class="mt-1 text-sm text-gray-600">Centang keluhan yang paling sesuai dengan kondisi sepeda motor.</p>
                    </div>

                    <!-- Alert Error Jika AI Microservice Mati / Gagal -->
                    <div v-if="errors && errors.message" class="mb-4 p-4 text-red-700 bg-red-100 rounded-lg">
                        {{ errors.message }}
                    </div>

                    <form @submit.prevent="submitDiagnosis">
                        
                        <!-- DROPDOWN TIPE MOTOR -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label for="motor_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Sepeda Motor</label>
                            <select 
                                id="motor_type" 
                                v-model="form.motor_type"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                            >
                                <option v-for="type in motorTypes" :key="type" :value="type">
                                    {{ type }}
                                </option>
                            </select>
                            <p class="mt-2 text-xs text-gray-500">Estimasi harga suku cadang dan jasa akan menyesuaikan dengan tipe motor yang dipilih.</p>
                        </div>

                        <!-- LOOPING KATEGORI & CHECKBOX GEJALA -->
                        <div v-for="category in categories" :key="category.name" class="mb-6">
                            <h4 class="font-semibold text-gray-700 border-b pb-2 mb-3">{{ category.name }}</h4>
                            <div class="space-y-2">
                                <div v-for="symptom in category.symptoms" :key="symptom.id" class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input 
                                            :id="symptom.id" 
                                            type="checkbox" 
                                            :value="symptom.id"
                                            v-model="form.symptoms"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                        >
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label :for="symptom.id" class="font-medium text-gray-700 cursor-pointer">{{ symptom.label }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL SUBMIT -->
                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200">
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-blue-600 rounded-md font-semibold text-white uppercase text-sm hover:bg-blue-700 transition ease-in-out duration-150"
                                :disabled="form.processing || form.symptoms.length === 0"
                                :class="{ 'opacity-50 cursor-not-allowed': form.symptoms.length === 0 || form.processing }"
                            >
                                <span v-if="form.processing">Memproses AI...</span>
                                <span v-else>Cek Estimasi</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- KOLOM KANAN: NOTA HASIL DIAGNOSIS AI -->
                <div v-if="result" class="md:col-span-1">
                    <div class="bg-gray-800 text-white shadow-sm sm:rounded-lg p-6 sticky top-6">
                        <h3 class="text-lg font-bold mb-4 border-b border-gray-600 pb-2">Hasil Analisis AI</h3>
                        
                        <div class="mb-4">
                            <p class="text-gray-400 text-sm">Prediksi Kerusakan:</p>
                            <p class="text-xl font-bold text-red-400">{{ result.diagnosis }}</p>
                        </div>

                        <div class="space-y-3 text-sm border-b border-gray-600 pb-4 mb-4">
                            <div>
                                <p class="text-gray-400">Suku Cadang ({{ result.part_name }}):</p>
                                <p class="font-semibold">{{ formatRupiah(result.part_price) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400">Estimasi Biaya Jasa:</p>
                                <p class="font-semibold">{{ formatRupiah(result.service_price) }}</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-gray-400 text-sm">Total Estimasi:</p>
                            <p class="text-2xl font-bold text-green-400">{{ formatRupiah(result.total_price) }}</p>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-6">*Harga di atas hanya estimasi dan dapat berubah sesuai pemeriksaan aktual oleh mekanik.</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>