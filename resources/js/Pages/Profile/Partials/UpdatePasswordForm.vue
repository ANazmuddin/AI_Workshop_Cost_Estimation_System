<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-gray-900">Perbarui Kata Sandi</h2>
            <p class="mt-1 text-sm text-gray-500">
                Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <!-- Sandi Saat Ini -->
            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi Saat Ini</label>
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    type="password"
                    class="block w-full pl-4 pr-4 py-3 text-base border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-xl bg-gray-50/50 transition-all"
                    v-model="form.current_password"
                    autocomplete="current-password"
                />
                <div v-if="form.errors.current_password" class="text-red-500 text-xs mt-2">{{ form.errors.current_password }}</div>
            </div>

            <!-- Sandi Baru -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi Baru</label>
                <input
                    id="password"
                    ref="passwordInput"
                    type="password"
                    class="block w-full pl-4 pr-4 py-3 text-base border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-xl bg-gray-50/50 transition-all"
                    v-model="form.password"
                    autocomplete="new-password"
                />
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-2">{{ form.errors.password }}</div>
            </div>

            <!-- Konfirmasi Sandi -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                <input
                    id="password_confirmation"
                    type="password"
                    class="block w-full pl-4 pr-4 py-3 text-base border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-xl bg-gray-50/50 transition-all"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                />
                <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-2">{{ form.errors.password_confirmation }}</div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center px-6 py-3 bg-gray-800 rounded-xl font-bold text-white text-sm hover:bg-gray-900 shadow-md transition-all duration-200"
                    :disabled="form.processing"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                >
                    Perbarui Sandi
                </button>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-bold bg-green-50 px-3 py-1.5 rounded-lg">Berhasil diubah.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>