<script setup lang="ts">
const props = defineProps<{ label: string }>()
</script>

<template>
  <div class="">
    <span class="text-text-muted tracking-wide m-2">{{ label }}</span>
    <slot />
  </div>
</template>