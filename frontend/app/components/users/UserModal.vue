<template>
    <DefaultModal :title="isEditing ? 'EDITAR USUÁRIO' : 'NOVO USUÁRIO'"
        :subtitle="isEditing ? 'Altere os dados do usuário abaixo.' : 'Preencha os dados do novo usuário.'"
        v-model:visible="visible">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6 mb-6">

            <div class="flex flex-col gap-1">
                <label for="user_first_name" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    Primeiro Nome
                </label>
                <InputText id="user_first_name" v-model="form.first_name" type="text" autocomplete="off"
                    placeholder="Rafael" class="w-full" />
                <small v-if="fieldErrors.first_name" class="text-red-500">
                    {{ fieldErrors.first_name }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="user_last_name" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    Sobrenome
                </label>
                <InputText id="user_last_name" v-model="form.last_name" type="text" autocomplete="off"
                    placeholder="Silva dos Santos" class="w-full" />
                <small v-if="fieldErrors.last_name" class="text-red-500">
                    {{ fieldErrors.last_name }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="user_email" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    E-mail
                </label>
                <InputText id="user_email" v-model="form.email" type="text" autocomplete="off"
                    placeholder="rafael.silva@gmail.com" class="w-full" />
                <small v-if="fieldErrors.email" class="text-red-500">
                    {{ fieldErrors.email }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="user_role" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    Perfil de Acesso
                </label>
                <Select id="user_role" v-model="form.role" :options="roles" optionLabel="name" optionValue="code"
                    placeholder="Selecione o perfil" class="w-full" />
                <small v-if="fieldErrors.role" class="text-red-500">
                    {{ fieldErrors.role }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="user_password" class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    {{ isEditing ? 'Nova Senha' : 'Senha' }}
                </label>
                <InputText id="user_password" v-model="form.password" type="password" autocomplete="off"
                    :placeholder="isEditing ? 'Mín. 8 caracteres / opcional' : 'Mín. 8 caracteres'" class="w-full" />
                <small v-if="isEditing" class="text-surface-400 text-xs">
                    Deixe em branco para manter a senha atual.
                </small>
                <small v-if="fieldErrors.password" class="text-red-500">
                    {{ fieldErrors.password }}
                </small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="user_password_confirmation"
                    class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    {{ isEditing ? 'Confirmar Nova Senha' : 'Confirmar Senha' }}
                </label>
                <InputText id="user_password_confirmation" v-model="form.password_confirmation" type="password"
                    autocomplete="off" placeholder="Repita a senha" class="w-full" />
                <small v-if="fieldErrors.password_confirmation" class="text-red-500">
                    {{ fieldErrors.password_confirmation }}
                </small>
            </div>

        </div>

        <div class="flex justify-end gap-3">
            <Button type="button" severity="secondary" :disabled="isLoading" @click="onCancel" label="CANCELAR" />
            <Button type="button" severity="primary" :loading="isLoading" @click="onSubmit"
                :label="isEditing ? 'SALVAR ALTERAÇÕES' : 'CRIAR USUÁRIO'" />
        </div>
    </DefaultModal>
</template>

<script setup lang="ts">
import type { User, CreateUserDTO, UpdateUserDTO } from '~/types/user'
import { useUsers } from '~/Composables/useUsers'

const props = defineProps<{
    visible: boolean
    user: User | null
}>()

const emit = defineEmits<{
    'update:visible': [value: boolean]
    'saved': []
    'close': []
}>()

const isEditing = computed(() => !!props.user)

const visible = computed({
    get: () => props.visible,
    set: (val: boolean) => emit('update:visible', val),
})

const { createUser, updateUser, isCreating, isUpdating } = useUsers()

const isLoading = computed(() => isEditing.value ? isUpdating.value : isCreating.value)

const roles = [
    { name: 'Administrador', code: 'admin' },
    { name: 'Gerente', code: 'manager' },
    { name: 'Funcionário', code: 'employee' },
]

interface UserForm {
    first_name: string
    last_name: string
    email: string
    role: string
    password: string
    password_confirmation: string
    is_active: boolean
}

const form = reactive<UserForm>({
    first_name: '',
    last_name: '',
    email: '',
    role: '',
    password: '',
    password_confirmation: '',
    is_active: true,
})

const fieldErrors = reactive<Partial<Record<keyof UserForm, string>>>({})

const clearFieldErrors = () => {
    ; (Object.keys(fieldErrors) as Array<keyof UserForm>).forEach((key) => {
        delete fieldErrors[key]
    })
}

const resetForm = () => {
    form.first_name = ''
    form.last_name = ''
    form.email = ''
    form.role = ''
    form.password = ''
    form.password_confirmation = ''
    form.is_active = true
    clearFieldErrors()
}

const populateForm = (user: User) => {
    form.first_name = user.first_name
    form.last_name = user.last_name
    form.email = user.email
    form.role = user.role
    form.password = ''
    form.password_confirmation = ''
    form.is_active = user.is_active
    clearFieldErrors()
}

watch(
    () => [props.visible, props.user] as const,
    ([isVisible, user]) => {
        if (!isVisible) {
            resetForm()
            return
        }
        user ? populateForm(user) : resetForm()
    },
    { immediate: true },
)

const onCancel = () => {
    visible.value = false
    emit('close')
}

const onSubmit = async () => {
    clearFieldErrors()

    try {
        if (isEditing.value && props.user) {
            const payload: UpdateUserDTO = {
                first_name: form.first_name,
                last_name: form.last_name,
                email: form.email,
                role: form.role,
                ...(form.password
                    ? {
                        password: form.password,
                        password_confirmation: form.password_confirmation,
                    }
                    : { password: null }
                ),
            }
            await updateUser(props.user.id, payload)
        } else {
            const payload: CreateUserDTO = {
                first_name: form.first_name,
                last_name: form.last_name,
                email: form.email,
                role: form.role,
                password: form.password,
                password_confirmation: form.password_confirmation,
                is_active: form.is_active,
            }
            await createUser(payload)
        }
        visible.value = false
        emit('saved')
    } catch (error: unknown) {
        const err = error as { data?: { errors?: Record<string, string[]> } }
        const errors = err?.data?.errors ?? {}

        Object.entries(errors).forEach(([key, messages]) => {
            fieldErrors[key as keyof UserForm] = messages[0]
        })
    }
}
</script>