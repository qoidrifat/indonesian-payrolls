<script setup>
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { AdjustmentsHorizontalIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { fmtIdr } from '@/lib/format';

const props = defineProps({ components: Object, employees: Array, filters: Object });

const employeeId = ref(props.filters?.employee_id ?? '');
let t;
watch(employeeId, () => {
    clearTimeout(t);
    t = setTimeout(() => router.get(route('salary-components.index'), { employee_id: employeeId.value }, { preserveState: true, replace: true }), 200);
});

const showModal = ref(false);
const form = useForm({
    employee_id: '',
    type: 'allowance',
    name: '',
    amount: 0,
    is_taxable: true,
    is_recurring: true,
    notes: '',
});
function submit() {
    form.post(route('salary-components.store'), {
        preserveScroll: true,
        onSuccess: () => { showModal.value = false; form.reset(); },
    });
}
function destroy(id) {
    if (!confirm('Remove this component?')) return;
    router.delete(route('salary-components.destroy', id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Salary configuration" />
    <AuthenticatedLayout>
        <PageHeader
            title="Salary configuration"
            description="Attach recurring allowances, deductions and bonuses to your team. These auto-apply on every payroll run."
            :icon="AdjustmentsHorizontalIcon"
        >
            <template #actions>
                <button @click="showModal=true" class="btn-primary">
                    <PlusIcon class="h-4 w-4" /> Add component
                </button>
            </template>
        </PageHeader>

        <div class="card p-4 mb-4">
            <select v-model="employeeId" class="input sm:max-w-md">
                <option value="">All employees</option>
                <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }} — {{ fmtIdr(e.base_salary) }}</option>
            </select>
        </div>

        <div class="card overflow-hidden">
            <div v-if="components.data.length" class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th class="text-right">Amount</th>
                            <th>Tax</th>
                            <th>Recurring</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200/60 dark:divide-surface-800/60">
                        <tr v-for="c in components.data" :key="c.id">
                            <td>
                                <p class="font-medium text-sm">{{ c.employee?.name }}</p>
                                <p class="text-xs font-mono text-surface-500">{{ c.employee?.employee_code }}</p>
                            </td>
                            <td><StatusBadge :status="c.type" /></td>
                            <td>{{ c.name }}</td>
                            <td class="text-right font-semibold">{{ fmtIdr(c.amount) }}</td>
                            <td>{{ c.is_taxable ? 'Taxable' : 'Non-tax' }}</td>
                            <td>{{ c.is_recurring ? 'Yes' : 'One-off' }}</td>
                            <td class="text-right">
                                <button @click="destroy(c.id)" class="p-1.5 rounded-lg hover:bg-rose-500/10 text-rose-500">
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState
                v-else
                title="No salary components"
                description="Add allowances, deductions, or bonuses to dial in your payroll math."
                :icon="AdjustmentsHorizontalIcon"
            >
                <button @click="showModal=true" class="btn-primary">
                    <PlusIcon class="h-4 w-4" /> Add component
                </button>
            </EmptyState>
        </div>

        <Pagination :links="components.links" />

        <Modal :show="showModal" title="New salary component" @close="showModal=false">
            <form @submit.prevent="submit" id="sc-form" class="grid sm:grid-cols-2 gap-3">
                <label class="block sm:col-span-2">
                    <span class="label mb-1">Employee</span>
                    <select v-model="form.employee_id" class="input" required>
                        <option value="">Select…</option>
                        <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }}</option>
                    </select>
                </label>
                <label class="block">
                    <span class="label mb-1">Type</span>
                    <select v-model="form.type" class="input">
                        <option value="allowance">Allowance</option>
                        <option value="deduction">Deduction</option>
                        <option value="bonus">Bonus</option>
                    </select>
                </label>
                <label class="block">
                    <span class="label mb-1">Amount (IDR)</span>
                    <input v-model.number="form.amount" type="number" min="0" class="input" required />
                </label>
                <label class="block sm:col-span-2">
                    <span class="label mb-1">Name</span>
                    <input v-model="form.name" class="input" required placeholder="e.g. Transport, Internet, Performance bonus" />
                </label>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input v-model="form.is_taxable" type="checkbox" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500" />
                    Taxable
                </label>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input v-model="form.is_recurring" type="checkbox" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500" />
                    Recurring every period
                </label>
                <label class="block sm:col-span-2">
                    <span class="label mb-1">Notes</span>
                    <textarea v-model="form.notes" rows="2" class="input"></textarea>
                </label>
            </form>
            <template #footer>
                <button @click="showModal=false" type="button" class="btn-ghost">Cancel</button>
                <button form="sc-form" type="submit" :disabled="form.processing" class="btn-primary">Save</button>
            </template>
        </Modal>
    </AuthenticatedLayout>
</template>
