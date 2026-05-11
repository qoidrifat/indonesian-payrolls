<script setup>
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Avatar from '@/Components/Avatar.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { ClockIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { fmtDate } from '@/lib/format';

const props = defineProps({ records: Object, employees: Array, filters: Object });

const month = ref(props.filters?.month ?? new Date().toISOString().slice(0,7));
const employeeId = ref(props.filters?.employee_id ?? '');

let t;
watch([month, employeeId], () => {
    clearTimeout(t);
    t = setTimeout(() => router.get(route('attendance.index'), {
        month: month.value, employee_id: employeeId.value,
    }, { preserveState: true, replace: true }), 200);
});

const showModal = ref(false);
const form = useForm({
    employee_id: '',
    date: new Date().toISOString().slice(0,10),
    status: 'present',
    check_in: '09:00',
    check_out: '18:00',
    overtime_minutes: 0,
    location: 'Office',
    notes: '',
});

function submit() {
    form.post(route('attendance.store'), {
        preserveScroll: true,
        onSuccess: () => { showModal.value = false; form.reset('notes'); },
    });
}

function destroy(id) {
    if (!confirm('Delete this attendance record?')) return;
    router.delete(route('attendance.destroy', id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Attendance" />
    <AuthenticatedLayout>
        <PageHeader
            title="Attendance"
            description="Track in-office, remote, leave, and overtime. Flexible by design — built for modern teams."
            :icon="ClockIcon"
        >
            <template #actions>
                <button @click="showModal=true" class="btn-primary">
                    <PlusIcon class="h-4 w-4" /> Log attendance
                </button>
            </template>
        </PageHeader>

        <div class="card p-4 mb-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <input v-model="month" type="month" class="input sm:w-44" />
                <select v-model="employeeId" class="input sm:w-72">
                    <option value="">All employees</option>
                    <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }} ({{ e.employee_code }})</option>
                </select>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div v-if="records.data.length" class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Status</th>
                            <th>Check in</th>
                            <th>Check out</th>
                            <th class="text-right">OT (min)</th>
                            <th>Location</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200/60 dark:divide-surface-800/60">
                        <tr v-for="r in records.data" :key="r.id">
                            <td class="font-medium">{{ fmtDate(r.date) }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <Avatar :name="r.employee?.name" size="sm" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium truncate">{{ r.employee?.name }}</p>
                                        <p class="text-xs text-surface-500 font-mono truncate">{{ r.employee?.employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><StatusBadge :status="r.status" /></td>
                            <td>{{ r.check_in?.slice(0,5) || '—' }}</td>
                            <td>{{ r.check_out?.slice(0,5) || '—' }}</td>
                            <td class="text-right">{{ r.overtime_minutes || 0 }}</td>
                            <td class="text-surface-500">{{ r.location || '—' }}</td>
                            <td class="text-right">
                                <button @click="destroy(r.id)" class="p-1.5 rounded-lg hover:bg-rose-500/10 text-rose-500">
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState
                v-else
                title="No attendance for this period"
                description="Log a record manually or import from CSV."
                :icon="ClockIcon"
            >
                <button @click="showModal=true" class="btn-primary">
                    <PlusIcon class="h-4 w-4" /> Log attendance
                </button>
            </EmptyState>
        </div>

        <Pagination :links="records.links" />

        <Modal :show="showModal" title="Log attendance" description="Manually record a day for an employee." @close="showModal=false">
            <form @submit.prevent="submit" id="att-form" class="grid sm:grid-cols-2 gap-3">
                <label class="block sm:col-span-2">
                    <span class="label mb-1">Employee</span>
                    <select v-model="form.employee_id" class="input" required>
                        <option value="">Select…</option>
                        <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }}</option>
                    </select>
                </label>
                <label class="block">
                    <span class="label mb-1">Date</span>
                    <input v-model="form.date" type="date" class="input" required />
                </label>
                <label class="block">
                    <span class="label mb-1">Status</span>
                    <select v-model="form.status" class="input">
                        <option value="present">Present</option>
                        <option value="remote">Remote</option>
                        <option value="leave">Leave</option>
                        <option value="sick">Sick</option>
                        <option value="absent">Absent</option>
                        <option value="holiday">Holiday</option>
                    </select>
                </label>
                <label class="block">
                    <span class="label mb-1">Check in</span>
                    <input v-model="form.check_in" type="time" class="input" />
                </label>
                <label class="block">
                    <span class="label mb-1">Check out</span>
                    <input v-model="form.check_out" type="time" class="input" />
                </label>
                <label class="block">
                    <span class="label mb-1">Overtime (min)</span>
                    <input v-model.number="form.overtime_minutes" type="number" min="0" class="input" />
                </label>
                <label class="block">
                    <span class="label mb-1">Location</span>
                    <input v-model="form.location" class="input" />
                </label>
                <label class="block sm:col-span-2">
                    <span class="label mb-1">Notes</span>
                    <textarea v-model="form.notes" rows="2" class="input"></textarea>
                </label>
            </form>
            <template #footer>
                <button @click="showModal=false" type="button" class="btn-ghost">Cancel</button>
                <button form="att-form" type="submit" :disabled="form.processing" class="btn-primary">Save</button>
            </template>
        </Modal>
    </AuthenticatedLayout>
</template>
