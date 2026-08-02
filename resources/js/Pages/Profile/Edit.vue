<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengaturan Profil</h2>
            </div>
        </template>

        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Informasi Profil -->
                <div class="p-6 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Ubah Kata Sandi -->
                <div class="p-6 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- Hapus Akun -->
                <div class="p-6 sm:p-8 bg-red-50 shadow-sm sm:rounded-2xl border border-red-100">
                    <DeleteUserForm class="max-w-xl" />
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>