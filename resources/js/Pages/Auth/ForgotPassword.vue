<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({ status: String });

const form = useForm({ email: '' });
const submit = () => form.post(route('password.email'));
</script>

<template>
    <GuestLayout>
        <Head title="Forgot password" />

        <h1 class="text-2xl font-bold tracking-tight">Forgot your password?</h1>
        <p class="text-sm text-surface-500 mt-1">
            No problem — enter your email and we'll send a reset link.
        </p>

        <div v-if="status" class="mt-4 chip-emerald">{{ status }}</div>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <label class="block">
                <span class="label mb-1">Email</span>
                <input v-model="form.email" type="email" required autofocus class="input" />
                <span v-if="form.errors.email" class="text-xs text-rose-500 mt-1 block">{{ form.errors.email }}</span>
            </label>
            <button :disabled="form.processing" class="btn-primary w-full justify-center">
                Send reset link
            </button>
        </form>
    </GuestLayout>
</template>
