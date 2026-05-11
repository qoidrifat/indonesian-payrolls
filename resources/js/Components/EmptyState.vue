<script setup>
defineProps({
    title: String,
    description: String,
    icon: { type: Object, default: null },
});
</script>

<template>
    <div class="text-center py-14 px-6">
        <div class="mx-auto h-14 w-14 grid place-items-center rounded-2xl bg-gradient-to-br from-brand-500/15 via-violet-500/15 to-cyan-500/15 text-brand-600 dark:text-brand-300">
            <component v-if="icon" :is="icon" class="h-7 w-7" />
        </div>
        <h3 class="mt-4 text-base font-semibold">{{ title }}</h3>
        <p v-if="description" class="mt-1 text-sm text-surface-500 dark:text-surface-400 max-w-md mx-auto">{{ description }}</p>
        <div class="mt-5"><slot /></div>
    </div>
</template>
