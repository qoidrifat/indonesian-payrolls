<script setup>
import { computed } from 'vue';
const props = defineProps({ status: String });
const map = {
    draft:      { cls: 'chip-slate',   text: 'Draft' },
    calculated: { cls: 'chip-indigo',  text: 'Calculated' },
    approved:   { cls: 'chip-amber',   text: 'Approved' },
    paid:       { cls: 'chip-emerald', text: 'Paid' },
    cancelled:  { cls: 'chip-rose',    text: 'Cancelled' },
    present:    { cls: 'chip-emerald', text: 'Present' },
    remote:     { cls: 'chip-indigo',  text: 'Remote' },
    leave:      { cls: 'chip-amber',   text: 'Leave' },
    sick:       { cls: 'chip-amber',   text: 'Sick' },
    absent:     { cls: 'chip-rose',    text: 'Absent' },
    holiday:    { cls: 'chip-slate',   text: 'Holiday' },
    allowance:  { cls: 'chip-emerald', text: 'Allowance' },
    deduction:  { cls: 'chip-rose',    text: 'Deduction' },
    bonus:      { cls: 'chip-indigo',  text: 'Bonus' },
    permanent:  { cls: 'chip-emerald', text: 'Permanent' },
    contract:   { cls: 'chip-indigo',  text: 'Contract' },
    probation:  { cls: 'chip-amber',   text: 'Probation' },
    intern:     { cls: 'chip-slate',   text: 'Intern' },
};
const meta = computed(() => map[props.status] || { cls: 'chip-slate', text: props.status });
</script>

<template>
    <span :class="meta.cls">{{ meta.text }}</span>
</template>
