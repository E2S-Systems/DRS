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

        <p v-if="error" class="text-red-500">
            Failed to load users: {{ error.message }}
        </p>

        <DefaultTable v-else :value="data" :loading="pending" :columns="columns" :perPage="meta?.per_page ?? 10"
            :total="meta?.total ?? 0" :from="meta?.from ?? 0" :to="meta?.to ?? 0" :search="search" @page="onPageChange"
            @search="onSearch" />
    </main>
</template>

<script setup lang="ts">
definePageMeta({
    layout: 'configuration',
})

import { useUsers } from '~/Composables/useUsers'

const { data, meta, pending, error, onPageChange, onSearch, search } = useUsers()



const columns = [
    { field: 'id', header: 'ID' },
    { field: 'first_name', header: 'Nome' },
    { field: 'last_name', header: 'Sobrenome' },
    { field: 'email', header: 'E-mail' },
    { field: 'role', header: 'Perfil' },
    { field: 'is_active', header: 'Status' },
    { field: 'last_login_at', header: 'Último Acesso' },
]
</script>