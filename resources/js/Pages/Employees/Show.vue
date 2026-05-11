<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Avatar from '@/Components/Avatar.vue';
import { UserIcon, PencilSquareIcon, TrashIcon, ArrowDownTrayIcon, BanknotesIcon } from '@heroicons/vue/24/outline';
import { fmtIdr, fmtDate } from '@/lib/format';

const props = defineProps({ employee: Object });

function destroy() {
    if (!confirm(`Archive ${props.employee.name}?`)) return;
    router.delete(route('employees.destroy', props.employee.id));
}
</script>

<template>
    <Head :title="employee.name" />
    <AuthenticatedLayout>
        <PageHeader :title="employee.name" :description="employee.position + (employee.department ? ' · ' + employee.department : '')">
            <template #actions>
                <Link :href="route('reports.employee.history', employee.id)" class="btn-secondary" target="_blank">
                    <ArrowDownTrayIcon class="h-4 w-4" /> Salary history
                </Link>
                <Link :href="route('employees.edit', employee.id)" class="btn-secondary">
                    <PencilSquareIcon class="h-4 w-4" /> Edit
                </Link>
                <button @click="destroy" class="btn-danger">
                    <TrashIcon class="h-4 w-4" /> Archive
                </button>
            </template>
        </PageHeader>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card p-5 lg:col-span-1">
                <div class="flex items-center gap-3">
                    <Avatar :name="employee.name" size="lg" />
                    <div class="min-w-0">
                        <p class="font-semibold truncate">{{ employee.name }}</p>
                        <p class="text-xs text-surface-500 font-mono">{{ employee.employee_code }}</p>
                    </div>
                </div>
                <dl class="mt-5 grid gap-3 text-sm">
                    <Row label="Email" :value="employee.email" />
                    <Row label="Phone" :value="employee.phone || '—'" />
                    <Row label="NIK" :value="employee.nik || '—'" mono />
                    <Row label="NPWP" :value="employee.npwp || 'No NPWP (+20% tax)'" mono :alert="!employee.npwp" />
                    <Row label="Status" >
                        <StatusBadge :status="employee.employment_status" />
                    </Row>
                    <Row label="PTKP" :value="employee.marital_status" />
                    <Row label="Joined" :value="fmtDate(employee.join_date)" />
                </dl>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold">Compensation</h3>
                        <BanknotesIcon class="h-5 w-5 text-surface-400" />
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-brand-500/10 via-violet-500/10 to-cyan-500/10 p-5">
                        <p class="text-xs uppercase font-semibold tracking-wider text-surface-500">Base salary</p>
                        <p class="text-3xl font-bold mt-1 gradient-text">{{ fmtIdr(employee.base_salary) }}</p>
                        <p class="text-xs text-surface-500 mt-1">monthly</p>
                    </div>
                    <div class="mt-5 grid sm:grid-cols-2 gap-3 text-sm">
                        <Row label="Bank" :value="(employee.bank_name || '—') + (employee.bank_account_number ? ' · ' + employee.bank_account_number : '')" />
                        <Row label="Account name" :value="employee.bank_account_name || '—'" />
                        <Row label="BPJS Kesehatan" :value="employee.bpjs_kesehatan || '—'" mono />
                        <Row label="BPJS Ketenagakerjaan" :value="employee.bpjs_ketenagakerjaan || '—'" mono />
                    </div>

                    <div v-if="employee.salary_components?.length" class="mt-6">
                        <h4 class="text-sm font-semibold mb-3">Salary components</h4>
                        <div class="overflow-x-auto">
                            <table class="table-base">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th class="text-right">Amount</th>
                                        <th>Taxable</th>
                                        <th>Recurring</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-200/60 dark:divide-surface-800/60">
                                    <tr v-for="c in employee.salary_components" :key="c.id">
                                        <td><StatusBadge :status="c.type" /></td>
                                        <td>{{ c.name }}</td>
                                        <td class="text-right font-semibold">{{ fmtIdr(c.amount) }}</td>
                                        <td>{{ c.is_taxable ? 'Yes' : 'No' }}</td>
                                        <td>{{ c.is_recurring ? 'Yes' : 'No' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card p-5">
                    <h3 class="text-sm font-semibold mb-3">Payroll history</h3>
                    <div v-if="employee.payroll_items?.length" class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th class="text-right">Gross</th>
                                    <th class="text-right">PPh 21</th>
                                    <th class="text-right">Net</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200/60 dark:divide-surface-800/60">
                                <tr v-for="i in employee.payroll_items" :key="i.id">
                                    <td>{{ i.payroll?.code }}</td>
                                    <td class="text-right">{{ fmtIdr(i.gross_salary) }}</td>
                                    <td class="text-right">{{ fmtIdr(i.pph21) }}</td>
                                    <td class="text-right font-semibold">{{ fmtIdr(i.net_salary) }}</td>
                                    <td><StatusBadge :status="i.payroll?.status" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-sm text-surface-500 py-4 text-center">No payroll entries yet.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { defineComponent, h } from 'vue';
const Row = defineComponent({
    props: { label: String, value: [String, Number], mono: Boolean, alert: Boolean },
    setup(props, { slots }) {
        return () => h('div', { class: 'flex items-center justify-between gap-3' }, [
            h('dt', { class: 'text-xs text-surface-500 dark:text-surface-400' }, props.label),
            slots.default
                ? h('dd', {}, slots.default())
                : h('dd', { class: ['text-sm font-medium', props.mono && 'font-mono', props.alert && 'text-amber-600 dark:text-amber-400'] }, props.value),
        ]);
    },
});
export default { components: { Row } };
</script>
