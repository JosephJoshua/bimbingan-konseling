<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
  ChevronUpDownIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  EyeIcon,
  ChatBubbleLeftRightIcon,
} from '@heroicons/vue/24/solid';
import { Student } from '@/types/student';
import { PaginatedResult } from '@/types/paginated-result';
import { GENDER_TRANSLATIONS } from '@/types/gender';
import { RELIGION_TRANSLATIONS } from '@/types/religion';
import ActionButton from '@/Components/ActionButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import BaseModal from '@/Components/BaseModal.vue';
import { ref } from 'vue';
import { parseISO } from 'date-fns';
import ellipsis from '@/utils/ellipsis';
import { WithConsultations } from '@/types/consultation';

const props = defineProps<{
  data: PaginatedResult<WithConsultations<Student>>;
  search: string;
  sort_by: string;
  sort_direction: 'asc' | 'desc';
}>();

const currentlyViewingStudent = ref<WithConsultations<Student> | null>(null);
const deleteForm = useForm({});

const getSortIcon = (column: string) => {
  const isSorting = props.sort_by === column;

  if (!isSorting) return ChevronUpDownIcon;
  if (props.sort_direction === 'asc') return ChevronUpIcon;

  return ChevronDownIcon;
};

const handleSort = (column: string) => {
  const newUrl = new URL(decodeURIComponent(window.location.href));

  if (props.sort_by === column) {
    newUrl.searchParams.set(
      'sort_direction',
      props.sort_direction === 'asc' ? 'desc' : 'asc',
    );
  } else {
    newUrl.searchParams.set('sort_by', column);
    newUrl.searchParams.set('sort_direction', 'asc');
  }

  router.get(newUrl, undefined, {
    replace: true,
    preserveState: true,
  });
};

const handleSelectPage = (page: number | string) => {
  const newUrl = new URL(decodeURIComponent(window.location.href));
  newUrl.searchParams.set('page', page.toString());

  router.get(newUrl, undefined, {
    only: ['data'],
    replace: true,
    preserveState: true,
  });
};

const handleSearch = (query: string) => {
  const newUrl = new URL(decodeURIComponent(window.location.href));

  newUrl.searchParams.set('page', '1');
  newUrl.searchParams.set('search', query.trim());

  router.get(newUrl, undefined, {
    only: ['data', 'search'],
    replace: true,
    preserveState: true,
    preserveScroll: true,
  });
};

const handleDelete = (id: number) => {
  deleteForm.delete(route('students.destroy', { student: id }), {
    onSuccess: () => {
      router.reload({ only: ['data'] });
    },
  });
};
</script>

<template>
  <Head title="Daftar Murid" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center gap-4">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Daftar Murid
        </h2>

        <Link :href="route('students.create')">
          <SecondaryButton>Tambah Murid</SecondaryButton>
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="m-4">
              <label for="table-search" class="sr-only">Cari</label>
              <div class="relative mt-1">
                <div
                  class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none"
                >
                  <MagnifyingGlassIcon class="w-4 h-4" />
                </div>

                <input
                  type="text"
                  id="table-search"
                  class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="Cari..."
                  :value="search"
                  @input="(e) => handleSearch((e.target as HTMLInputElement).value)"
                />
              </div>
            </div>

            <table
              class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
              <thead
                class="text-xs text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
              >
                <tr>
                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('nisn')"
                    >
                      NISN

                      <component :is="getSortIcon('nisn')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('nis')"
                    >
                      NIS

                      <component :is="getSortIcon('nis')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex items-center text-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('full_name')"
                    >
                      Nama Lengkap

                      <component :is="getSortIcon('full_name')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('gender')"
                    >
                      Jenis Kelamin

                      <component :is="getSortIcon('gender')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('religion')"
                    >
                      Agama

                      <component :is="getSortIcon('religion')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('phone_number')"
                    >
                      No Telp.

                      <component
                        :is="getSortIcon('phone_number')"
                        class="w-4"
                      />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('email')"
                    >
                      E-mail

                      <component :is="getSortIcon('email')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex items-center text-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('full_address')"
                    >
                      Alamat Lengkap

                      <component
                        :is="getSortIcon('full_address')"
                        class="w-4"
                      />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex items-center text-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('origin_school')"
                    >
                      Asal Sekolah

                      <component
                        :is="getSortIcon('origin_school')"
                        class="w-4"
                      />
                    </div>
                  </th>

                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="student in data.data"
                  :key="student.id"
                  class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                >
                  <td class="px-6 py-4 text-center">
                    {{ student.nisn }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ student.nis }}
                  </td>

                  <th
                    scope="row"
                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                  >
                    {{ student.full_name }}
                  </th>

                  <td class="px-6 py-4 text-center">
                    {{ GENDER_TRANSLATIONS[student.gender] }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ RELIGION_TRANSLATIONS[student.religion] }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ student.phone_number }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ student.email ?? '-' }}
                  </td>

                  <td class="px-6 py-4">
                    {{ ellipsis(student.full_address, 50) }}
                  </td>

                  <td class="px-6 py-4">
                    {{ student.origin_school }}
                  </td>

                  <td class="px-6 py-4">
                    <div class="flex justify-end items-center gap-2">
                      <ActionButton
                        title="Lihat"
                        @click="() => (currentlyViewingStudent = student)"
                      >
                        <EyeIcon class="w-4 h-4 text-blue-500" />
                      </ActionButton>

                      <Link
                        :href="route('students.edit', { student: student.id })"
                      >
                        <ActionButton title="Ubah">
                          <PencilIcon class="w-4 h-4 text-orange-400" />
                        </ActionButton>
                      </Link>

                      <Link
                        :href="
                          route('students.consultations.create', {
                            student: student.id,
                          })
                        "
                      >
                        <ActionButton title="Konsultasi">
                          <ChatBubbleLeftRightIcon
                            class="w-4 h-4 text-green-600"
                          />
                        </ActionButton>
                      </Link>

                      <DeleteButton
                        title="Hapus Murid"
                        message="Apakah Anda yakin ingin menghapus murid ini?"
                        :loading="deleteForm.processing"
                        @delete="() => handleDelete(student.id)"
                      />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <nav
              class="flex items-center flex-column flex-wrap md:flex-row justify-between m-4"
              aria-label="Table navigation"
            >
              <span
                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto"
              >
                Menampilkan
                <span class="font-semibold text-gray-900 dark:text-white">
                  {{ data.from }}-{{ data.to }}
                </span>
                dari
                <span class="font-semibold text-gray-900 dark:text-white">
                  {{ data.total }}
                </span>
              </span>

              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <select
                    class="py-0.5 text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    @change="(e: Event) => handleSelectPage((e.target as HTMLSelectElement).value)"
                  >
                    <option
                      v-for="page in Array.from(
                        { length: data.last_page },
                        (_, i) => i + 1,
                      )"
                      :key="page"
                      :value="page"
                      :selected="data.current_page === page"
                    >
                      {{ page }}
                    </option>
                  </select>

                  <div class="text-sm">
                    <span class="text-gray-500 dark:text-gray-400">dari </span>
                    <span class="text-gray-900 dark:text-white">
                      {{ data.last_page }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400">
                      halaman
                    </span>
                  </div>
                </div>

                <div
                  class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8"
                >
                  <Link
                    v-if="data.prev_page_url"
                    :href="data.prev_page_url"
                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                    replace
                  >
                    <ChevronLeftIcon class="w-4 h-4" />
                  </Link>

                  <Link
                    v-if="data.next_page_url"
                    :href="data.next_page_url"
                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                    replace
                  >
                    <ChevronRightIcon class="w-4 h-4" />
                  </Link>
                </div>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <BaseModal
      :open="!!currentlyViewingStudent"
      :title="currentlyViewingStudent?.full_name ?? ''"
      @close="() => (currentlyViewingStudent = null)"
    >
      <ul v-if="!!currentlyViewingStudent">
        <li>
          <span class="font-medium">ID:</span>
          {{ currentlyViewingStudent.id }}
        </li>
        <li>
          <span class="font-medium">NISN:</span>
          {{ currentlyViewingStudent.nisn }}
        </li>
        <li>
          <span class="font-medium">NIS:</span>
          {{ currentlyViewingStudent.nis }}
        </li>
        <li>
          <span class="font-medium">Nama Lengkap:</span>
          {{ currentlyViewingStudent.full_name }}
        </li>
        <li>
          <span class="font-medium">Jenis Kelamin:</span>
          {{ GENDER_TRANSLATIONS[currentlyViewingStudent.gender] }}
        </li>
        <li>
          <span class="font-medium">Agama:</span>
          {{ RELIGION_TRANSLATIONS[currentlyViewingStudent.religion] }}
        </li>
        <li>
          <span class="font-medium">Tanggal/Tempat Lahir:</span>
          {{
            parseISO(currentlyViewingStudent.birth_date).toLocaleDateString(
              'id-ID',
              {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
              },
            )
          }}
          / {{ currentlyViewingStudent.birth_place }}
        </li>
        <li>
          <span class="font-medium">Alamat Lengkap:</span>
          {{ currentlyViewingStudent.full_address }}
        </li>
        <li>
          <span class="font-medium">No. Telp:</span>
          {{ currentlyViewingStudent.phone_number }}
        </li>
        <li>
          <span class="font-medium">E-mail:</span>
          {{ currentlyViewingStudent.email ?? '-' }}
        </li>
        <li>
          <span class="font-medium">Asal Sekolah:</span>
          {{ currentlyViewingStudent.origin_school }}
        </li>
        <li v-if="!!currentlyViewingStudent.father_name">
          <span class="font-medium">Nama Ayah:</span>
          {{ currentlyViewingStudent.father_name }}
        </li>
        <li v-if="!!currentlyViewingStudent.father_name">
          <span class="font-medium">No Telp. Ayah:</span>
          {{ currentlyViewingStudent.father_phone_number ?? '-' }}
        </li>
        <li v-if="!!currentlyViewingStudent.mother_name">
          <span class="font-medium">Nama Ibu:</span>
          {{ currentlyViewingStudent.mother_name }}
        </li>
        <li v-if="!!currentlyViewingStudent.mother_name">
          <span class="font-medium">No Telp. Ibu:</span>
          {{ currentlyViewingStudent.mother_phone_number ?? '-' }}
        </li>
        <li v-if="!!currentlyViewingStudent.guardian_name">
          <span class="font-medium">Nama Wali:</span>
          {{ currentlyViewingStudent.guardian_name }}
        </li>
        <li v-if="!!currentlyViewingStudent.guardian_name">
          <span class="font-medium">No Telp. Wali:</span>
          {{ currentlyViewingStudent.guardian_phone_number ?? '-' }}
        </li>

        <li class="mt-8">
          <div class="font-semibold text-lg mb-1">Riwayat Konsultasi</div>
          <div v-if="currentlyViewingStudent.consultations.length === 0">
            Belum terdapat riwayat konsultasi.
          </div>
        </li>
      </ul>
    </BaseModal>
  </AuthenticatedLayout>
</template>
