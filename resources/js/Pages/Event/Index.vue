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
import { endOfDay, format, formatDate, startOfDay } from 'date-fns';
import { id } from 'date-fns/locale/id';
import ActionButton from '@/Components/ActionButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Event as AppEvent } from '@/types/event';
import extractContentFromHtml from '@/utils/extract-content-from-html';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ellipsis from '@/utils/ellipsis';
import BaseSelect from '@/Components/BaseSelect.vue';
import VueTailwindDatePicker from 'vue-tailwind-datepicker';
import { computed } from 'vue';
import { DeprecationTypes } from 'vue';

const props = defineProps<{
  data: PaginatedResult<AppEvent & { status: 'upcoming' | 'done' }>;
  search: string;
  sort_by: string;
  sort_direction: 'asc' | 'desc';
  status_filter: 'all' | 'upcoming' | 'done';
  start_date?: string;
  end_date?: string;
}>();

const dateFilters = computed(() => {
  return {
    startDate:
      props.start_date == null
        ? ''
        : formatDate(new Date(props.start_date), 'dd MMM yyyy'),
    endDate:
      props.end_date == null
        ? ''
        : formatDate(new Date(props.end_date), 'dd MMM yyyy'),
  };
});

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

const handleFilterStatus = (filter: string) => {
  const newUrl = new URL(decodeURIComponent(window.location.href));

  newUrl.searchParams.set('page', '1');
  newUrl.searchParams.set('status', filter);

  router.get(newUrl, undefined, {
    only: ['data', 'status_filter'],
    replace: true,
    preserveState: true,
    preserveScroll: true,
  });
};

const handleFilterDate = (dates: { startDate: string; endDate: string }) => {
  const { startDate, endDate } = dates;

  const newUrl = new URL(decodeURIComponent(window.location.href));

  newUrl.searchParams.set('page', '1');
  newUrl.searchParams.set('start_date', startDate);
  newUrl.searchParams.set('end_date', endDate);

  router.get(newUrl, undefined, {
    only: ['data', 'start_date', 'end_date'],
    replace: true,
    preserveState: true,
    preserveScroll: true,
  });
};

const handleDelete = (id: number) => {
  deleteForm.delete(route('events.destroy', { event: id }), {
    onSuccess: () => {
      router.reload({ only: ['data'] });
    },
  });
};
</script>

<template>
  <Head title="Daftar Kegiatan" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center gap-4">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Daftar Kegiatan
        </h2>

        <Link :href="route('events.create')">
          <SecondaryButton>Tambah Kegiatan</SecondaryButton>
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
          <div
            class="relative min-h-[calc(95vh-200px)] overflow-x-auto shadow-md sm:rounded-lg flex flex-col"
          >
            <div class="m-4 flex items-center gap-4">
              <div>
                <label for="table-search" class="sr-only">Cari</label>
                <div class="relative">
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
                    :value="search ?? ''"
                    @input="(e) => handleSearch((e.target as HTMLInputElement).value)"
                  />
                </div>
              </div>

              <BaseSelect
                :model-value="status_filter"
                @update:model-value="handleFilterStatus"
              >
                <option value="all">Semua</option>
                <option value="upcoming">Mendatang</option>
                <option value="done">Sudah Selesai</option>
              </BaseSelect>

              <VueTailwindDatePicker
                placeholder="Pilih tanggal"
                separator=" - "
                :options="{
                  shortcuts: {
                    today: 'Hari ini',
                    yesterday: 'Kemarin',
                    past: (period) => `${period} hari terakhir`,
                    currentMonth: 'Bulan ini',
                    pastMonth: 'Bulan lalu',
                  },
                  footer: {
                    apply: 'Terapkan',
                    cancel: 'Batal',
                  },
                }"
                :formatter="{
                  date: 'DD MMM YYYY',
                  month: 'MMM',
                }"
                :model-value="dateFilters"
                @update:model-value="handleFilterDate"
              />
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
                      @click="() => handleSort('event_date')"
                    >
                      Tanggal Kegiatan

                      <component :is="getSortIcon('event_date')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="whitespace-nowrap flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('event_time')"
                    >
                      Waktu Kegiatan

                      <component :is="getSortIcon('event_time')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="whitespace-nowrap flex items-center text-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                      @click="() => handleSort('title')"
                    >
                      Nama Kegiatan

                      <component :is="getSortIcon('title')" class="w-4" />
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="whitespace-nowrap flex items-center gap-1 transition-all duration-200 hover:brightness-125"
                    >
                      Deskripsi
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="whitespace-nowrap flex justify-center text-center items-center gap-1 transition-all duration-200 hover:brightness-125"
                    >
                      Status
                    </div>
                  </th>

                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="event in data.data"
                  :key="event.id"
                  class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                >
                  <td class="px-6 py-4 text-center">
                    {{ format(event.event_date, 'd MMMM y') }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ event.event_time }}
                  </td>

                  <th
                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                  >
                    {{ event.title }}
                  </th>

                  <td class="px-6 py-4">
                    {{
                      ellipsis(extractContentFromHtml(event.description), 50)
                    }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    <span
                      :class="{
                        'bg-green-100 text-green-800 dark:bg-green-500 dark:text-green-100':
                          event.status === 'done',
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-500 dark:text-yellow-100':
                          event.status === 'upcoming',
                      }"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{
                        event.status === 'done' ? 'Sudah Selesai' : 'Mendatang'
                      }}
                    </span>
                  </td>

                  <td class="px-6 py-4">
                    <div class="flex justify-end items-center gap-2">
                      <Link
                        :href="
                          route('events.show', {
                            event: event.id,
                          })
                        "
                      >
                        <ActionButton title="Lihat">
                          <EyeIcon class="w-4 h-4 text-blue-500" />
                        </ActionButton>
                      </Link>

                      <Link
                        :href="
                          route('events.edit', {
                            event: event.id,
                          })
                        "
                      >
                        <ActionButton title="Ubah">
                          <PencilIcon class="w-4 h-4 text-orange-400" />
                        </ActionButton>
                      </Link>

                      <DeleteButton
                        title="Hapus Kegiatan"
                        message="Apakah Anda yakin ingin menghapus kegiatan ini?"
                        :loading="deleteForm.processing"
                        @delete="() => handleDelete(event.id)"
                      />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="flex-1 flex items-end">
              <nav
                class="flex flex-1 flex-wrap items-center justify-between m-4"
                aria-label="Table navigation"
              >
                <span
                  class="text-sm font-normal text-gray-500 dark:text-gray-400"
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
                      <span class="text-gray-500 dark:text-gray-400"
                        >dari
                      </span>
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
    </div>
  </AuthenticatedLayout>
</template>
