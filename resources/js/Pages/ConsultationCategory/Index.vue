<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { PencilIcon, EyeIcon } from '@heroicons/vue/24/solid';
import ActionButton from '@/Components/ActionButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ConsultationCategory } from '@/types/consultation-category';

defineProps<{
  data: ConsultationCategory[];
}>();

const deleteForm = useForm({});

const handleDelete = (id: number) => {
  deleteForm.delete(
    route('consultation-categories.destroy', { consultation_category: id }),
    {
      onSuccess: () => {
        router.reload({ only: ['data'] });
      },
    },
  );
};
</script>

<template>
  <Head title="Daftar Kategori Konsultasi" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center gap-4">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Daftar Kategori Konsultasi
        </h2>

        <Link :href="route('consultation-categories.create')">
          <SecondaryButton>Tambah Kategori</SecondaryButton>
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
          <div
            class="relative min-h-[calc(95vh-200px)] overflow-x-auto shadow-md sm:rounded-lg flex flex-col"
          >
            <table
              class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
              <thead
                class="text-xs text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
              >
                <tr>
                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex justify-center text-center items-center gap-1 transition-all duration-200 hover:brightness-125"
                    >
                      #
                    </div>
                  </th>

                  <th scope="col" class="px-6 py-3">
                    <div
                      class="flex justify-center text-center items-center gap-1 cursor-pointer transition-all duration-200 hover:brightness-125"
                    >
                      Nama Kategori
                    </div>
                  </th>

                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(category, idx) in data"
                  :key="category.id"
                  class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                >
                  <td class="px-6 py-4 text-center">
                    {{ idx + 1 }}
                  </td>

                  <th
                    scope="row"
                    class="text-center px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                  >
                    {{ category.name }}
                  </th>

                  <td class="px-6 py-4">
                    <div class="flex justify-end items-center gap-2">
                      <Link
                        :href="
                          route('consultation-categories.show', {
                            consultation_category: category.id,
                          })
                        "
                      >
                        <ActionButton title="Lihat">
                          <EyeIcon class="w-4 h-4 text-blue-500" />
                        </ActionButton>
                      </Link>

                      <Link
                        :href="
                          route('consultation-categories.edit', {
                            consultation_category: category.id,
                          })
                        "
                      >
                        <ActionButton title="Ubah">
                          <PencilIcon class="w-4 h-4 text-orange-400" />
                        </ActionButton>
                      </Link>

                      <DeleteButton
                        title="Hapus Kategori"
                        message="Apakah Anda yakin ingin menghapus kategori konsultasi ini?"
                        :loading="deleteForm.processing"
                        @delete="() => handleDelete(category.id)"
                      />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
