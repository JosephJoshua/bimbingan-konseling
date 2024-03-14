<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseSelect from '@/Components/BaseSelect.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ChevronLeftIcon } from '@heroicons/vue/24/solid';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { WithStudent } from '@/types/student';
import { ConsultationCategory } from '@/types/consultation-category';
import { QuillEditor } from '@vueup/vue-quill';
import ImageCompress from 'quill-image-compress';
import MagicUrl from 'quill-magic-url';
import ImageUploader from 'quill-image-uploader';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import axios from 'axios';
import { Consultation } from '@/types/consultation';
import { DatePicker } from 'v-calendar';

const props = defineProps<{
  data: WithStudent<Consultation>;
  categories: ConsultationCategory[];
  index: number;
}>();

const form = useForm({
  consultation_date: new Date(props.data.consultation_date),
  consultation_category_id: props.data.consultation_category_id,
  description: props.data.description,
});

const submit = () => {
  form.put(route('consultations.update', { consultation: props.data.id }));
};

const handleUploadImage = async (file: File) => {
  const formData = new FormData();
  formData.append('image', file);

  const response = await axios.post(
    route('consultations.upload-image'),
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    },
  );

  return response.data as string;
};
</script>

<style>
.ql-editor {
  min-height: 200px;
}
</style>

<template>
  <Head :title="`${data.student.full_name} | Ubah Konsultasi #${index}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <Link :href="route('consultations.index')">
          <ChevronLeftIcon class="w-4 h-4" />
        </Link>

        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          {{ data.student.full_name }} | Ubah Konsultasi #{{ index }}
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
              <InputLabel
                for="consultation_date"
                value="Tanggal Konsultasi"
                required
              />

              <DatePicker
                v-model="form.consultation_date"
                is-required
                locale="id"
                :update-on-input="false"
              >
                <template #default="{ inputValue, inputEvents }">
                  <TextInput
                    class="w-full mt-1"
                    :value="inputValue"
                    v-on="inputEvents"
                  />
                </template>
              </DatePicker>

              <InputError
                class="mt-2"
                :message="form.errors.consultation_date"
              />
            </div>

            <div class="mt-4">
              <InputLabel
                for="student_name"
                value="Nama Lengkap Murid"
                required
              />

              <TextInput
                id="student_name"
                :model-value="data.student.full_name"
                type="text"
                class="mt-1 block w-full"
                readonly
                required
              />
            </div>

            <div class="mt-4">
              <InputLabel
                for="consultation_category_id"
                value="Kategori Konsultasi"
                required
              />

              <BaseSelect
                id="consultation_category_id"
                v-model="form.consultation_category_id"
                class="mt-1 block w-full"
                required
              >
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </BaseSelect>

              <InputError
                class="mt-2"
                :message="form.errors.consultation_category_id"
              />
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
