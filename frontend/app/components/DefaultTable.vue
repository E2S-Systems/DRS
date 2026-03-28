<template>
    <DataTable dataKey="id" :value="props.value" :loading="props.loading" tableStyle="min-width: 50rem"
        lazy paginator :rows="props.perPage" :totalRecords="props.total" @page="emit('page', $event)">
        <template #paginatorstart>
            <span class="text-text-muted text-sm">Mostrando {{ props.from }}-{{ props.to }} de {{ props.total }}
                usuários</span>
        </template>
        <Column v-for="col of props.columns" :key="col.field" :field="col.field" :header="col.header" />
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
    perPage: number
    total: number
    from: number
    to: number
}>()

const emit = defineEmits<{
    page: [event: { page: number, rows: number }]
}>()
</script>