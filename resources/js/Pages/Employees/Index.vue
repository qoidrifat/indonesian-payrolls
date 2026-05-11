<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Avatar from '@/Components/Avatar.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import { UsersIcon, PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { fmtIdr } from '@/lib/format';

const props = defineProps({ employees: Object, filters: Object });

const q = ref(props.filters?.q ?? '');
const status = ref(props.filters?.status ?? '');
let t;
watch([q, status], () => {
    clearTimeout(t);
    t = setTimeout(() => {
        router.get(route('employees.index'), { q: q.value, status: status.value }, { preserveState: true, replace: true });
    }, 250);
});
</script>

<template>
    <Head title="Employees" />
    <AuthenticatedLayout>
        <PageHeader
            title="Employees"
            description="The humans behind your product. Manage profiles, salaries, and statuses."
            :icon="UsersIcon"
        >
            <template #actions>
                <Link :href="route('employees.create')" class="btn-primary">
                    <PlusIcon class="h-4 w-4" /> Add employee
                </Link>
            </template>
        </PageHeader>

        <div class="card p-4 mb-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-surface-400" />
                    <input v-model="q" type="text" placeholder="Search by name, email, code, position…" class="input pl-9" />
                </div>
                <select v-model="status" class="input sm:w-44">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div v-if="employees.data.length" class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Code</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th class="text-right">Base salary</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200/60 dark:divide-surface-800/60">
                        <tr v-for="e in employees.data" :key="e.id" class="cursor-pointer" @click="router.visit(route('employees.show', e.id))">
                            <td>
                                <div class="flex items-center gap-3">
                                    <Avatar :name="e.name" />
                                    <div class="min-w-0">
                                        <p class="font-semibold">{{ e.name }}</p>
                                        <p class="text-xs text-surface-500 truncate">{{ e.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs">{{ e.employee_code }}</td>
                            <td>{{ e.position }}</td>
                            <td class="text-surface-500">{{ e.department || '—' }}</td>
                            <td><StatusBadge :status="e.employment_status" /></td>
                            <td class="text-right font-semibold">{{ fmtIdr(e.base_salary) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState
                v-else
                title="No employees yet"
                description="Add your first team member to start building payroll runs."
                :icon="UsersIcon"
            >
                <Link :href="route('employees.create')" class="btn-primary">
                    <PlusIcon class="h-4 w-4" /> Add employee
                </Link>
            </EmptyState>
        </div>

        <Pagination :links="employees.links" />
    </AuthenticatedLayout>
</template>
