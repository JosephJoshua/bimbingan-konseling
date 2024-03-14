<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseSelect from '@/Components/BaseSelect.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ChevronLeftIcon } from '@heroicons/vue/24/solid';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { GENDERS, GENDER_TRANSLATIONS } from '@/types/gender';
import { RELIGIONS, RELIGION_TRANSLATIONS } from '@/types/religion';
import {
  STATUSES_IN_FAMILY,
  STATUS_IN_FAMILY_TRANSLATIONS,
} from '@/types/status-in-family';
import { DatePicker } from 'v-calendar';

const form = useForm<{
  nisn: string;
  nis: string;
  nik: string;
  gender: string;
  religion: string;
  full_name: string;
  birth_date: Date | null;
  birth_place: string;
  status_in_family: string;
  child_order: number;
  full_address: string;
  origin_school: string;
  phone_number: string;
  email: string;
  father_name: string;
  father_phone_number: string;
  mother_name: string;
  mother_phone_number: string;
  guardian_name: string;
  guardian_phone_number: string;
}>({
  nisn: '',
  nis: '',
  nik: '',
  gender: '',
  religion: '',
  full_name: '',
  birth_date: null,
  birth_place: '',
  status_in_family: '',
  child_order: 1,
  full_address: '',
  origin_school: '',
  phone_number: '',
  email: '',
  father_name: '',
  father_phone_number: '',
  mother_name: '',
  mother_phone_number: '',
  guardian_name: '',
  guardian_phone_number: '',
});

const submit = () => {
  form.post(route('students.store'));
};
</script>

<template>
  <Head title="Tambah Murid" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <Link :href="route('students.index')">
          <ChevronLeftIcon class="w-4 h-4" />
        </Link>

        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Tambah Murid
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
              <InputLabel for="nisn" value="NISN" required />

              <TextInput
                id="nisn"
                v-model="form.nisn"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
              />

              <InputError class="mt-2" :message="form.errors.nisn" />
            </div>

            <div class="mt-4">
              <InputLabel for="nis" value="NIS" required />

              <TextInput
                id="nis"
                v-model="form.nis"
                type="text"
                class="mt-1 block w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.nis" />
            </div>

            <div class="mt-4">
              <InputLabel for="nik" value="NIK" required />

              <TextInput
                id="nik"
                v-model="form.nik"
                type="text"
                class="mt-1 block w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.nik" />
            </div>

            <div class="mt-4">
              <InputLabel for="full_name" value="Nama Lengkap" required />

              <TextInput
                id="full_name"
                v-model="form.full_name"
                type="text"
                class="mt-1 block w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.full_name" />
            </div>

            <div class="mt-4">
              <InputLabel for="gender" value="Jenis Kelamin" required />

              <BaseSelect
                id="gender"
                v-model="form.gender"
                class="mt-1 block w-full"
                required
              >
                <option v-for="gender in GENDERS" :key="gender" :value="gender">
                  {{ GENDER_TRANSLATIONS[gender] }}
                </option>
              </BaseSelect>

              <InputError class="mt-2" :message="form.errors.gender" />
            </div>

            <div class="mt-4">
              <InputLabel for="religion" value="Agama" required />

              <BaseSelect
                id="religion"
                v-model="form.religion"
                class="mt-1 block w-full"
                required
              >
                <option
                  v-for="religion in RELIGIONS"
                  :key="religion"
                  :value="religion"
                >
                  {{ RELIGION_TRANSLATIONS[religion] }}
                </option>
              </BaseSelect>

              <InputError class="mt-2" :message="form.errors.religion" />
            </div>

            <div class="mt-4">
              <InputLabel for="full_address" value="Alamat Lengkap" required />

              <TextInput
                id="full_address"
                v-model="form.full_address"
                type="text"
                class="mt-1 block w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.full_address" />
            </div>

            <div class="mt-4">
              <InputLabel for="phone_number" value="No. Telp" required />

              <TextInput
                id="phone_number"
                v-model="form.phone_number"
                type="text"
                class="mt-1 block w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.phone_number" />
            </div>

            <div class="mt-4">
              <InputLabel for="email" value="E-mail" />

              <TextInput
                id="email"
                v-model="form.email"
                type="text"
                class="mt-1 block w-full"
              />

              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
              <InputLabel for="birth_date" value="Tanggal Lahir" required />

              <DatePicker
                v-model="form.birth_date"
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

              <InputError class="mt-2" :message="form.errors.birth_date" />
            </div>

            <div class="mt-4">
              <InputLabel for="birth_place" value="Tempat Lahir" required />

              <TextInput
                id="birth_place"
                v-model="form.birth_place"
                type="text"
                class="mt-1 block w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.birth_place" />
            </div>

            <div class="mt-4">
              <InputLabel for="origin_school" value="Asal Sekolah" required />

              <TextInput
                id="origin_school"
                v-model="form.origin_school"
                type="text"
                class="mt-1 block w-full"
                required
              />

              <InputError class="mt-2" :message="form.errors.origin_school" />
            </div>

            <div class="mt-4">
              <InputLabel
                for="status_in_family"
                value="Status di Keluarga"
                required
              />

              <BaseSelect
                id="status_in_family"
                v-model="form.status_in_family"
                class="mt-1 block w-full"
                required
              >
                <option
                  v-for="status_in_family in STATUSES_IN_FAMILY"
                  :key="status_in_family"
                  :value="status_in_family"
                >
                  {{ STATUS_IN_FAMILY_TRANSLATIONS[status_in_family] }}
                </option>
              </BaseSelect>

              <InputError
                class="mt-2"
                :message="form.errors.status_in_family"
              />
            </div>

            <div class="mt-4">
              <InputLabel for="child_order" value="Anak ke-" required />

              <TextInput
                id="child_order"
                v-model="form.child_order"
                type="text"
                class="mt-1 block w-full"
                inputmode="numeric"
                required
              />

              <InputError class="mt-2" :message="form.errors.child_order" />
            </div>

            <div class="mt-4">
              <InputLabel for="father_name" value="Nama Lengkap Ayah" />

              <TextInput
                id="father_name"
                v-model="form.father_name"
                type="text"
                class="mt-1 block w-full"
              />

              <InputError class="mt-2" :message="form.errors.father_name" />
            </div>

            <div class="mt-4">
              <InputLabel for="father_phone_number" value="No. Telp Ayah" />

              <TextInput
                id="father_phone_number"
                v-model="form.father_phone_number"
                type="text"
                class="mt-1 block w-full"
              />

              <InputError
                class="mt-2"
                :message="form.errors.father_phone_number"
              />
            </div>

            <div class="mt-4">
              <InputLabel for="mother_name" value="Nama Lengkap Ibu" />

              <TextInput
                id="mother_name"
                v-model="form.mother_name"
                type="text"
                class="mt-1 block w-full"
              />

              <InputError class="mt-2" :message="form.errors.mother_name" />
            </div>

            <div class="mt-4">
              <InputLabel for="mother_phone_number" value="No. Telp Ibu" />

              <TextInput
                id="mother_phone_number"
                v-model="form.mother_phone_number"
                type="text"
                class="mt-1 block w-full"
              />

              <InputError
                class="mt-2"
                :message="form.errors.mother_phone_number"
              />
            </div>

            <div class="mt-4">
              <InputLabel for="guardian_name" value="Nama Lengkap Wali" />

              <TextInput
                id="guardian_name"
                v-model="form.guardian_name"
                type="text"
                class="mt-1 block w-full"
              />

              <InputError class="mt-2" :message="form.errors.guardian_name" />
            </div>

            <div class="mt-4">
              <InputLabel for="guardian_phone_number" value="No. Telp Wali" />

              <TextInput
                id="guardian_phone_number"
                v-model="form.guardian_phone_number"
                type="text"
                class="mt-1 block w-full"
              />

              <InputError
                class="mt-2"
                :message="form.errors.guardian_phone_number"
              />
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
