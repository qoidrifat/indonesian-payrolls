<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ChartCard from '@/Components/ChartCard.vue';
import { ChartBarIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import { fmtIdr } from '@/lib/format';

const props = defineProps({ payrolls: Array, tax_by_month: Array, employees: Array });

const year = ref(new Date().getFullYear());
const month = ref(new Date().toISOString().slice(0,7));

const taxSeries = computed(() => [{
    name: 'PPh 21',
    data: props.tax_by_month.map(r => Number(r.pph21) || 0),
}]);
const taxOptions = computed(() => ({
    xaxis: { categories: props.tax_by_month.map(r => `${r.y}-${String(r.m).padStart(2,'0')}`) },
    yaxis: { labels: { formatter: v => fmtIdr(v, true) } },
    colors: ['#a855f7'],
}));
</script>

<template>
    <Head title="Reports" />
    <AuthenticatedLayout>
        <PageHeader title="Reports" description="Export payroll, tax, and attendance summaries." :icon="ChartBarIcon" />

        <div class="grid gap-6 lg:grid-cols-2 mb-6">
            <ChartCard title="PPh 21 by month" subtitle="Withholding tax trend" :series="taxSeries" :options="taxOptions" type="bar" :height="300" />
            <div class="card p-5">
                <h3 class="text-sm font-semibold mb-3">Quick exports</h3>
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <label class="text-sm text-surface-600 dark:text-surface-300 w-32 shrink-0">Tax summary year</label>
                        <input v-model.number="year" type="number" min="2020" max="2099" class="input sm:w-32" />
                        <a :href="route('reports.tax.excel', { year })" class="btn-secondary">
                            <ArrowDownTrayIcon class="h-4 w-4" /> Excel
                        </a>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <label class="text-sm text-surface-600 dark:text-surface-300 w-32 shrink-0">Attendance month</label>
                        <input v-model="month" type="month" class="input sm:w-44" />
                        <a :href="route('reports.attendance.excel', { month })" class="btn-secondary">
                            <ArrowDownTrayIcon class="h-4 w-4" /> Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <h3 class="text-sm font-semibold px-5 pt-5">Monthly payrolls</h3>
            <div class="overflow-x-auto mt-3">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th class="text-right">Gross</th>
                            <th class="text-right">Net</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200/60 dark:divide-surface-800/60">
                        <tr v-for="p in payrolls" :key="p.id">
                            <td class="font-medium">{{ p.period }}</td>
                            <td class="font-mono text-xs">{{ p.code }}</td>
                            <td>{{ p.status }}</td>
                            <td class="text-right">{{ fmtIdr(p.total_gross) }}</td>
                            <td class="text-right font-semibold">{{ fmtIdr(p.total_net) }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    <Link :href="route('reports.payroll.excel', p.id)" class="btn-ghost text-xs">Excel</Link>
                                    <Link :href="route('reports.payroll.pdf', p.id)" target="_blank" class="btn-ghost text-xs">PDF</Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden mt-6">
            <h3 class="text-sm font-semibold px-5 pt-5">Employee salary history</h3>
            <ul class="mt-3 divide-y divide-surface-200/60 dark:divide-surface-800/60">
                <li v-for="e in employees" :key="e.id" class="flex items-center justify-between px-5 py-3">
                    <div>
                        <p class="text-sm font-medium">{{ e.name }}</p>
                        <p class="text-xs text-surface-500">{{ e.position }}</p>
                    </div>
                    <Link :href="route('reports.employee.history', e.id)" target="_blank" class="btn-ghost text-xs">
                        <ArrowDownTrayIcon class="h-4 w-4" /> Download PDF
                    </Link>
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>
