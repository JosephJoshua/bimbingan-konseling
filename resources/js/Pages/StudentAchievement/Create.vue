<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ChevronLeftIcon } from '@heroicons/vue/24/solid';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Student } from '@/types/student';

const props = defineProps<{
  student: Student;
}>();

const form = useForm({
  title: '',
});

const submit = () => {
  form.post(
    route('students.achievements.store', { student: props.student.id }),
  );
};
</script>

<template>
  <Head :title="`${student.full_name} - Tambah Prestasi`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <Link :href="route('students.index')">
          <ChevronLeftIcon class="w-4 h-4" />
        </Link>

        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          {{ student.full_name }} | Tambah Prestasi
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg px-6 py-4"
        >
          <form @submit.prevent="submit">
            <div>
              <InputLabel for="title" value="Judul" required />

              <TextInput
                v-model="form.title"
                id="title"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
              />

              <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div class="flex items-center justify-end mt-4">
              <PrimaryButton
                class="ms-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                + Tambah
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
