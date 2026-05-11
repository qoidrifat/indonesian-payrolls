<script setup>
import { ArrowTrendingUpIcon, ArrowTrendingDownIcon } from '@heroicons/vue/24/outline';

defineProps({
    label: String,
    value: [String, Number],
    icon: Object,
    accent: { type: String, default: 'from-brand-500 to-violet-500' },
    delta: { type: [String, Number, null], default: null },
    deltaDirection: { type: String, default: 'up' },
    hint: { type: String, default: '' },
});
</script>

<template>
    <div class="card p-5 relative overflow-hidden group hover:-translate-y-0.5 transition">
        <div class="absolute -top-10 -right-10 h-32 w-32 rounded-full bg-gradient-to-br opacity-20 group-hover:opacity-30 transition" :class="accent"></div>
        <div class="flex items-start justify-between relative">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">{{ label }}</p>
                <p class="text-3xl font-bold tracking-tight">{{ value }}</p>
                <p v-if="hint" class="text-xs text-surface-500 dark:text-surface-400">{{ hint }}</p>
                <div v-if="delta !== null" class="flex items-center gap-1 text-xs font-medium" :class="deltaDirection === 'up' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                    <component :is="deltaDirection === 'up' ? ArrowTrendingUpIcon : ArrowTrendingDownIcon" class="h-3.5 w-3.5" />
                    <span>{{ delta }}</span>
                </div>
            </div>
            <span class="grid h-11 w-11 place-items-center rounded-2xl text-white bg-gradient-to-br shadow-glow" :class="accent">
                <component :is="icon" class="h-5 w-5" />
            </span>
        </div>
    </div>
</template>
