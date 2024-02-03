<script setup lang="ts">
defineProps<{
  value?: string;
  required?: boolean;
}>();
</script>

<template>
  <label class="block font-medium text-sm text-gray-700">
    <div v-if="value" class="flex items-center gap-1">
      <span>
        {{ value }}
      </span>

      <span v-if="required" class="text-red-500">*</span>
    </div>
    <span v-else><slot /></span>
  </label>
</template>
