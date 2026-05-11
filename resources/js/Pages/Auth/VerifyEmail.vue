<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({ status: String });

const form = useForm({});
const submit = () => form.post(route('verification.send'));
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verify email" />

        <h1 class="text-2xl font-bold tracking-tight">Verify your email</h1>
        <p class="text-sm text-surface-500 mt-1">
            We sent a verification link to your inbox. Click it to continue. Lost it? Request a new one below.
        </p>

        <div v-if="verificationLinkSent" class="mt-4 chip-emerald">
            A new verification link has been sent.
        </div>

        <form @submit.prevent="submit" class="mt-6 space-y-3">
            <button :disabled="form.processing" class="btn-primary w-full justify-center">
                Resend verification email
            </button>

            <Link :href="route('logout')" method="post" as="button" class="btn-ghost w-full justify-center">
                Log out
            </Link>
        </form>
    </GuestLayout>
</template>
