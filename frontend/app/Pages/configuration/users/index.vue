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
                <button class="bg-primary w-60 h-full cursor-pointer" @click="visible = true">
                    <i class="pi pi-plus m-2" style="font-size: 0.8rem" />
                    <span class="tracking-wider text-xl">NOVO USUÁRIO</span>
                </button>
            </div>
        </div>

        <DefaultModal :title="title" :subtitle="subtitle" v-model:visible="visible">

            <div class="flex items-center gap-4 mb-4">
                <label for="first_name" class="font-semibold w-24">PRIMEIRO NOME</label>
                <InputText id="first_name" v-model="form.first_name" class="flex-auto" type="text" autocomplete="off"
                    placeholder="Ex.: Rafael" />
                <small v-if="fieldErrors.first_name" class="text-red-500 mt-1">
                    {{ fieldErrors.first_name }}
                </small>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="last_name" class="font-semibold w-24">SOBRENOME</label>
                <InputText id="last_name" v-model="form.last_name" class="flex-auto" type="text" autocomplete="off"
                    placeholder="Ex.: Silva dos Santos" />
                    <small v-if="fieldErrors.last_name" class="text-red-500 mt-1">
                    {{ fieldErrors.last_name }}
                </small>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="email" class="font-semibold w-24">EMAIL</label>
                <InputText id="email" v-model="form.email" class="flex-auto" type="text" autocomplete="off"
                    placeholder="Ex.: rafael.silva@organizacao.com" />
                    <small v-if="fieldErrors.email" class="text-red-500 mt-1">
                    {{ fieldErrors.email }}
                </small>
            </div>
            <div class="flex items-center gap-4 mb-8">
                <label for="password" class="font-semibold w-24">SENHA TEMPORÁRIA</label>
                <InputText id="password" v-model="form.password" class="flex-auto" type="password" autocomplete="off" />
                <small v-if="fieldErrors.password" class="text-red-500 mt-1">
                    {{ fieldErrors.password }}
                </small>
            </div>
            <div class="flex items-center gap-4 mb-8">
                <label for="password_confirmation" class="font-semibold w-24">CONFIRME A SENHA</label>
                <InputText id="password_confirmation" v-model="form.password_confirmation" class="flex-auto"
                    type="password" autocomplete="off" />
            </div>

            <div class="flex items-center gap-4 mb-8">
                <Select v-model="form.role" :options="roles" optionLabel="name" optionValue="code"
                    placeholder="Selecione o papel/cargo" class="w-full md:w-56" />
                <small v-if="fieldErrors.role" class="text-red-500 mt-1">
                    {{ fieldErrors.role }}
                </small>
            </div>


            <div class="flex justify-end gap-2">
                <Button type="button" severity="secondary" :disabled="isCreating" @click="onCancel">
                    CANCELAR
                </Button>
                <Button type="button" :loading="isCreating" @click="onSubmit">
                    SALVAR USUÁRIO
                </Button>
            </div>
        </DefaultModal>

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
import { useFormErrors } from '~/Composables/useFormErrors'

const { data, meta, counts, pending, error,
    search, status,
    isCreating,
    createUser,
    onPageChange, onSearch, onStatusChange } = useUsers()

const tabs = computed(() => [
    { value: '' as const, label: 'TODOS ', count: counts.value?.total ?? 0 },
    { value: true as const, label: 'ATIVOS ', count: counts.value?.active ?? 0 },
    { value: false as const, label: 'INATIVOS ', count: counts.value?.inactive ?? 0 },
])

const title = 'Criar'
const subtitle = 'Crie usuário'
const visible = ref(false)

const roles = ref([
    { name: 'Administrador', code: 'admin' },
    { name: 'Gerente', code: 'manager' },
    { name: 'Empregado', code: 'employeer' }
])

const form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    is_active: true
})

function resetForm() {
    form.first_name = '',
        form.last_name = '',
        form.email = '',
        form.password = '',
        form.password_confirmation = '',
        form.role = '',
        form.is_active = true
}

const { fieldErrors, apiError, extractErrors } = useFormErrors({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    role: '',
})

function onCancel() {
    visible.value = false
    resetForm()
}

async function onSubmit() {
    try {
        await createUser({ ...form })
        onCancel()
    } catch (err) {
        extractErrors(err) // tudo encapsulado
    }
}

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
