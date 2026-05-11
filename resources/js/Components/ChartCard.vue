<script setup>
import { computed, ref, onMounted } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    title: String,
    subtitle: { type: String, default: '' },
    series: { type: Array, required: true },
    options: { type: Object, default: () => ({}) },
    type: { type: String, default: 'area' },
    height: { type: Number, default: 300 },
});

const isDark = ref(false);
onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
    const obs = new MutationObserver(() => isDark.value = document.documentElement.classList.contains('dark'));
    obs.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

const baseOptions = computed(() => ({
    chart: {
        toolbar: { show: false },
        zoom: { enabled: false },
        background: 'transparent',
        fontFamily: 'Plus Jakarta Sans, Inter, sans-serif',
        animations: { enabled: true, easing: 'easeinout', speed: 600 },
    },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    grid: { borderColor: isDark.value ? '#27272a' : '#e4e4e7', strokeDashArray: 4 },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.55,
            opacityTo: 0.05,
            stops: [0, 90, 100],
        },
    },
    colors: ['#6366f1', '#22d3ee', '#a855f7', '#10b981', '#f97316'],
    tooltip: { theme: isDark.value ? 'dark' : 'light' },
    legend: { labels: { colors: isDark.value ? '#a1a1aa' : '#52525b' } },
    xaxis: {
        labels: { style: { colors: isDark.value ? '#a1a1aa' : '#52525b' } },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: { style: { colors: isDark.value ? '#a1a1aa' : '#52525b' } },
    },
    ...props.options,
}));
</script>

<template>
    <div class="card p-5">
        <div class="flex items-start justify-between mb-2">
            <div>
                <h3 class="text-sm font-semibold tracking-tight">{{ title }}</h3>
                <p v-if="subtitle" class="text-xs text-surface-500 dark:text-surface-400">{{ subtitle }}</p>
            </div>
            <slot name="actions" />
        </div>
        <VueApexCharts :type="type" :options="baseOptions" :series="series" :height="height" />
    </div>
</template>
