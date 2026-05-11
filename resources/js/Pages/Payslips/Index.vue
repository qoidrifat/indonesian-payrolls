<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { DocumentTextIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline';
import { fmtIdr } from '@/lib/format';

defineProps({ payrolls: Array });
</script>

<template>
    <Head title="Payslips" />
    <AuthenticatedLayout>
        <PageHeader
            title="Payslips"
            description="Beautiful, branded PDF payslips ready to share with your team."
            :icon="DocumentTextIcon"
        />

        <div v-if="payrolls.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div v-for="p in payrolls" :key="p.id" class="card p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-surface-500">{{ p.period }}</p>
                        <p class="font-semibold mt-1">{{ p.code }}</p>
                    </div>
                    <StatusBadge :status="p.status" />
                </div>
                <p class="mt-3 text-2xl font-bold gradient-text">{{ fmtIdr(p.total_net) }}</p>
                <p class="text-xs text-surface-500">{{ p.employee_count }} payslips ready</p>
                <Link :href="route('payrolls.show', p.id)" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-300 hover:underline">
                    Open payroll <ArrowTopRightOnSquareIcon class="h-3.5 w-3.5" />
                </Link>
            </div>
        </div>
        <div v-else class="card">
            <EmptyState
                title="No payslips yet"
                description="Run a payroll cycle first to generate payslips."
                :icon="DocumentTextIcon"
            />
        </div>
    </AuthenticatedLayout>
</template>
