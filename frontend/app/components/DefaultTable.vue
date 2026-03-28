<template>
    <DataTable dataKey="id" :value="props.value" :loading="props.loading" tableStyle="min-width: 50rem">
        <Column
            v-for="col of props.columns"
            :key="col.field"
            :field="col.field"
            :header="col.header"
        />
    </DataTable>
</template>

<script setup lang="ts">
interface ColumnDefinition {
    field: string
    header: string
}

const props = defineProps<{
    value: any[]          
    loading: boolean    
    columns?: ColumnDefinition[] 
}>()
</script>