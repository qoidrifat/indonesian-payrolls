<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Avatar from '@/Components/Avatar.vue';
import {
    BanknotesIcon, PlayIcon, CheckCircleIcon, ArrowLeftIcon,
    ArrowDownTrayIcon, DocumentTextIcon, CurrencyDollarIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { fmtIdr, fmtDate } from '@/lib/format';

const props = defineProps({ payroll: Object, totals: Object });

const canRun = computed(() => ['draft', 'calculated'].includes(props.payroll.status));
const canApprove = computed(() => props.payroll.status === 'calculated');
const canPay = computed(() => props.payroll.status === 'approved');
const canDelete = computed(() => props.payroll.status !== 'paid');

function run() {
    if (!confirm('Run payroll calculation? Existing items will be replaced.')) return;
    router.post(route('payrolls.run', props.payroll.id), {}, { preserveScroll: true });
}
function approve() { router.post(route('payrolls.approve', props.payroll.id), {}, { preserveScroll: true }); }
function pay() { router.post(route('payrolls.pay', props.payroll.id), {}, { preserveScroll: true }); }
function generate() { router.post(route('payslips.generate', props.payroll.id), {}, { preserveScroll: true }); }
function destroy() {
    if (!confirm('Delete payroll?')) return;
    router.delete(route('payrolls.destroy', props.payroll.id));
}
</script>

<template>
    <Head :title="payroll.code" />
    <AuthenticatedLayout>
        <PageHeader :title="payroll.code" :description="'Period: ' + fmtDate(payroll.period_start) + ' – ' + fmtDate(payroll.period_end)" :icon="BanknotesIcon">
            <template #actions>
                <Link :href="route('payrolls.index')" class="btn-ghost">
                    <ArrowLeftIcon class="h-4 w-4" /> Back
                </Link>
                <button v-if="canRun" @click="run" class="btn-secondary">
                    <PlayIcon class="h-4 w-4" /> Run calculation
                </button>
                <button v-if="canApprove" @click="approve" class="btn-secondary">
                    <CheckCircleIcon class="h-4 w-4" /> Approve
                </button>
                <button v-if="canPay" @click="pay" class="btn-primary">
                    <CurrencyDollarIcon class="h-4 w-4" /> Mark paid
                </button>
            </template>
        </PageHeader>

        <div class="grid gap-4 md:grid-cols-4 mb-6">
            <div class="card p-4">
                <p class="text-xs text-surface-500 uppercase tracking-wider">Status</p>
                <div class="mt-2"><StatusBadge :status="payroll.status" /></div>
            </div>
            <div class="card p-4">
                <p class="text-xs text-surface-500 uppercase tracking-wider">Employees</p>
                <p class="text-2xl font-bold mt-1">{{ payroll.employee_count }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-surface-500 uppercase tracking-wider">Total gross</p>
                <p class="text-2xl font-bold mt-1">{{ fmtIdr(totals.gross) }}</p>
            </div>
            <div class="card p-4 relative overflow-hidden">
                <div class="absolute inset-0 bg-mesh-1 opacity-40"></div>
                <div class="relative">
                    <p class="text-xs text-surface-500 uppercase tracking-wider">Total net</p>
                    <p class="text-2xl font-bold mt-1 gradient-text">{{ fmtIdr(totals.net) }}</p>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-surface-200/60 dark:border-surface-800/60">
                <h3 class="text-sm font-semibold">Line items</h3>
                <div class="flex gap-2">
                    <button v-if="payroll.items?.length" @click="generate" class="btn-secondary">
                        <DocumentTextIcon class="h-4 w-4" /> Generate payslips
                    </button>
                    <Link :href="route('reports.payroll.excel', payroll.id)" class="btn-secondary">
                        <ArrowDownTrayIcon class="h-4 w-4" /> Excel
                    </Link>
                    <Link :href="route('reports.payroll.pdf', payroll.id)" target="_blank" class="btn-secondary">
                        <ArrowDownTrayIcon class="h-4 w-4" /> PDF
                    </Link>
                    <button v-if="canDelete" @click="destroy" class="btn-ghost text-rose-500">
                        <TrashIcon class="h-4 w-4" />
                    </button>
                </div>
            </div>
            <div v-if="payroll.items?.length" class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th class="text-right">Base</th>
                            <th class="text-right">Allow.</th>
                            <th class="text-right">OT/Bonus</th>
                            <th class="text-right">Gross</th>
                            <th class="text-right">BPJS</th>
                            <th class="text-right">PPh 21</th>
                            <th class="text-right">Net</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200/60 dark:divide-surface-800/60">
                        <tr v-for="i in payroll.items" :key="i.id">
                            <td>
                                <div class="flex items-center gap-2">
                                    <Avatar :name="i.employee?.name" size="sm" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium truncate">{{ i.employee?.name }}</p>
                                        <p class="text-xs text-surface-500 font-mono truncate">{{ i.employee?.employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">{{ fmtIdr(i.base_salary) }}</td>
                            <td class="text-right">{{ fmtIdr(i.allowances) }}</td>
                            <td class="text-right">{{ fmtIdr(Number(i.overtime_pay) + Number(i.bonus)) }}</td>
                            <td class="text-right font-medium">{{ fmtIdr(i.gross_salary) }}</td>
                            <td class="text-right">{{ fmtIdr(Number(i.bpjs_kesehatan_employee) + Number(i.bpjs_jht_employee) + Number(i.bpjs_jp_employee)) }}</td>
                            <td class="text-right">{{ fmtIdr(i.pph21) }}</td>
                            <td class="text-right font-bold gradient-text">{{ fmtIdr(i.net_salary) }}</td>
                            <td class="text-right">
                                <Link :href="route('payslips.show', i.id)" target="_blank" class="text-xs font-semibold text-brand-600 dark:text-brand-300 hover:underline">
                                    Payslip
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-10 text-center text-sm text-surface-500">
                No items yet. <button @click="run" class="text-brand-600 font-semibold hover:underline">Run calculation</button> to generate.
            </div>
        </div>
    </AuthenticatedLayout>
</template>
