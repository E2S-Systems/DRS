<template>
    <Dialog :visible="props.visible" @update:visible="(val: boolean) => emit('update:visible', val)" modal
        :draggable="false" :blockScroll="true" :style="{ width: '42rem' }">
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="text-base font-semibold uppercase tracking-widest">
                    {{ props.title }}
                </h2>
                <p v-if="props.subtitle" class="text-sm text-surface-400">
                    {{ props.subtitle }}
                </p>
            </div>
        </template>
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