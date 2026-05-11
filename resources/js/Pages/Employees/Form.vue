<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { UserPlusIcon, ArrowLeftIcon, CheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ employee: { type: Object, default: null } });

const isEdit = !!props.employee;
const form = useForm({
    employee_code: props.employee?.employee_code ?? '',
    name: props.employee?.name ?? '',
    email: props.employee?.email ?? '',
    phone: props.employee?.phone ?? '',
    nik: props.employee?.nik ?? '',
    npwp: props.employee?.npwp ?? '',
    bpjs_kesehatan: props.employee?.bpjs_kesehatan ?? '',
    bpjs_ketenagakerjaan: props.employee?.bpjs_ketenagakerjaan ?? '',
    bank_name: props.employee?.bank_name ?? '',
    bank_account_number: props.employee?.bank_account_number ?? '',
    bank_account_name: props.employee?.bank_account_name ?? '',
    position: props.employee?.position ?? '',
    department: props.employee?.department ?? '',
    employment_status: props.employee?.employment_status ?? 'permanent',
    marital_status: props.employee?.marital_status ?? 'TK',
    join_date: props.employee?.join_date?.slice(0,10) ?? new Date().toISOString().slice(0,10),
    end_date: props.employee?.end_date?.slice(0,10) ?? '',
    base_salary: props.employee?.base_salary ?? 0,
    is_active: props.employee?.is_active ?? true,
    address: props.employee?.address ?? '',
});

function submit() {
    if (isEdit) {
        form.put(route('employees.update', props.employee.id));
    } else {
        form.post(route('employees.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit employee' : 'New employee'" />
    <AuthenticatedLayout>
        <PageHeader
            :title="isEdit ? employee.name : 'New employee'"
            :description="isEdit ? 'Update employee profile and compensation.' : 'Add a team member and configure their compensation.'"
            :icon="UserPlusIcon"
        >
            <template #actions>
                <Link :href="route('employees.index')" class="btn-ghost">
                    <ArrowLeftIcon class="h-4 w-4" /> Back
                </Link>
            </template>
        </PageHeader>

        <form @submit.prevent="submit" class="grid gap-6">
            <div class="card p-5">
                <h3 class="text-sm font-semibold mb-4">Identity</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <Field label="Employee code"><input v-model="form.employee_code" class="input" required /></Field>
                    <Field label="Full name"><input v-model="form.name" class="input" required /></Field>
                    <Field label="Email"><input v-model="form.email" type="email" class="input" required /></Field>
                    <Field label="Phone"><input v-model="form.phone" class="input" /></Field>
                    <Field label="NIK (KTP)"><input v-model="form.nik" class="input" /></Field>
                    <Field label="NPWP"><input v-model="form.npwp" class="input" placeholder="Leave blank if none — adds 20% PPh penalty" /></Field>
                    <Field label="Address" class="sm:col-span-2">
                        <textarea v-model="form.address" class="input" rows="2"></textarea>
                    </Field>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="text-sm font-semibold mb-4">Employment</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <Field label="Position"><input v-model="form.position" class="input" required /></Field>
                    <Field label="Department"><input v-model="form.department" class="input" /></Field>
                    <Field label="Employment status">
                        <select v-model="form.employment_status" class="input">
                            <option value="permanent">Permanent</option>
                            <option value="contract">Contract</option>
                            <option value="probation">Probation</option>
                            <option value="intern">Intern</option>
                        </select>
                    </Field>
                    <Field label="PTKP status">
                        <select v-model="form.marital_status" class="input">
                            <option value="TK">TK (single)</option>
                            <option value="K0">K/0 (married, 0 deps)</option>
                            <option value="K1">K/1 (married, 1 dep)</option>
                            <option value="K2">K/2 (married, 2 deps)</option>
                            <option value="K3">K/3 (married, 3 deps)</option>
                        </select>
                    </Field>
                    <Field label="Join date"><input v-model="form.join_date" type="date" class="input" required /></Field>
                    <Field label="End date"><input v-model="form.end_date" type="date" class="input" /></Field>
                    <Field label="Base salary (IDR)"><input v-model.number="form.base_salary" type="number" min="0" class="input" required /></Field>
                    <Field label="Active">
                        <label class="inline-flex items-center gap-2 mt-2">
                            <input v-model="form.is_active" type="checkbox" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500" />
                            <span class="text-sm text-surface-600 dark:text-surface-300">Currently employed</span>
                        </label>
                    </Field>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="text-sm font-semibold mb-4">Statutory & Banking</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <Field label="BPJS Kesehatan"><input v-model="form.bpjs_kesehatan" class="input" /></Field>
                    <Field label="BPJS Ketenagakerjaan"><input v-model="form.bpjs_ketenagakerjaan" class="input" /></Field>
                    <Field label="Bank name"><input v-model="form.bank_name" class="input" /></Field>
                    <Field label="Bank account number"><input v-model="form.bank_account_number" class="input" /></Field>
                    <Field label="Account holder name" class="sm:col-span-2"><input v-model="form.bank_account_name" class="input" /></Field>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <Link :href="route('employees.index')" class="btn-ghost">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary">
                    <CheckIcon class="h-4 w-4" />
                    {{ isEdit ? 'Save changes' : 'Create employee' }}
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

<script>
import { defineComponent, h } from 'vue';
const Field = defineComponent({
    props: { label: String },
    setup(props, { slots, attrs }) {
        return () => h('label', { class: ['block', attrs.class] }, [
            h('span', { class: 'label mb-1' }, props.label),
            slots.default?.(),
        ]);
    },
});
export default { components: { Field } };
</script>
