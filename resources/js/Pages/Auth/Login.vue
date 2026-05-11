<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({ canResetPassword: Boolean, status: String });

const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <GuestLayout>
        <Head title="Sign in" />

        <h1 class="text-2xl font-bold tracking-tight">Welcome back</h1>
        <p class="text-sm text-surface-500 mt-1">Sign in to your Aurex account.</p>

        <div v-if="status" class="mt-4 chip-emerald">{{ status }}</div>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <label class="block">
                <span class="label mb-1">Email</span>
                <input v-model="form.email" type="email" autofocus required class="input" />
                <span v-if="form.errors.email" class="text-xs text-rose-500 mt-1 block">{{ form.errors.email }}</span>
            </label>
            <label class="block">
                <div class="flex items-center justify-between mb-1">
                    <span class="label">Password</span>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs text-brand-600 dark:text-brand-300 hover:underline">Forgot?</Link>
                </div>
                <input v-model="form.password" type="password" required class="input" />
                <span v-if="form.errors.password" class="text-xs text-rose-500 mt-1 block">{{ form.errors.password }}</span>
            </label>
            <label class="inline-flex items-center gap-2 text-sm">
                <input v-model="form.remember" type="checkbox" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500" />
                Remember me
            </label>
            <button type="submit" :disabled="form.processing" class="btn-primary w-full justify-center">
                Sign in
            </button>
        </form>

        <p class="text-sm text-surface-500 mt-6">
            Don't have an account?
            <Link :href="route('register')" class="text-brand-600 dark:text-brand-300 font-medium hover:underline">Create one</Link>
        </p>
    </GuestLayout>
</template>
