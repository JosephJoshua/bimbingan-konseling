<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
  ChevronUpDownIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  PencilIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
} from '@heroicons/vue/24/solid';
import { PaginatedResult } from '@/types/paginated-result';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { format } from 'date-fns';
import { Consultation } from '@/types/consultation';
import { WithStudent } from '@/types/student';
import { WithConsultationCategory } from '@/types/consultation-category';
import { id } from 'date-fns/locale/id';
import ActionButton from '@/Components/ActionButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import extractContentFromHtml from '@/utils/extract-content-from-html';
import ellipsis from '@/utils/ellipsis';

const props = defineProps<{
  data: PaginatedResult<WithStudent<WithConsultationCategory<Consultation>>>;
  search: string;
  sort_by: string;
  sort_direction: 'asc' | 'desc';
}>();

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
  newUrl.searchParams.set('search', query);

  router.get(newUrl, undefined, {
    only: ['data', 'search'],
    replace: true,
    preserveState: true,
    preserveScroll: true,
  });
};

const handleDelete = (id: number) => {
  deleteForm.delete(route('consultations.destroy', { consultation: id }), {
    onSuccess: () => {
      router.reload({ only: ['data'] });
    },
  });
};
</script>

<template>
  <Head title="Daftar Konsultasi" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center gap-4">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Daftar Konsultasi
        </h2>
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
                      class="whitespace-nowrap flex justify-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('students.full_name')"
                    >
                      Nama Murid

                      <component
                        :is="getSortIcon('students.full_name')"
                        class="w-4"
                      />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="whitespace-nowrap flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('consultation_categories.name')"
                    >
                      Kategori

                      <component
                        :is="getSortIcon('consultation_categories.name')"
                        class="w-4"
                      />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="whitespace-nowrap flex items-center text-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('consultation_date')"
                    >
                      Tanggal Konsultasi

                      <component
                        :is="getSortIcon('consultation_date')"
                        class="w-4"
                      />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="whitespace-nowrap flex items-center gap-1 transition-all duration-200 hover:brightness-125"
                    >
                      Deskripsi
                    </div>
                  </th>

                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="consultation in data.data"
                  :key="consultation.id"
                  class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                >
                  <td
                    class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap dark:text-white"
                  >
                    {{ consultation.student.full_name }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    <span
                      class="px-2 py-1 text-xs font-semibold leading-none text-green-800 bg-green-100 rounded-full"
                    >
                      {{ consultation.consultation_category.name }}
                    </span>
                  </td>

                  <th class="px-6 py-4 whitespace-nowrap">
                    {{
                      format(consultation.consultation_date, 'd MMMM y', {
                        locale: id,
                      })
                    }}
                  </th>

                  <td class="px-6 py-4">
                    {{
                      ellipsis(
                        extractContentFromHtml(consultation.description),
                        50,
                      )
                    }}
                  </td>

                  <td class="px-6 py-4">
                    <div class="flex justify-end items-center gap-2">
                      <Link
                        :href="
                          route('consultations.show', {
                            consultation: consultation.id,
                          })
                        "
                      >
                        <ActionButton title="Lihat">
                          <EyeIcon class="w-4 h-4 text-blue-500" />
                        </ActionButton>
                      </Link>

                      <Link
                        :href="
                          route('consultations.edit', {
                            consultation: consultation.id,
                          })
                        "
                      >
                        <ActionButton title="Ubah">
                          <PencilIcon class="w-4 h-4 text-orange-400" />
                        </ActionButton>
                      </Link>

                      <DeleteButton
                        title="Hapus Konsultasi"
                        message="Apakah Anda yakin ingin menghapus konsultasi ini?"
                        :loading="deleteForm.processing"
                        @delete="() => handleDelete(consultation.id)"
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
  </AuthenticatedLayout>
</template>
