<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-10 text-left lg:text-center">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Selamat Datang Kembali</h2>
            <p class="text-base text-gray-500 mt-2">Silakan masuk ke akun Anda untuk melanjutkan.</p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                <input
                    id="email"
                    type="email"
                    class="block w-full pl-4 pr-4 py-3.5 text-base border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-xl bg-gray-50/50 transition-all"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@bengkel.com"
                />
                <div v-if="form.errors.email" class="text-red-500 text-xs mt-2">{{ form.errors.email }}</div>
            </div>

            <!-- Password -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Kata Sandi</label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors"
                    >
                        Lupa sandi?
                    </Link>
                </div>
                <input
                    id="password"
                    type="password"
                    class="block w-full pl-4 pr-4 py-3.5 text-base border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-xl bg-gray-50/50 transition-all"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-2">{{ form.errors.password }}</div>
            </div>

            <!-- Remember Me -->
            <div class="block">
                <label class="flex items-center cursor-pointer group w-max">
                    <input type="checkbox" name="remember" v-model="form.remember" class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer transition-colors" />
                    <span class="ms-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full flex justify-center items-center gap-2 px-4 py-4 bg-blue-600 rounded-xl font-bold text-white text-base hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all duration-200"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Masuk ke Akun</span>
            </button>

            <!-- Register Link -->
            <p class="text-center text-sm text-gray-600 pt-4">
                Belum punya akun?
                <Link :href="route('register')" class="font-bold text-blue-600 hover:text-blue-500 transition-colors">Daftar sekarang</Link>
            </p>
        </form>
    </GuestLayout>
</template>