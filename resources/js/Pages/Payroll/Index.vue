<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import { BanknotesIcon, PlusIcon, ArrowRightIcon } from '@heroicons/vue/24/outline';
import { fmtIdr, fmtDate } from '@/lib/format';

defineProps({ payrolls: Object });
</script>

<template>
    <Head title="Payrolls" />
    <AuthenticatedLayout>
        <PageHeader
            title="Payrolls"
            description="Monthly cycles, end-to-end. Calculate, approve, and pay — all in one flow."
            :icon="BanknotesIcon"
        >
            <template #actions>
                <Link :href="route('payrolls.create')" class="btn-primary">
                    <PlusIcon class="h-4 w-4" /> New payroll
                </Link>
            </template>
        </PageHeader>

        <div v-if="payrolls.data.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Link
                v-for="p in payrolls.data"
                :key="p.id"
                :href="route('payrolls.show', p.id)"
                class="card p-5 hover:-translate-y-0.5 transition group"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase font-semibold tracking-wider text-surface-500">{{ p.period_year }}-{{ String(p.period_month).padStart(2, '0') }}</p>
                        <h3 class="text-lg font-semibold mt-1 group-hover:gradient-text transition">{{ p.code }}</h3>
                    </div>
                    <StatusBadge :status="p.status" />
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-surface-500">Total net</p>
                        <p class="font-semibold">{{ fmtIdr(p.total_net) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-surface-500">Employees</p>
                        <p class="font-semibold">{{ p.items_count }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-surface-500">Period</p>
                        <p class="text-xs">{{ fmtDate(p.period_start) }} – {{ fmtDate(p.period_end) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-surface-500">Pay date</p>
                        <p class="text-xs">{{ p.pay_date ? fmtDate(p.pay_date) : '—' }}</p>
                    </div>
                </div>
                <div class="mt-4 inline-flex items-center gap-1 text-xs text-brand-600 dark:text-brand-300 font-semibold">
                    Open <ArrowRightIcon class="h-3.5 w-3.5" />
                </div>
            </Link>
        </div>
        <div v-else class="card">
            <EmptyState
                title="No payroll runs yet"
                description="Create your first monthly payroll cycle."
                :icon="BanknotesIcon"
            >
                <Link :href="route('payrolls.create')" class="btn-primary">
                    <PlusIcon class="h-4 w-4" /> New payroll
                </Link>
            </EmptyState>
        </div>

        <Pagination :links="payrolls.links" />
    </AuthenticatedLayout>
</template>
