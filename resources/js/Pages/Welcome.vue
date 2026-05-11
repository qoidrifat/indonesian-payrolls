<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRightIcon, BanknotesIcon, BoltIcon, ChartBarIcon,
    ClockIcon, DocumentTextIcon, ShieldCheckIcon, SparklesIcon, UsersIcon,
} from '@heroicons/vue/24/outline';

defineProps({ canLogin: Boolean, canRegister: Boolean });

const features = [
    { icon: BanknotesIcon, title: 'Compliant payroll engine',  desc: 'BPJS, PPh 21, overtime — all calculated automatically using the latest Indonesian regulations.' },
    { icon: UsersIcon,     title: 'Built for small teams',     desc: 'Designed for 5-50 person companies. Lightweight, fast, and zero learning curve.' },
    { icon: ClockIcon,     title: 'Flexible attendance',       desc: 'Remote, on-site, flex hours, manual entries. Works the way your team actually works.' },
    { icon: DocumentTextIcon, title: 'Beautiful payslips',     desc: 'Branded PDF payslips you can drop into Slack, WhatsApp, or email — instantly.' },
    { icon: ChartBarIcon,  title: 'Reports that don\'t suck',  desc: 'Live dashboards, Excel exports, tax summaries. Everything stakeholders ask for.' },
    { icon: ShieldCheckIcon, title: 'Role-based security',    desc: 'Admin, HR, and Employee roles with fine-grained permissions and full activity logging.' },
];
</script>

<template>
    <Head title="Aurex Payroll — modern payroll for Indonesian web teams" />
    <div class="min-h-screen relative bg-surface-50 dark:bg-surface-950 text-surface-900 dark:text-surface-50">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-40 -right-40 h-[40rem] w-[40rem] rounded-full bg-mesh-1 opacity-60 blur-3xl"></div>
            <div class="absolute bottom-0 -left-40 h-[36rem] w-[36rem] rounded-full bg-mesh-2 opacity-50 blur-3xl"></div>
        </div>

        <header class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
            <Link href="/" class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-brand-600 via-violet-600 to-cyan-500 text-white shadow-glow">
                    <SparklesIcon class="h-5 w-5" />
                </span>
                <span class="text-base font-bold tracking-tight">
                    <span class="gradient-text">Aurex</span>
                    <span class="text-surface-500 font-medium"> · Payroll</span>
                </span>
            </Link>
            <nav class="flex items-center gap-2 text-sm">
                <Link v-if="canLogin" :href="route('login')" class="btn-ghost">Sign in</Link>
                <Link v-if="canRegister" :href="route('register')" class="btn-primary">
                    Get started <ArrowRightIcon class="h-4 w-4" />
                </Link>
            </nav>
        </header>

        <section class="max-w-6xl mx-auto px-6 pt-16 pb-24 text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-surface-200/60 dark:border-surface-800/60 px-3 py-1 text-xs bg-white/60 dark:bg-surface-900/60 backdrop-blur">
                <BoltIcon class="h-3.5 w-3.5 text-violet-500" />
                <span class="text-surface-600 dark:text-surface-300">Built for Indonesian web & IT teams</span>
            </div>
            <h1 class="mt-6 text-4xl sm:text-6xl font-bold tracking-tight leading-[1.05]">
                Payroll that feels<br />
                <span class="gradient-text">absurdly simple.</span>
            </h1>
            <p class="mt-6 text-base sm:text-lg text-surface-600 dark:text-surface-400 max-w-xl mx-auto">
                Aurex is a modern SaaS payroll engine for small Indonesian companies. BPJS, PPh 21, overtime — handled. Beautiful UI, zero spreadsheet pain.
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <Link v-if="canRegister" :href="route('register')" class="btn-primary">
                    Start free trial <ArrowRightIcon class="h-4 w-4" />
                </Link>
                <Link :href="route('login')" class="btn-secondary">
                    Sign in
                </Link>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-6 pb-24">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="f in features" :key="f.title" class="card p-6 hover:-translate-y-0.5 transition">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 via-violet-500 to-cyan-500 text-white shadow-glow">
                        <component :is="f.icon" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-4 text-base font-semibold tracking-tight">{{ f.title }}</h3>
                    <p class="mt-1 text-sm text-surface-600 dark:text-surface-400 leading-relaxed">{{ f.desc }}</p>
                </div>
            </div>
        </section>

        <footer class="border-t border-surface-200/60 dark:border-surface-800/60 py-6 text-center text-xs text-surface-500">
            © {{ new Date().getFullYear() }} Aurex Payroll · Made in Bangkalan, Madura
        </footer>
    </div>
</template>
