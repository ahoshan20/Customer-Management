<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login as loginRoute } from '@/routes/admin';
import { post } from '@/routes/admin/login';
</script>

<template>
    <Head title="Admin Login" />
    <div class="w-full max-w-md space-y-5">

        <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="mb-5 text-center">
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Admin Login
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Sign in with your admin account.
                </p>
            </div>

            <Form
                v-bind="post.form()"
                class="space-y-4"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="admin@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <Label for="remember" class="inline-flex items-center gap-2">
                    <Checkbox id="remember" name="remember" />
                    <span class="text-sm">Remember me</span>
                </Label>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="processing"
                >
                    Log in
                </Button>
            </Form>
        </div>

        <div class="text-center text-sm text-slate-600 dark:text-slate-400">
            <Link
                :href="loginRoute.url()"
                class="underline underline-offset-4 hover:text-slate-900 dark:hover:text-slate-100"
            >
                Reload login page
            </Link>
        </div>
    </div>
</template>
