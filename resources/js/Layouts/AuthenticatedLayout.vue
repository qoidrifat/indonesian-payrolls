<script setup>
import { computed, ref, watch, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import {
    HomeIcon, UsersIcon, ClockIcon, BanknotesIcon,
    DocumentTextIcon, ChartBarIcon, Cog6ToothIcon,
    AdjustmentsHorizontalIcon, ArrowRightOnRectangleIcon,
    Bars3Icon, XMarkIcon, MoonIcon, SunIcon,
    BellIcon, MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline';
import { Bars3BottomLeftIcon } from '@heroicons/vue/24/solid';
import Toast from '@/Components/Toast.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash || {});

const navMain = [
    { name: 'Dashboard',          href: route('dashboard'),           icon: HomeIcon,                  match: 'dashboard' },
    { name: 'Employees',          href: route('employees.index'),     icon: UsersIcon,                 match: 'employees' },
    { name: 'Attendance',         href: route('attendance.index'),    icon: ClockIcon,                 match: 'attendance' },
    { name: 'Salary Config',      href: route('salary-components.index'), icon: AdjustmentsHorizontalIcon, match: 'salary-components' },
];
const navPayroll = [
    { name: 'Payrolls',           href: route('payrolls.index'),      icon: BanknotesIcon,             match: 'payrolls' },
    { name: 'Payslips',           href: route('payslips.index'),      icon: DocumentTextIcon,          match: 'payslips' },
    { name: 'Reports',            href: route('reports.index'),       icon: ChartBarIcon,              match: 'reports' },
];
const navAccount = [
    { name: 'Settings',           href: route('settings.edit'),       icon: Cog6ToothIcon,             match: 'settings' },
];

const sidebarOpen = ref(false);
const dark = ref(false);

onMounted(() => {
    const stored = localStorage.getItem('theme');
    dark.value = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme();
});
watch(dark, applyTheme);
function applyTheme() {
    const root = document.documentElement;
    if (dark.value) root.classList.add('dark'); else root.classList.remove('dark');
    localStorage.setItem('theme', dark.value ? 'dark' : 'light');
}

function isActive(match) {
    return new URL(page.url, window.location.origin).pathname.startsWith('/' + match);
}

function logout() { router.post(route('logout')); }
</script>

<template>
    <div class="min-h-screen relative bg-surface-50 dark:bg-surface-950">
        <!-- Decorative background -->
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-32 -left-32 h-[36rem] w-[36rem] rounded-full bg-mesh-1 opacity-60 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-mesh-2 opacity-50 blur-3xl"></div>
        </div>

        <!-- Sidebar (mobile drawer) -->
        <transition name="fade">
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-surface-900/40 backdrop-blur-sm lg:hidden" @click="sidebarOpen=false"></div>
        </transition>
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-72 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:z-auto',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <div class="flex h-full flex-col border-r border-surface-200/60 dark:border-surface-800/60 bg-white/70 dark:bg-surface-900/60 backdrop-blur-xl">
                <div class="flex items-center justify-between px-5 py-5">
                    <Link :href="route('dashboard')" class="flex items-center gap-2.5">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-brand-600 via-violet-600 to-cyan-500 text-white shadow-glow">
                            <Bars3BottomLeftIcon class="h-5 w-5" />
                        </span>
                        <span class="text-base font-bold tracking-tight">
                            <span class="gradient-text">Aurex</span>
                            <span class="text-surface-500 dark:text-surface-400 font-medium"> · Payroll</span>
                        </span>
                    </Link>
                    <button @click="sidebarOpen=false" class="lg:hidden p-2 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <nav class="flex-1 px-3 py-2 space-y-6 overflow-y-auto">
                    <div>
                        <p class="px-3 pb-1.5 text-[10px] font-semibold uppercase tracking-widest text-surface-400">Workspace</p>
                        <NavItem v-for="i in navMain" :key="i.name" :item="i" :active="isActive(i.match)" />
                    </div>
                    <div>
                        <p class="px-3 pb-1.5 text-[10px] font-semibold uppercase tracking-widest text-surface-400">Payroll</p>
                        <NavItem v-for="i in navPayroll" :key="i.name" :item="i" :active="isActive(i.match)" />
                    </div>
                    <div>
                        <p class="px-3 pb-1.5 text-[10px] font-semibold uppercase tracking-widest text-surface-400">Account</p>
                        <NavItem v-for="i in navAccount" :key="i.name" :item="i" :active="isActive(i.match)" />
                    </div>
                </nav>

                <div class="border-t border-surface-200/60 dark:border-surface-800/60 px-3 py-4">
                    <Link :href="route('profile.edit')" class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-surface-100 dark:hover:bg-surface-800 transition">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-brand-500 to-violet-500 text-white text-sm font-semibold">
                            {{ user?.name?.[0] || 'A' }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold truncate">{{ user?.name }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400 truncate">{{ user?.email }}</p>
                        </div>
                        <ArrowRightOnRectangleIcon @click.stop.prevent="logout" class="h-5 w-5 text-surface-400 hover:text-rose-500 cursor-pointer" />
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main column -->
        <div class="lg:pl-72">
            <header class="sticky top-0 z-30 backdrop-blur-xl bg-white/60 dark:bg-surface-950/50 border-b border-surface-200/60 dark:border-surface-800/60">
                <div class="flex items-center gap-3 px-4 sm:px-6 py-3">
                    <button @click="sidebarOpen=true" class="lg:hidden p-2 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800">
                        <Bars3Icon class="h-5 w-5" />
                    </button>

                    <div class="hidden sm:flex flex-1 max-w-md items-center gap-2 rounded-xl px-3 py-2 bg-surface-100/80 dark:bg-surface-900/80 border border-surface-200/60 dark:border-surface-800/60 text-sm">
                        <MagnifyingGlassIcon class="h-4 w-4 text-surface-400" />
                        <input type="text" placeholder="Quick search…" class="bg-transparent border-0 outline-none p-0 flex-1 text-sm focus:ring-0" />
                        <kbd class="hidden md:inline-flex items-center rounded-md border border-surface-300/60 dark:border-surface-700 px-1.5 py-0.5 text-[10px] text-surface-500">⌘ K</kbd>
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        <button class="p-2 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 relative">
                            <BellIcon class="h-5 w-5" />
                            <span class="absolute top-1.5 right-1.5 h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                        </button>
                        <button @click="dark=!dark" class="p-2 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800">
                            <SunIcon v-if="dark" class="h-5 w-5" />
                            <MoonIcon v-else class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </header>

            <main class="px-4 sm:px-6 lg:px-8 py-6 max-w-[1500px] mx-auto animate-fade-in">
                <slot />
            </main>
        </div>

        <Toast :flash="flash" />
    </div>
</template>

<script>
import { defineComponent, h } from 'vue';
import { Link as InertiaLink } from '@inertiajs/vue3';
const NavItem = defineComponent({
    props: { item: Object, active: Boolean },
    setup(props) {
        return () => h(InertiaLink, {
            href: props.item.href,
            class: [
                'group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition relative',
                props.active
                    ? 'text-white bg-gradient-to-r from-brand-600 via-violet-600 to-cyan-500 shadow-glow'
                    : 'text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800',
            ],
        }, () => [
            h(props.item.icon, { class: 'h-5 w-5 shrink-0' }),
            h('span', { class: 'truncate' }, props.item.name),
        ]);
    },
});
export default { components: { NavItem } };
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
