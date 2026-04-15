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
                <small class="text-surface-400 text-xs text-text-muted">
                    Deixe em branco para manter a senha atual.
                </small>
                <small v-if="fieldErrors.password" class="text-red-500">
                    {{ fieldErrors.password }}
                </small>
            </div>
            
            <div class="flex flex-col gap-1">
                <label for="edit_password_confirmation"
                    class="text-xs font-semibold tracking-widest uppercase text-surface-400">
                    Confirmar Nova Senha
                </label>
                <InputText id="edit_password_confirmation" v-model="form.password_confirmation" type="password"
                    autocomplete="off" placeholder="Repita a nova senha" class="w-full" />
                <small v-if="fieldErrors.password_confirmation" class="text-red-500">
                    {{ fieldErrors.password_confirmation }}
                </small>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <Button type="button" severity="secondary" :disabled="isUpdating" @click="onCancel" label="CANCELAR" />
            <Button type="button" severity="primary" :loading="isUpdating" @click="onSubmit" label="SALVAR ALTERAÇÕES" />
        </div>
    </DefaultModal>
</template>

<script setup lang="ts">
import type { User } from '~/types/user'
import { useUsers } from '~/Composables/useUsers';

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
    set: (val: boolean) => emit('update:visible', val),
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
    password_confirmation: string
}

const form = reactive<EditForm>({
    first_name: '',
    last_name: '',
    email: '',
    role: '',
    password: '',
    password_confirmation: '',
})

const fieldErrors = reactive<Partial<Record<keyof EditForm, string>>>({})

const clearFieldErrors = () => {
    Object.keys(fieldErrors).forEach((key) => {
        delete fieldErrors[key as keyof EditForm]
    })
}
const resetForm = () => {
    form.first_name = ''
    form.last_name = ''
    form.email = ''
    form.role = ''
    form.password = ''
    form.password_confirmation = ''
    clearFieldErrors()
}
const populateForm = (user: User) => {
    form.first_name = user.first_name
    form.last_name = user.last_name
    form.email = user.email
    form.role = user.role
    form.password = ''
    form.password_confirmation = ''
    clearFieldErrors()
}

watch(
    () => props.user,
    (user: User) => {
        if (!props.visible) return
        if (!user) {
            resetForm()
            return
        }
        populateForm(user)
    },
    { immediate: true }
)
// Reabre corretamente o modal mesmo para o mesmo usuário (mesma referência).
watch(
    () => props.visible,
    (isVisible: boolean) => {
        if (isVisible) {
            if (props.user) {
                populateForm(props.user)
            } else {
                resetForm()
            }
            return
        }
        resetForm()
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
            ...(form.password_confirmation ? { password_confirmation: form.password_confirmation} : {}),
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