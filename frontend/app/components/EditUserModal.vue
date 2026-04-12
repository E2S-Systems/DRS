<template>
    <DefaultModal title="EDITAR USUÁRIO" subtitle="Altere os dados do usuário abaixo." v-model:visible="visible">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6 mb-6">

            <div class="flex flex-col gap-1">
                <label for="edit_first_name" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    Primeiro Nome
                </label>
                <InputText id="edit_first_name" v-model="form.first_name" type="text" autocomplete="off"
                    placeholder="Rafael" class="w-full" />
                <small v-if="fieldErrors.first_name" class="text-red-500">
                    {{ fieldErrors.first_name }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="edit_last_name" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    Sobrenome
                </label>
                <InputText id="edit_last_name" v-model="form.last_name" type="text" autocomplete="off"
                    placeholder="Silva dos Santos" class="w-full" />
                <small v-if="fieldErrors.last_name" class="text-red-500">
                    {{ fieldErrors.last_name }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="edit_email" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    E-mail
                </label>
                <InputText id="edit_email" v-model="form.email" type="text" autocomplete="off"
                    placeholder="rafael.silva@gmail.com" class="w-full" />
                <small v-if="fieldErrors.email" class="text-red-500">
                    {{ fieldErrors.email }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="edit_role" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    Perfil de Acesso
                </label>
                <Select id="edit_role" v-model="form.role" :options="roles" optionLabel="name" optionValue="code"
                    placeholder="Selecione o perfil" class="w-full" />
                <small v-if="fieldErrors.role" class="text-red-500">
                    {{ fieldErrors.role }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="edit_password" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    Nova Senha
                </label>
                <InputText id="edit_password" v-model="form.password" type="password" autocomplete="off"
                    placeholder="Mín. 8 caracteres / opcional" class="w-full" />
                <small class="text-surface-400 text-xs">
                    Deixe em branco para manter a senha atual.
                </small>
                <small v-if="fieldErrors.password" class="text-red-500">
                    {{ fieldErrors.password }}
                </small>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <Button type="button" severity="secondary" :disabled="isUpdating" @click="onCancel" label="CANCELAR" />
            <Button type="button" :loading="isUpdating" @click="onSubmit" label="SALVAR ALTERAÇÕES" />
        </div>
    </DefaultModal>
</template>

<script setup lang="ts">
import type { User } from '~/types/user'
import { useUsers } from '~/Composables/useUsers';
const toast = useToast()

const props = defineProps<{
    visible: boolean
    user: User | null
}>()

const emit = defineEmits<{
    'update:visible': [value: boolean]
    'saved': []
}>()

const visible = computed({
    get: () => props.visible,
    set: (val) => emit('update:visible', val),
})

const { updateUser, isUpdating } = useUsers()

const roles = [
    { name: 'Administrador', code: 'admin' },
    { name: 'Gerente', code: 'manager' },
    { name: 'Funcionário', code: 'employee' },
]

interface EditForm {
    first_name: string
    last_name: string
    email: string
    role: string
    password: string
}

const form = reactive<EditForm>({
    first_name: '',
    last_name: '',
    email: '',
    role: '',
    password: '',
})

const fieldErrors = reactive<Partial<Record<keyof EditForm, string>>>({})

// Popula o form sempre que o user mudar (novo usuário selecionado para edição)
// immediate: true garante que popula na primeira abertura também
watch(
    () => props.user,
    (user) => {
        if (!user) return

        form.first_name = user.first_name
        form.last_name = user.last_name
        form.email = user.email
        form.role = user.role
        form.password = ''

        Object.keys(fieldErrors).forEach((key) => {
            delete fieldErrors[key as keyof EditForm]
        })
    },
    { immediate: true }
)
const onCancel = () => {
    visible.value = false
}

const onSubmit = async () => {
    if (!props.user) return

    try {
        const payload = {
            first_name: form.first_name,
            last_name: form.last_name,
            email: form.email,
            role: form.role,
            ...(form.password ? { password: form.password } : {}),
        }

        await updateUser(props.user.id, payload)

        visible.value = false
        emit('saved')
    } catch (error: any) {
        const errors = error?.data?.errors ?? {}
        Object.keys(errors).forEach((key) => {
            fieldErrors[key as keyof EditForm] = errors[key][0]
        })
    }
}
</script>