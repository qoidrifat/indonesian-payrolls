<script setup>
import { ref, watch } from 'vue';
import { CheckCircleIcon, ExclamationCircleIcon, InformationCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ flash: Object });
const visible = ref(false);
const message = ref('');
const variant = ref('success');
let timer;

watch(() => props.flash, (f) => {
    if (!f) return;
    if (f.success) { message.value = f.success; variant.value = 'success'; show(); }
    else if (f.error) { message.value = f.error; variant.value = 'error'; show(); }
    else if (f.info) { message.value = f.info; variant.value = 'info'; show(); }
}, { deep: true, immediate: true });

function show() {
    visible.value = true;
    clearTimeout(timer);
    timer = setTimeout(() => visible.value = false, 4000);
}

const icons = {
    success: CheckCircleIcon,
    error: ExclamationCircleIcon,
    info: InformationCircleIcon,
};
const colors = {
    success: 'from-emerald-500 to-emerald-600',
    error: 'from-rose-500 to-pink-600',
    info: 'from-brand-500 to-violet-500',
};
</script>

<template>
    <transition name="toast">
        <div v-if="visible" class="fixed top-5 right-5 z-[100] flex items-start gap-3 px-4 py-3 rounded-2xl bg-white/95 dark:bg-surface-900/95 border border-surface-200/60 dark:border-surface-800 shadow-glow max-w-sm">
            <span class="grid h-8 w-8 place-items-center rounded-xl text-white bg-gradient-to-br" :class="colors[variant]">
                <component :is="icons[variant]" class="h-5 w-5" />
            </span>
            <p class="flex-1 text-sm font-medium text-surface-800 dark:text-surface-100 pt-1">{{ message }}</p>
            <button @click="visible=false" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800">
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>
    </transition>
</template>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all .25s ease; }
.toast-enter-from { opacity: 0; transform: translate(20px, -10px); }
.toast-leave-to { opacity: 0; transform: translate(20px, 0); }
</style>
