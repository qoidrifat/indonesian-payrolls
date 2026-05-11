<script setup>
defineProps({
    title: String,
    description: String,
    icon: { type: Object, default: null },
});
</script>

<template>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="flex items-start gap-3">
            <span v-if="icon" class="hidden sm:grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 via-violet-500 to-cyan-500 text-white shadow-glow shrink-0">
                <component :is="icon" class="h-5 w-5" />
            </span>
            <div>
                <h1 class="text-2xl font-bold tracking-tight">{{ title }}</h1>
                <p v-if="description" class="text-sm text-surface-500 dark:text-surface-400 mt-1 max-w-2xl">{{ description }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
