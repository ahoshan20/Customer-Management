<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Users, ArrowRight } from 'lucide-vue-next';
import { dashboard } from '@/routes/admin';
import { index as customerIndex } from '@/routes/admin/customer';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Admin Dashboard',
                href: dashboard.url(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
            <h1 class="text-2xl font-semibold">Admin Dashboard</h1>
            <p class="mt-2 text-sm text-muted-foreground">
                Manage key sections of the admin panel.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Link
                :href="customerIndex.url()"
                class="group rounded-xl border border-sidebar-border/70 p-5 transition hover:bg-muted/40 dark:border-sidebar-border"
            >
                <div class="mb-3 inline-flex rounded-md bg-muted p-2">
                    <Users class="h-5 w-5" />
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-medium">Customer Management</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            View and manage your customer records.
                        </p>
                    </div>
                    <ArrowRight class="h-4 w-4 text-muted-foreground transition group-hover:translate-x-1" />
                </div>
            </Link>
        </div>
    </div>
</template>
