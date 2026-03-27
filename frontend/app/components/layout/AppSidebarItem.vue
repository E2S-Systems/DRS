<template>
  <NuxtLink
    :to="to"
    class="text-text-muted hover:text-white text-md px-4 py-2"
    active-class="bg-bg-panel text-white border-l-2 border-primary"
  >
    <i :class="`pi pi-${icon}`" v-tooltip.right="!props.modalState ? label : null"/>
    <span class="px-3" v-if="props.modalState">{{ label }}</span>
  </NuxtLink>
</template>

<script setup lang="ts">
const props = defineProps<{
  label: string
  icon: string
  to: string
  modalState: boolean
}>()

</script>
