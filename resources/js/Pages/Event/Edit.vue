<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CalendarIcon, ChevronLeftIcon } from '@heroicons/vue/24/solid';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Datepicker from 'flowbite-datepicker/Datepicker';
import { ref } from 'vue';
import { onMounted } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import BlotFormatter from 'quill-blot-formatter';
import ImageCompress from 'quill-image-compress';
import MagicUrl from 'quill-magic-url';
import ImageUploader from 'quill-image-uploader';
import 'quill-paste-smart';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { parseISO } from 'date-fns';
import axios from 'axios';
import { Event } from '@/types/event';

const props = defineProps<{
  data: Event;
}>();

const form = useForm({
  title: props.data.title,
  description: props.data.description,
  event_date: props.data.event_date,
  event_time: props.data.event_time,
});

const eventDateRef = ref<HTMLInputElement | null>(null);

const submit = () => {
  if (eventDateRef.value === null) return;

  form.event_date = eventDateRef.value.value;
  form.put(route('events.update', { event: props.data.id }));
};

const handleUploadImage = async (file: File) => {
  const formData = new FormData();
  formData.append('image', file);

  const response = await axios.post(route('events.upload-image'), formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });

  return response.data as string;
};

onMounted(() => {
  if (eventDateRef.value === null) return;
  new Datepicker(eventDateRef.value).setDate(parseISO(form.event_date));
});
</script>

<style>
.ql-editor {
  min-height: 200px;
}
</style>

<template>
  <Head :title="`Ubah Kegiatan - ${data.title}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <Link :href="route('events.index')">
          <ChevronLeftIcon class="w-4 h-4" />
        </Link>

        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Ubah Kegiatan - {{ data.title }}
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
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full"
                autofocus
                required
              />

              <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div class="mt-4">
              <InputLabel for="event_date" value="Tanggal Kegiatan" required />

              <div class="relative w-full">
                <div
                  class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
                >
                  <CalendarIcon class="w-4 h-4" />
                </div>

                <input
                  ref="eventDateRef"
                  datepicker
                  datepicker-autohide
                  type="text"
                  class="mt-1 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                  placeholder="Pilih tanggal"
                />
              </div>

              <InputError class="mt-2" :message="form.errors.event_date" />
            </div>

            <div class="mt-4">
              <InputLabel for="event_time" value="Jam Kegiatan" required />

              <TextInput
                id="event_time"
                v-model="form.event_time"
                type="time"
                class="mt-1 block w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.event_time" />
            </div>

            <div class="mt-4">
              <InputLabel for="description" value="Deskripsi" required />

              <QuillEditor
                v-model:content="form.description"
                content-type="html"
                :modules="[
                  {
                    name: 'blotFormatter',
                    module: BlotFormatter,
                  },
                  {
                    name: 'imageCompress',
                    module: ImageCompress,
                    options: {
                      quality: 0.4,
                    },
                  },
                  {
                    name: 'magicUrl',
                    module: MagicUrl,
                  },
                  {
                    name: 'imageUploader',
                    module: ImageUploader,
                    options: {
                      upload: handleUploadImage,
                    },
                  },
                ]"
                theme="snow"
                toolbar="full"
                id="description"
                name="description"
                class="mt-1 w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <div class="flex items-center justify-end mt-4">
              <PrimaryButton
                class="ms-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Simpan
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
