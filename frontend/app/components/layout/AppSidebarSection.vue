<script setup lang="ts">
const props = defineProps<{ label: string, modalState: boolean }>()
</script>

<template>
  <div class="">
    <span class="text-text-muted tracking-wide m-2" v-if="props.modalState">{{ label }}</span>
    <slot />
  </div>
</template>