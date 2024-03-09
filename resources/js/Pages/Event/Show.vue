<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CalendarIcon, ChevronLeftIcon } from '@heroicons/vue/24/solid';
import { Head, Link } from '@inertiajs/vue3';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { Event } from '@/types/event';
import formatEventDate from '@/utils/format-event-date';

defineProps<{
  data: Event & {
    status: 'done' | 'upcoming' | 'ongoing';
  };
}>();
</script>

<template>
  <Head :title="data.title" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <Link :href="route('events.index')">
          <ChevronLeftIcon class="w-4 h-4" />
        </Link>

        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          {{ data.title }}
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg px-6 py-4"
        >
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <CalendarIcon class="w-5 h-5" />
              <span class="text-gray-600">
                {{ formatEventDate(data) }}
              </span>
            </div>

            <span
              :class="{
                'bg-green-100 text-green-800 dark:bg-green-500 dark:text-green-100':
                  data.status === 'done',
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-500 dark:text-yellow-100':
                  data.status === 'upcoming',
                'bg-blue-100 text-blue-800 dark:bg-blue-500 dark:text-blue-100':
                  data.status === 'ongoing',
              }"
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
            >
              {{
                data.status === 'done'
                  ? 'Selesai'
                  : data.status === 'upcoming'
                  ? 'Mendatang'
                  : 'Sedang berlangsung'
              }}
            </span>
          </div>

          <div class="font-medium tracking-wide mt-6 text-xl">Deskripsi</div>
          <div class="h-px w-full bg-gray-200 mt-1 mb-4"></div>

          <div class="ql-snow">
            <div class="ql-editor" v-html="data.description"></div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
