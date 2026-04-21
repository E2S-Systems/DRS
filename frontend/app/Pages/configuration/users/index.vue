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
                <button class="bg-primary hover:bg-primary-hover w-60 h-full cursor-pointer" @click="openCreateModal">
                    <i class="pi pi-plus m-2" style="font-size: 0.8rem" />
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

        <DefaultTable v-else :value="data" :loading="pending" :perPage="meta?.per_page ?? 10" :total="meta?.total ?? 0"
            model="usuários" :from="meta?.from ?? 0" :to="meta?.to ?? 0" :search="search" @page="onPageChange"
            @search="onSearch">
            <Column field="id" header="ID" />
            <Column field="first_name" header="Nome" />
            <Column field="last_name" header="Sobrenome" />
            <Column field="email" header="E-mail" />
            <Column field="role" header="Perfil" />

            <Column field="is_active" header="Status">
                <template #body="{ data: user }">
                    <span class="px-2 py-1 rounded-full text-xs font-bold tracking-wider"
                        :class="user.is_active ? 'text-[#00C407]' : 'text-text-muted'">
                        {{ user.is_active ? 'ATIVO' : 'INATIVO' }}
                    </span>
                </template>
            </Column>

            <Column field="last_login_at" header="Último Acesso">
                <template #body="{ data: user }">
                    {{ formatDate(user.last_login_at) }}
                </template>
            </Column>

            <Column field="created_by" header="Criado por">
                <template #body="{ data: user }">
                    <span v-if="user.created_by">{{ user.created_by }}</span>
                    <span v-else class="text-text-muted text-xs">—</span>
                </template>
            </Column>

            <ConfirmDialog :draggable="false" :blockScroll="true" />

            <Column field="actions" header="Ações">
                <template #body="{ data: user }">
                    <div class="flex items-center gap-2">
                        <Button icon="pi pi-pen-to-square" v-tooltip.top="'Editar usuário'"
                            @click="openEditModal(user)" />
                        <Button severity="danger" icon="pi pi-trash" v-tooltip.top="'Excluir usuário'"
                            @click="confirmDelete(user)" />
                    </div>
                </template>
            </Column>
        </DefaultTable>

        <UserModal v-model:visible="modalVisible" :user="selectedUser" @saved="onModalSaved" @close="onModalClose" />

    </main>
</template>

<script setup lang="ts">
import type { User } from '~/types/user'
import { useUsers } from '~/Composables/useUsers'

definePageMeta({
    layout: 'configuration',
    // middleware: ['sanctum:auth'],
})

useHead({ title: 'Configurações - Usuários' })

const confirm = useConfirm()

const {
    data,
    meta,
    counts,
    pending,
    error,
    search,
    status,
    deleteUser,
    refresh,
    onPageChange,
    onSearch,
    onStatusChange,
} = useUsers()

const tabs = computed(() => [
    { value: '' as const, label: 'TODOS ', count: counts.value?.total ?? 0 },
    { value: true as const, label: 'ATIVOS ', count: counts.value?.active ?? 0 },
    { value: false as const, label: 'INATIVOS ', count: counts.value?.inactive ?? 0 },
])

const modalVisible = ref(false)

const selectedUser = ref<User | null>(null)

const openCreateModal = () => {
    selectedUser.value = null
    modalVisible.value = true
}

const openEditModal = (user: User) => {
    selectedUser.value = user
    modalVisible.value = true
}

const onModalSaved = () => {
    refresh()
}

const onModalClose = () => {
    selectedUser.value = null
}

const confirmDelete = (user: User) => {
    confirm.require({
        message: `Você deseja mesmo excluir o usuário ${user.first_name}?`,
        header: 'CONFIRMAR EXCLUSÃO',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'CANCELAR',
        rejectProps: { label: 'CANCELAR', severity: 'secondary', outlined: true },
        acceptProps: { label: 'EXCLUIR', severity: 'primary' },
        accept: () => deleteUser(user.id),
        reject: () => { },
    })
}

function formatDate(dateString: string | null): string {
    if (!dateString) return '—'

    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
    }).format(new Date(dateString))
}
</script>