<template>
  <aside class="sidebar relative border-r border-border-input h-lvh flex flex-col
           transition-[width] duration-300 ease-in-out overflow-hidden" :class="modalState ? 'w-64' : 'w-16'">

    <div class="flex items-center h-14 shrink-0 px-2" :class="modalState ? 'justify-end' : 'justify-center'">
      <button type="button" @click="modalState = !modalState" class="cursor-pointer p-2 rounded-md hover:bg-muted
               transition-transform duration-300 ease-in-out hover:scale-110">

        <i class="pi pi-angle-double-right text-2xl block
                  transition-transform duration-300 ease-in-out" :class="modalState ? 'rotate-180' : 'rotate-0'" />
      </button>
    </div>

    <div class="shrink-0 px-4 pb-2 overflow-hidden
                transition-all duration-300 ease-in-out"
      :class="modalState ? 'h-20 opacity-100' : 'h-14 opacity-0 pointer-events-none'">
      <span class="font-bold tracking-wider text-xl block">DRS ERP</span>
      <span class="text-text-muted tracking-widest text-sm block">E2S SYSTEMS</span>
      <div class="h-1 max-w-1/4 bg-primary mt-2" />
    </div>

    <nav class="flex flex-col flex-1 overflow-y-auto py-4">
      <AppSidebarSection v-for="group in navigation" :key="group.section" :modal-state="modalState"
        :label="group.section" class="flex flex-col mb-6">
        <AppSidebarItem v-for="item in group.items" :key="item.to" v-bind="item" :modal-state="modalState" />
      </AppSidebarSection>
    </nav>
  </aside>
</template>

<script setup lang="ts">
import { useLogout } from '~/Composables/useLogout';

const { logoutUser, loading: logoutLoading } = useLogout()

const navigation = [
  {
    section: 'PRINCIPAL',
    items: [
      { label: 'Dashboard', icon: 'th-large', to: '/dashboard' },
      // { label: 'Vendas', icon: 'shopping-cart', to: '/vendas' },
      // { label: 'Financeiro', icon: 'wallet', to: '/financeiro' },
      // { label: 'Estoque', icon: 'box', to: '/estoque' },
      // { label: 'RH', icon: 'users', to: '/rh' },
    ]
  },
  {
    section: 'SISTEMA',
    items: [
      { label: 'Configurações', icon: 'cog', to: '/configuration/users' },
    ]
  },
   {
    section: 'Conta',
    items: [
      {
        label: 'Sair',
        icon: 'sign-out',
        action: logoutUser,
        loading: logoutLoading.value,
      },
    ],
  },
]

useSanctumAuth()
const modalState = ref(true)
</script>