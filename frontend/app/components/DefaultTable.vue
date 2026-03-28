<template>
    <DataTable dataKey="id" :value="data" :loading="pending" tableStyle="min-width: 50rem">
        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" />
    </DataTable>
</template>

<script setup lang="ts">
interface ColumnDefinition {
    field: string
    header: string
}

const props = defineProps<{
    columns: ColumnDefinition[]
}>()
</script>