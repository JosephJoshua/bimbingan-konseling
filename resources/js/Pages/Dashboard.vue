<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="relative min-h-[calc(95vh-200px)] overflow-x-auto shadow-md sm:rounded-lg flex flex-col"
        >
          <div class="p-6 text-gray-900">You're logged in!</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
