<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, update } from '@/routes/admin/customer';

const props = defineProps<{
    customer: {
        id: number;
        name: string;
        email: string;
        status: string;
    };
    statuses: string[];
}>();

const form = useForm({
    name: props.customer.name,
    email: props.customer.email,
    password: '',
    status: props.customer.status,
});

const submit = () => {
    form.put(update.url(props.customer.id));
};
defineOptions({
    layout: {
        name: 'AdminSidebar',
        breadcrumbs: [
            {
                title: 'Customer Management',
                href: index.url(),
            },
            {
                title: 'Edit Customer',
                href: index.url(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Edit Customer" />

    <div class="mx-auto max-w-2xl space-y-6 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Customer</h1>
            <Button as-child variant="outline">
                <Link :href="index.url()">Back</Link>
            </Button>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input id="name" v-model="form.name" type="text" required />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input id="email" v-model="form.email" type="email" required />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password (optional)</Label>
                <Input id="password" v-model="form.password" type="password" />
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="status">Status</Label>
                <select id="status" v-model="form.status" class="rounded-md border p-2">
                    <option v-for="status in statuses" :key="status" :value="status">
                        {{ status }}
                    </option>
                </select>
                <InputError :message="form.errors.status" />
            </div>

            <Button :disabled="form.processing" type="submit">Update Customer</Button>
        </form>
    </div>
</template>
