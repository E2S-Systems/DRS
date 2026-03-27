<template>
  <aside class="sidebar relative border-r border-border-input max-w-1/10 max-h-full h-lvh">
    <i class="pi pi-angle-double-left p-2"></i>

    <div class="flex flex-col p-4">
      <span class="font-bold tracking-wider text-xl">DRS ERP</span>
      <span class="text-text-muted tracking-widest text-sm">E2S SYSTEMS</span>
      <div class="h-1 max-w-1/4 bg-primary my-2" />
    </div>

    <nav class="flex flex-col my-10">
      <AppSidebarSection
        v-for="group in navigation"
        :key="group.section"
        :label="group.section"
        class="flex flex-col my-10"
      >
        <AppSidebarItem
          v-for="item in group.items"
          :key="item.to"
          v-bind="item"
        />
      </AppSidebarSection>
    </nav>
  </aside>
</template>

<script setup lang="ts">
const navigation = [
  {
    section: 'PRINCIPAL',
    items: [
      { label: 'Dashboard', icon: 'th-large', to: '/dashboard' },
      { label: 'Vendas', icon: 'shopping-cart', to: '/vendas' },
      { label: 'Financeiro', icon: 'wallet', to: '/financeiro' },
      { label: 'Estoque', icon: 'box', to: '/estoque' },
      { label: 'RH', icon: 'users', to: '/rh' },
    ]
  },
  {
    section: 'SISTEMA',
    items: [
      { label: 'Configurações', icon: 'cog', to: '/configuration' },
    ]
  }
]

const { user } = useSanctumAuth() 
</script>
