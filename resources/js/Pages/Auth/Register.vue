<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h2>
            <p class="text-sm text-gray-500 mt-2">Daftarkan diri Anda untuk menggunakan layanan AI.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input
                    id="name"
                    type="text"
                    class="block w-full pl-4 pr-4 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg bg-gray-50 transition-colors"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Budi Mekanik"
                />
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-2">{{ form.errors.name }}</div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                <input
                    id="email"
                    type="email"
                    class="block w-full pl-4 pr-4 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg bg-gray-50 transition-colors"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="budi@bengkel.com"
                />
                <div v-if="form.errors.email" class="text-red-500 text-xs mt-2">{{ form.errors.email }}</div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                <input
                    id="password"
                    type="password"
                    class="block w-full pl-4 pr-4 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg bg-gray-50 transition-colors"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-2">{{ form.errors.password }}</div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Kata Sandi</label>
                <input
                    id="password_confirmation"
                    type="password"
                    class="block w-full pl-4 pr-4 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg bg-gray-50 transition-colors"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                />
                <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-2">{{ form.errors.password_confirmation }}</div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full flex justify-center items-center gap-2 mt-2 px-4 py-3.5 bg-blue-600 rounded-lg font-bold text-white text-sm hover:bg-blue-700 transition-all duration-200"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Daftar Sekarang</span>
            </button>

            <!-- Login Link -->
            <p class="text-center text-sm text-gray-600 pt-2">
                Sudah punya akun?
                <Link :href="route('login')" class="font-bold text-blue-600 hover:text-blue-500 transition-colors">Masuk di sini</Link>
            </p>
        </form>
    </GuestLayout>
</template>