<script setup>
import { Link } from '@inertiajs/vue3';
import { SparklesIcon, ShieldCheckIcon, BoltIcon } from '@heroicons/vue/24/outline';
</script>

<template>
    <div class="min-h-screen relative grid lg:grid-cols-2 bg-surface-50 dark:bg-surface-950 text-surface-900 dark:text-surface-50">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-40 -right-40 h-[40rem] w-[40rem] rounded-full bg-mesh-1 opacity-60 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 h-[36rem] w-[36rem] rounded-full bg-mesh-2 opacity-50 blur-3xl"></div>
        </div>

        <!-- Left side: form -->
        <div class="flex flex-col justify-between p-6 sm:p-10">
            <Link href="/" class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-brand-600 via-violet-600 to-cyan-500 text-white shadow-glow">
                    <SparklesIcon class="h-5 w-5" />
                </span>
                <span class="text-base font-bold tracking-tight">
                    <span class="gradient-text">Aurex</span>
                    <span class="text-surface-500 font-medium"> · Payroll</span>
                </span>
            </Link>

            <div class="flex flex-1 items-center">
                <div class="w-full max-w-md mx-auto">
                    <slot />
                </div>
            </div>

            <p class="text-xs text-surface-500">© {{ new Date().getFullYear() }} Aurex Payroll</p>
        </div>

        <!-- Right side: hero -->
        <div class="hidden lg:flex relative overflow-hidden items-center justify-center p-10 bg-gradient-to-br from-brand-600 via-violet-600 to-cyan-500 text-white">
            <div class="absolute inset-0 bg-mesh-1 opacity-40"></div>
            <div class="relative max-w-md text-center">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/15 backdrop-blur px-3 py-1 text-xs">
                    <BoltIcon class="h-3.5 w-3.5" /> Modern payroll for Indonesian web teams
                </div>
                <h2 class="mt-6 text-4xl font-bold tracking-tight leading-tight">
                    Payroll that feels<br/> absurdly simple.
                </h2>
                <p class="mt-4 text-white/80 text-sm leading-relaxed">
                    BPJS, PPh 21, overtime, beautiful payslips — handled in one elegant workflow.
                </p>
                <div class="mt-8 grid grid-cols-3 gap-3 text-xs">
                    <div class="rounded-2xl bg-white/10 backdrop-blur p-3">
                        <ShieldCheckIcon class="h-5 w-5 mx-auto mb-2" />
                        Compliant
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur p-3">
                        <BoltIcon class="h-5 w-5 mx-auto mb-2" />
                        Lightning fast
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur p-3">
                        <SparklesIcon class="h-5 w-5 mx-auto mb-2" />
                        Beautiful UI
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
