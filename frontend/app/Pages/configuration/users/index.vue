<template>
    <main class="pt-14 px-4 w-full max-w-full">
        <div class="flex justify-between">
            <div>
                <span class="font-bold tracking-wider text-xl block">USUÁRIOS</span>
                <span class="text-text-muted tracking-widest text-sm block">
                    Gerencie os usuários com acesso ao sistema
                </span>
            </div>
            <div>
                <button class="bg-primary w-60 h-full">
                    <i class="pi pi-plus" style="font-size: 0.8rem" />
                    <span class="tracking-wider text-xl">NOVO USUÁRIO</span>
                </button>
            </div>
        </div>

        <div class="flex justify-items-stretch py-8">
            <button v-for="tab in tabs" :key="String(tab.value)"
                class="font-bold w-full border-b-2 border-x-0 border-t-0 py-2 transition-colors" :class="status === tab.value
                    ? 'border-b-primary text-text'
                    : 'border-b-transparent text-text-muted hover:border-b-text-hint hover:text-text'
                    " @click="onStatusChange(tab.value)">
                {{ tab.label }}({{ tab.count }})
            </button>
        </div>

        <p v-if="error" class="text-red-500">
            Erro ao carregar usuários: {{ error.message }}
        </p>

        <DefaultTable v-else :value="data" :loading="pending" :perPage="meta?.per_page ?? 10" :total="meta?.total ?? 0" model="usuários"
            :from="meta?.from ?? 0" :to="meta?.to ?? 0" :search="search" @page="onPageChange" @search="onSearch">
            <Column field="id" header="ID" />
            <Column field="first_name" header="Nome" />
            <Column field="last_name" header="Sobrenome" />
            <Column field="email" header="E-mail" />
            <Column field="role" header="Perfil" />

            <Column field="is_active" header="Status">
                <template #body="{ data: user }">
                    <span class="px-2 py-1 rounded-full text-xs font-bold tracking-wider" :class="user.is_active
                        ? 'text-[#00C407]'
                        : 'text-text-muted'">
                        {{ user.is_active ? 'ATIVO' : 'INATIVO' }}
                    </span>
                </template>
            </Column>

            <Column field="last_login_at" header="Último Acesso">
                <template #body="{ data: user }">
                    {{ formatDate(user.last_login_at) }}
                </template>
            </Column>
        </DefaultTable>
    </main>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'configuration' })

import { useUsers } from '~/Composables/useUsers'

const { data, meta, counts, pending, error, search, status, onPageChange, onSearch, onStatusChange } = useUsers()

const tabs = computed(() => [
    { value: '' as const, label: 'TODOS ', count: counts.value?.total ?? 0 },
    { value: true as const, label: 'ATIVOS ', count: counts.value?.active ?? 0 },
    { value: false as const, label: 'INATIVOS ', count: counts.value?.inactive ?? 0 },
])

// considerar extrair para um composable useDateFormat
function formatDate(dateString: string | null): string {
    if (!dateString) return '—'

    const date = new Date(dateString)

    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
    }).format(date)
}
</script>