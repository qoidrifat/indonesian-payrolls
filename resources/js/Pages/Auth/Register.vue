<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const submit = () => form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <GuestLayout>
        <Head title="Create account" />

        <h1 class="text-2xl font-bold tracking-tight">Create your account</h1>
        <p class="text-sm text-surface-500 mt-1">Onboard your team in minutes.</p>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <label class="block">
                <span class="label mb-1">Full name</span>
                <input v-model="form.name" required autofocus class="input" />
                <span v-if="form.errors.name" class="text-xs text-rose-500 mt-1 block">{{ form.errors.name }}</span>
            </label>
            <label class="block">
                <span class="label mb-1">Email</span>
                <input v-model="form.email" type="email" required class="input" />
                <span v-if="form.errors.email" class="text-xs text-rose-500 mt-1 block">{{ form.errors.email }}</span>
            </label>
            <label class="block">
                <span class="label mb-1">Password</span>
                <input v-model="form.password" type="password" required class="input" />
                <span v-if="form.errors.password" class="text-xs text-rose-500 mt-1 block">{{ form.errors.password }}</span>
            </label>
            <label class="block">
                <span class="label mb-1">Confirm password</span>
                <input v-model="form.password_confirmation" type="password" required class="input" />
            </label>
            <button type="submit" :disabled="form.processing" class="btn-primary w-full justify-center">
                Create account
            </button>
        </form>

        <p class="text-sm text-surface-500 mt-6">
            Already have an account?
            <Link :href="route('login')" class="text-brand-600 dark:text-brand-300 font-medium hover:underline">Sign in</Link>
        </p>
    </GuestLayout>
</template>
