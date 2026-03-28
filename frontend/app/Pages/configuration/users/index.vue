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

        <DefaultTable v-else :value="data" :loading="pending">
            <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" />
        </DefaultTable>
    </main>
</template>

<script setup lang="ts">
definePageMeta({
    layout: 'configuration',
})

const { data, pending, error } = useUsers();

const columns = [
    { field: 'id', header: 'ID' },
    { field: 'first_name', header: 'First Name' },
    { field: 'last_name', header: 'Last Name' },
    { field: 'email', header: 'Email' },
    { field: 'role', header: 'Role' },
    { field: 'is_active', header: 'Active' },
    { field: 'last_login_at', header: 'Last Login' },
]
</script>