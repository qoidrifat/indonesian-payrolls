<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({ meta: Object, links: Array });
</script>

<template>
    <div v-if="links && links.length > 3" class="flex flex-wrap items-center gap-1 mt-4">
        <template v-for="(link, i) in links" :key="i">
            <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                preserve-scroll
                :class="[
                    'rounded-lg px-3 py-1.5 text-sm transition',
                    link.active
                        ? 'bg-gradient-to-r from-brand-600 to-violet-600 text-white shadow-glow'
                        : 'text-surface-600 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800'
                ]"
            />
            <span
                v-else
                v-html="link.label"
                class="rounded-lg px-3 py-1.5 text-sm text-surface-400 cursor-not-allowed"
            />
        </template>
    </div>
</template>
