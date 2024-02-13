<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CalendarIcon, ChevronLeftIcon } from '@heroicons/vue/24/solid';
import { Head, Link } from '@inertiajs/vue3';
import { Consultation } from '@/types/consultation';
import { WithConsultationCategory } from '@/types/consultation-category';
import { WithStudent } from '@/types/student';
import { format } from 'date-fns';
import { id } from 'date-fns/locale/id';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

defineProps<{
  consultation: WithStudent<WithConsultationCategory<Consultation>>;
  index: number;
}>();
</script>

<template>
  <Head :title="`${consultation.student.full_name} | Konsultasi #${index}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <Link :href="route('consultations.index')">
          <ChevronLeftIcon class="w-4 h-4" />
        </Link>

        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          {{ consultation.student.full_name }} | Konsultasi #{{ index }}
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
                  format(consultation.consultation_date, 'd MMMM y', {
                    locale: id,
                  })
                }}
              </span>
            </div>

            <div
              class="px-2 py-1 text-xs font-semibold leading-none text-green-800 bg-green-100 rounded-full"
            >
              {{ consultation.consultation_category.name }}
            </div>
          </div>

          <div class="font-medium tracking-wide mt-6 text-xl">Deskripsi</div>
          <div class="h-px w-full bg-gray-200 mt-1 mb-4"></div>

          <div class="ql-snow">
            <div class="ql-editor" v-html="consultation.description"></div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
