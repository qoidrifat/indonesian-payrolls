<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({ email: String, token: String });

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});
const submit = () => form.post(route('password.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
});
</script>

<template>
    <GuestLayout>
        <Head title="Reset password" />

        <h1 class="text-2xl font-bold tracking-tight">Set a new password</h1>
        <p class="text-sm text-surface-500 mt-1">Choose something memorable but strong.</p>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <label class="block">
                <span class="label mb-1">Email</span>
                <input v-model="form.email" type="email" required class="input" />
                <span v-if="form.errors.email" class="text-xs text-rose-500 mt-1 block">{{ form.errors.email }}</span>
            </label>
            <label class="block">
                <span class="label mb-1">New password</span>
                <input v-model="form.password" type="password" autofocus required class="input" />
                <span v-if="form.errors.password" class="text-xs text-rose-500 mt-1 block">{{ form.errors.password }}</span>
            </label>
            <label class="block">
                <span class="label mb-1">Confirm password</span>
                <input v-model="form.password_confirmation" type="password" required class="input" />
            </label>
            <button :disabled="form.processing" class="btn-primary w-full justify-center">
                Update password
            </button>
        </form>
    </GuestLayout>
</template>
