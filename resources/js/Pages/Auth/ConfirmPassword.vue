<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({ password: '' });
const submit = () => form.post(route('password.confirm'), {
    onFinish: () => form.reset(),
});
</script>

<template>
    <GuestLayout>
        <Head title="Confirm password" />

        <h1 class="text-2xl font-bold tracking-tight">Confirm your password</h1>
        <p class="text-sm text-surface-500 mt-1">This is a secure area — please confirm your password to continue.</p>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <label class="block">
                <span class="label mb-1">Password</span>
                <input v-model="form.password" type="password" autofocus required class="input" />
                <span v-if="form.errors.password" class="text-xs text-rose-500 mt-1 block">{{ form.errors.password }}</span>
            </label>
            <button :disabled="form.processing" class="btn-primary w-full justify-center">
                Confirm
            </button>
        </form>
    </GuestLayout>
</template>
