<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { BanknotesIcon, ArrowLeftIcon, PlayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ defaults: Object });

const form = useForm({
    period_year: props.defaults.period_year,
    period_month: props.defaults.period_month,
    period_start: props.defaults.period_start,
    period_end: props.defaults.period_end,
    pay_date: props.defaults.pay_date,
    notes: '',
});

function submit() {
    form.post(route('payrolls.store'));
}
</script>

<template>
    <Head title="New payroll" />
    <AuthenticatedLayout>
        <PageHeader title="New payroll" description="Define a new monthly cycle. You can run calculations after creation." :icon="BanknotesIcon">
            <template #actions>
                <Link :href="route('payrolls.index')" class="btn-ghost">
                    <ArrowLeftIcon class="h-4 w-4" /> Back
                </Link>
            </template>
        </PageHeader>

        <form @submit.prevent="submit" class="card p-6 grid gap-4 max-w-2xl">
            <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                    <span class="label mb-1">Year</span>
                    <input v-model.number="form.period_year" type="number" class="input" required min="2020" max="2099" />
                </label>
                <label class="block">
                    <span class="label mb-1">Month</span>
                    <select v-model.number="form.period_month" class="input">
                        <option v-for="m in 12" :key="m" :value="m">{{ m }}</option>
                    </select>
                </label>
                <label class="block">
                    <span class="label mb-1">Period start</span>
                    <input v-model="form.period_start" type="date" class="input" required />
                </label>
                <label class="block">
                    <span class="label mb-1">Period end</span>
                    <input v-model="form.period_end" type="date" class="input" required />
                </label>
                <label class="block sm:col-span-2">
                    <span class="label mb-1">Pay date</span>
                    <input v-model="form.pay_date" type="date" class="input" />
                </label>
                <label class="block sm:col-span-2">
                    <span class="label mb-1">Notes</span>
                    <textarea v-model="form.notes" rows="2" class="input" placeholder="Optional"></textarea>
                </label>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <Link :href="route('payrolls.index')" class="btn-ghost">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary">
                    <PlayIcon class="h-4 w-4" /> Create & continue
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
