<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: { type: String, default: '' },
    size: { type: String, default: 'md' },
});

const initials = computed(() => {
    const parts = (props.name || '').trim().split(/\s+/).filter(Boolean);
    return (parts.slice(0, 2).map(p => p[0]).join('') || 'NA').toUpperCase();
});
const colorIdx = computed(() => (props.name || '').split('').reduce((a, c) => a + c.charCodeAt(0), 0) % 5);
const colors = [
    'from-brand-500 to-violet-500',
    'from-violet-500 to-pink-500',
    'from-cyan-500 to-blue-500',
    'from-emerald-500 to-cyan-500',
    'from-amber-500 to-orange-500',
];
const sizeClass = computed(() => ({
    sm: 'h-7 w-7 text-[10px]',
    md: 'h-9 w-9 text-xs',
    lg: 'h-12 w-12 text-sm',
}[props.size]));
</script>

<template>
    <span :class="['grid place-items-center rounded-full text-white font-semibold bg-gradient-to-br shrink-0', sizeClass, colors[colorIdx]]">
        {{ initials }}
    </span>
</template>
