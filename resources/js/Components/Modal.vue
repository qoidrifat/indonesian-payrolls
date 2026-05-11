<script setup>
import { onBeforeUnmount, onMounted, watch } from 'vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean,
    title: String,
    description: { type: String, default: '' },
    maxWidth: { type: String, default: '2xl' },
});
const emit = defineEmits(['close']);

function close() { emit('close'); }

watch(() => props.show, (v) => {
    document.body.style.overflow = v ? 'hidden' : '';
});

function onEsc(e) { if (e.key === 'Escape') close(); }
onMounted(() => window.addEventListener('keydown', onEsc));
onBeforeUnmount(() => window.removeEventListener('keydown', onEsc));

const maxWidthClass = {
    'sm': 'max-w-sm', 'md': 'max-w-md', 'lg': 'max-w-lg',
    'xl': 'max-w-xl', '2xl': 'max-w-2xl', '3xl': 'max-w-3xl', '4xl': 'max-w-4xl',
}[props.maxWidth];
</script>

<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-surface-900/40 backdrop-blur-sm" @click="close"></div>
            <div :class="['relative w-full card animate-scale-in', maxWidthClass]">
                <div class="flex items-start justify-between px-5 pt-5">
                    <div>
                        <h2 class="text-lg font-semibold">{{ title }}</h2>
                        <p v-if="description" class="text-sm text-surface-500 dark:text-surface-400 mt-0.5">{{ description }}</p>
                    </div>
                    <button @click="close" class="p-1.5 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>
                <div class="px-5 py-4">
                    <slot />
                </div>
                <div v-if="$slots.footer" class="px-5 py-4 border-t border-surface-200/60 dark:border-surface-800 flex items-center justify-end gap-2">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity .2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
