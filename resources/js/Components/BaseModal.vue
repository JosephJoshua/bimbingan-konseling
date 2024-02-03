<script setup lang="ts">
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue';
import { XMarkIcon } from '@heroicons/vue/24/solid';

defineProps<{
  open?: boolean;
  onClose?: () => void;
  title: string;
}>();
</script>

<template>
  <TransitionRoot appear :show="open" as="template">
    <Dialog as="div" @close="onClose" class="relative z-10">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/50" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div
          class="flex min-h-full items-center justify-center p-4 text-center"
        >
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 text-left align-middle shadow-xl transition-all"
            >
              <DialogTitle
                as="h3"
                class="text-xl font-semibold leading-6 text-gray-800 dark:text-gray-200"
              >
                <div class="flex justify-between gap-4 items-center">
                  <span>
                    {{ title }}
                  </span>

                  <button
                    class="rounded-full -me-2 p-2 transition-colors duration-200 hover:bg-white/10"
                    @click="onClose"
                  >
                    <XMarkIcon class="w-6 h-6" />
                  </button>
                </div>
              </DialogTitle>

              <div class="mt-6 text-gray-800 dark:text-gray-200">
                <slot />
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
