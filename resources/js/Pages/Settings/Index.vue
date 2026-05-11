<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Cog6ToothIcon, CheckIcon, BuildingOffice2Icon, BanknotesIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ settings: Object });

const form = useForm({ ...props.settings });

function submit() {
    form.patch(route('settings.update'));
}
</script>

<template>
    <Head title="Settings" />
    <AuthenticatedLayout>
        <PageHeader title="Settings" description="Configure your company profile and payroll defaults." :icon="Cog6ToothIcon" />

        <form @submit.prevent="submit" class="grid gap-6 max-w-3xl">
            <div class="card p-5">
                <div class="flex items-center gap-2 mb-4">
                    <BuildingOffice2Icon class="h-5 w-5 text-brand-600 dark:text-brand-300" />
                    <h3 class="text-sm font-semibold">Company profile</h3>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="block sm:col-span-2"><span class="label mb-1">Company name</span><input v-model="form.company_name" class="input" required /></label>
                    <label class="block sm:col-span-2"><span class="label mb-1">Address</span><textarea v-model="form.company_address" rows="2" class="input"></textarea></label>
                    <label class="block"><span class="label mb-1">Email</span><input v-model="form.company_email" type="email" class="input" /></label>
                    <label class="block"><span class="label mb-1">Phone</span><input v-model="form.company_phone" class="input" /></label>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center gap-2 mb-4">
                    <BanknotesIcon class="h-5 w-5 text-emerald-600 dark:text-emerald-300" />
                    <h3 class="text-sm font-semibold">Payroll defaults</h3>
                </div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <label class="block"><span class="label mb-1">Currency</span><input v-model="form.currency" class="input" /></label>
                    <label class="block"><span class="label mb-1">Pay day (of month)</span><input v-model.number="form.pay_day" type="number" min="1" max="28" class="input" /></label>
                    <label class="block"><span class="label mb-1">Working hours / day</span><input v-model.number="form.default_working_hours" type="number" min="1" max="12" class="input" /></label>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center gap-2 mb-4">
                    <h3 class="text-sm font-semibold">BPJS rates (%)</h3>
                </div>
                <p class="text-xs text-surface-500 mb-4">Default values follow current Perpres 64/2020 & PP 44–45/2015 guidance. Adjust if your industry uses a different JKK risk grade.</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <label class="block"><span class="label mb-1">Kesehatan – Employee</span><input v-model.number="form.bpjs_kesehatan_employee_rate" type="number" step="0.01" class="input" /></label>
                    <label class="block"><span class="label mb-1">Kesehatan – Company</span><input v-model.number="form.bpjs_kesehatan_company_rate" type="number" step="0.01" class="input" /></label>
                    <label class="block"><span class="label mb-1">JHT – Employee</span><input v-model.number="form.bpjs_jht_employee_rate" type="number" step="0.01" class="input" /></label>
                    <label class="block"><span class="label mb-1">JHT – Company</span><input v-model.number="form.bpjs_jht_company_rate" type="number" step="0.01" class="input" /></label>
                    <label class="block"><span class="label mb-1">JP – Employee</span><input v-model.number="form.bpjs_jp_employee_rate" type="number" step="0.01" class="input" /></label>
                    <label class="block"><span class="label mb-1">JP – Company</span><input v-model.number="form.bpjs_jp_company_rate" type="number" step="0.01" class="input" /></label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="form.processing" class="btn-primary">
                    <CheckIcon class="h-4 w-4" /> Save settings
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
