<template>
    <Dialog :visible="props.visible" @update:visible="(val: boolean) => emit('update:visible', val)" modal
        :header="props.title" :style="{ width: '40rem' }">
        <span class="text-surface-500 dark:text-surface-400 block mb-8">{{ props.subtitle }}</span>
        <slot />
    </Dialog>
</template>

<script setup lang="ts">
const props = defineProps<{
    title: string
    subtitle?: string
    visible: boolean
}>()

const emit = defineEmits<{
    'update:visible': [value: boolean]
}>()
</script>