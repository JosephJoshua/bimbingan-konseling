<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ArrowRightIcon, ChevronLeftIcon } from '@heroicons/vue/24/solid';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import BlotFormatter from 'quill-blot-formatter';
import ImageCompress from 'quill-image-compress';
import MagicUrl from 'quill-magic-url';
import ImageUploader from 'quill-image-uploader';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { DatePicker } from 'v-calendar';
import { endOfMinute, startOfMinute } from 'date-fns';
import axios from 'axios';
import { Event } from '@/types/event';

const props = defineProps<{
  data: Event;
}>();

const form = useForm<{
  title: string;
  description: string;
  start_date: Date | null;
  end_date: Date | null;
}>({
  title: props.data.title,
  description: props.data.description,
  start_date: null,
  end_date: null,
});

const dateRange = ref({
  start: props.data.start_date,
  end: props.data.end_date,
});

const submit = () => {
  form.start_date = startOfMinute(dateRange.value.start);
  form.end_date = endOfMinute(dateRange.value.end);

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
          class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg px-6 py-4"
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

              <DatePicker
                v-model.range="dateRange"
                mode="dateTime"
                is24hr
                is-required
                locale="id"
                :time-accuracy="2"
                :popover="{
                  placement: 'bottom',
                }"
                :update-on-input="false"
              >
                <template #default="{ inputValue, inputEvents }">
                  <div class="flex items-center gap-4">
                    <TextInput
                      class="flex-1"
                      :value="inputValue.start"
                      v-on="inputEvents.start"
                    />

                    <ArrowRightIcon class="h-5" />

                    <TextInput
                      class="flex-1"
                      :value="inputValue.end"
                      v-on="inputEvents.end"
                    />
                  </div>
                </template>
              </DatePicker>

              <div class="mt-2 flex flex-col gap-1">
                <InputError :message="form.errors.start_date" />
                <InputError :message="form.errors.end_date" />
              </div>
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
