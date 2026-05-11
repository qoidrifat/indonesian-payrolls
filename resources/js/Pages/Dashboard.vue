<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import ChartCard from '@/Components/ChartCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import {
    UsersIcon, BanknotesIcon, ClockIcon, ChartBarIcon,
    CalendarDaysIcon, SparklesIcon,
} from '@heroicons/vue/24/outline';
import { fmtIdr } from '@/lib/format';

const props = defineProps({
    stats: Object,
    monthly_trend: Array,
    department_mix: Array,
    attendance_today: Object,
    latest_payroll: Object,
    next_payroll_date: String,
    activity: Array,
});

const trendSeries = computed(() => [
    {
        name: 'Net Payout',
        data: props.monthly_trend.map(r => Number(r.net) || 0),
    },
    {
        name: 'Gross',
        data: props.monthly_trend.map(r => Number(r.gross) || 0),
    },
]);
const trendOptions = computed(() => ({
    xaxis: {
        categories: props.monthly_trend.map(r => `${r.y}-${String(r.m).padStart(2,'0')}`),
    },
    yaxis: { labels: { formatter: v => fmtIdr(v, true) } },
}));

const deptSeries = computed(() => props.department_mix.map(d => Number(d.value)));
const deptOptions = computed(() => ({
    labels: props.department_mix.map(d => d.label),
    legend: { position: 'bottom' },
    plotOptions: { pie: { donut: { size: '72%' } } },
}));

const attendanceSeries = computed(() => {
    const a = props.attendance_today || {};
    return [a.present || 0, a.remote || 0, a.leave || 0, a.sick || 0, a.absent || 0];
});
const attendanceOptions = computed(() => ({
    labels: ['Present', 'Remote', 'Leave', 'Sick', 'Absent'],
    colors: ['#10b981', '#6366f1', '#f59e0b', '#f97316', '#ef4444'],
    legend: { position: 'bottom' },
    plotOptions: { pie: { donut: { size: '70%' } } },
}));
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="grid gap-6">
            <!-- Hero -->
            <div class="card overflow-hidden relative p-6 sm:p-8">
                <div class="absolute inset-0 bg-mesh-1 opacity-50"></div>
                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-medium text-surface-500 dark:text-surface-400">
                            <SparklesIcon class="h-4 w-4 text-violet-500" />
                            <span>Welcome back</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight mt-2">
                            Your payroll, <span class="gradient-text">orchestrated</span>.
                        </h1>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mt-2 max-w-xl">
                            Real-time view of your team, attendance, and upcoming payouts — built for fast-moving Indonesian web teams.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <Link :href="route('payrolls.create')" class="btn-primary">
                            <BanknotesIcon class="h-4 w-4" /> New payroll
                        </Link>
                        <Link :href="route('employees.create')" class="btn-secondary">
                            <UsersIcon class="h-4 w-4" /> Add employee
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Stat row -->
            <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
                <StatCard
                    label="Active Employees"
                    :value="stats.employees_active"
                    :icon="UsersIcon"
                    :hint="`${stats.employees_new} new this month`"
                    accent="from-brand-500 to-violet-500"
                />
                <StatCard
                    label="Last Net Payout"
                    :value="fmtIdr(stats.this_month_net)"
                    :icon="BanknotesIcon"
                    :hint="`Gross: ${fmtIdr(stats.this_month_gross)}`"
                    accent="from-emerald-500 to-cyan-500"
                />
                <StatCard
                    label="Payrolls Paid"
                    :value="stats.payrolls_paid"
                    :icon="ChartBarIcon"
                    :hint="`${stats.payrolls_draft} pending`"
                    accent="from-pink-500 to-orange-500"
                />
                <StatCard
                    label="Next Pay Date"
                    :value="next_payroll_date"
                    :icon="CalendarDaysIcon"
                    hint="Auto-scheduled"
                    accent="from-cyan-500 to-blue-500"
                />
            </div>

            <!-- Charts -->
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <ChartCard
                        title="Payroll trend"
                        subtitle="Net vs gross over the last 12 months"
                        type="area"
                        :series="trendSeries"
                        :options="trendOptions"
                        :height="320"
                    />
                </div>
                <ChartCard
                    title="Department mix"
                    subtitle="Active team distribution"
                    type="donut"
                    :series="deptSeries"
                    :options="deptOptions"
                    :height="320"
                />
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <ChartCard
                    title="Today's attendance"
                    subtitle="Live snapshot of clock-ins"
                    type="donut"
                    :series="attendanceSeries"
                    :options="attendanceOptions"
                    :height="280"
                />
                <!-- Latest payroll -->
                <div class="card p-5 lg:col-span-2">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold tracking-tight">Latest payroll</h3>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Most recent processed cycle</p>
                        </div>
                        <Link v-if="latest_payroll" :href="route('payrolls.show', latest_payroll.id)" class="btn-ghost text-xs">View →</Link>
                    </div>
                    <div v-if="latest_payroll" class="grid sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-gradient-to-br from-brand-500/10 via-violet-500/10 to-cyan-500/10 p-5">
                            <p class="text-xs uppercase font-semibold tracking-wider text-surface-500">{{ latest_payroll.period }}</p>
                            <p class="text-3xl font-bold mt-1 gradient-text">{{ fmtIdr(latest_payroll.total_net) }}</p>
                            <div class="flex items-center gap-2 mt-3">
                                <StatusBadge :status="latest_payroll.status" />
                                <span class="text-xs text-surface-500">{{ latest_payroll.employee_count }} employees</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="h-9 w-9 grid place-items-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-300">
                                    <ClockIcon class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="text-xs text-surface-500">Run code</p>
                                    <p class="text-sm font-semibold">{{ latest_payroll.code }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-9 w-9 grid place-items-center rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-300">
                                    <UsersIcon class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="text-xs text-surface-500">Headcount</p>
                                    <p class="text-sm font-semibold">{{ latest_payroll.employee_count }} employees paid</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-9 w-9 grid place-items-center rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-300">
                                    <CalendarDaysIcon class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="text-xs text-surface-500">Next cycle</p>
                                    <p class="text-sm font-semibold">{{ next_payroll_date }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-surface-500 py-8 text-center">No payroll yet. Create one to get started.</p>
                </div>
            </div>

            <!-- Activity feed -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold tracking-tight">Recent activity</h3>
                        <p class="text-xs text-surface-500 dark:text-surface-400">Audit log of recent changes</p>
                    </div>
                </div>
                <ul class="divide-y divide-surface-200/60 dark:divide-surface-800/60">
                    <li v-for="a in activity" :key="a.id" class="flex items-center gap-3 py-3">
                        <span class="h-8 w-8 grid place-items-center rounded-full bg-gradient-to-br from-brand-500 to-violet-500 text-white text-[10px] font-bold">
                            {{ (a.causer || 'SY').split(' ').map(p=>p[0]).join('').slice(0,2).toUpperCase() }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium truncate">
                                <span class="text-surface-600 dark:text-surface-300">{{ a.causer || 'System' }}</span>
                                <span class="text-surface-400"> · </span>
                                <span>{{ a.description }}</span>
                                <span v-if="a.subject" class="chip-slate ml-2">{{ a.subject }}</span>
                            </p>
                        </div>
                        <span class="text-xs text-surface-400">{{ a.created_at }}</span>
                    </li>
                    <li v-if="!activity.length" class="py-6 text-center text-sm text-surface-500">No activity yet.</li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
