<script setup lang="ts">
import { onMounted } from 'vue';
import { ref } from 'vue';

const model = defineModel<string | number | null>({ required: false });

const input = ref<HTMLSelectElement | null>(null);

onMounted(() => {
  if (input.value?.hasAttribute('autofocus')) {
    input.value?.focus();
  }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
  <select
    class="text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
    v-model="model"
    ref="input"
  >
    <slot />
  </select>
</template>
