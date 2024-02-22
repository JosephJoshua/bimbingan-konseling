<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CalendarIcon, ChevronLeftIcon } from '@heroicons/vue/24/solid';
import { Head, Link } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { id } from 'date-fns/locale/id';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { Event } from '@/types/event';

defineProps<{
  data: Event;
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
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg px-6 py-4"
        >
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <CalendarIcon class="w-5 h-5" />
              <span class="text-gray-600">
                {{
                  format(data.event_date, 'd MMMM y', {
                    locale: id,
                  })
                }}
                {{ ' ' }}
                {{ data.event_time }}
              </span>
            </div>
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
