<script setup lang="ts">
import { TrashIcon } from '@heroicons/vue/24/solid';
import ActionButton from './ActionButton.vue';
import { ref } from 'vue';
import BaseModal from './BaseModal.vue';
import SecondaryButton from './SecondaryButton.vue';
import PrimaryButton from './PrimaryButton.vue';

const props = defineProps<{
  title: string;
  message: string;
  onDelete: () => void;
  loading?: boolean;
}>();

const showModal = ref(false);

const handleDelete = () => {
  props.onDelete();
  showModal.value = false;
};
</script>

<template>
  <ActionButton @click="showModal = true" :title="title">
    <TrashIcon class="w-4 h-4 text-red-400" />
  </ActionButton>

  <BaseModal :title="title" :open="showModal" @close="showModal = false">
    <div>
      {{ message }}
    </div>

    <div class="mt-4 flex justify-end gap-4 items-center">
      <SecondaryButton @click="showModal = false">Tidak</SecondaryButton>

      <PrimaryButton
        :class="{ 'opacity-25': loading }"
        :disabled="loading"
        @click="handleDelete"
      >
        Ya
      </PrimaryButton>
    </div>
  </BaseModal>
</template>
