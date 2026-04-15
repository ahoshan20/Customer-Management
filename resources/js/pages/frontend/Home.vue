<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">

    <!-- Navbar -->
    <header class="flex justify-between items-center p-6 bg-white shadow">
      <h1 class="text-xl font-bold text-blue-600">CustomerHub</h1>

      <Link
        v-if="$page.props.auth?.user"
        href="/admin/dashboard"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg"
      >
        Dashboard
      </Link>
      <template v-else>
        <Link href="/admin/login"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg">
        Login
      </Link>

      </template>
    </header>

    <!-- Hero -->
    <section class="flex-1 flex items-center justify-center text-center px-6">
      <div>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">
          Manage Your Customers Easily
        </h2>

        <p class="text-gray-600 mb-6">
          Track, analyze and grow your business with our dashboard.
        </p>

        <Link
          href="/admin/login"
          class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg"
        >
          Get Started
        </Link>
      </div>
    </section>

  </div>
</template>