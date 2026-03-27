<template>
    <aside class="sidebar relative border-r border-border-input h-lvh flex flex-col
           transition-[width]">
        <div class="shrink-0 px-4 pb-5 pt-14 overflow-hidden
                transition-all duration-300 ease-in-out">
            <span class="font-bold tracking-wider text-xl block">CONFIGURAÇÕES</span>
            <span class="text-text-muted tracking-widest text-sm block">
                {{ breadcrumb }}
            </span>
            <div class="h-1 max-w-1/4 bg-primary mt-2" />
        </div>

        <nav class="flex flex-col flex-1 overflow-y-auto py-4">
            <ConfigurationSidebarSection v-for="group in navigation" :key="group.section" :label="group.section"
                class="flex flex-col mb-6">
                <ConfigurationSidebarItems v-for="item in group.items" :key="item.to" v-bind="item" />
            </ConfigurationSidebarSection>
        </nav>
    </aside>
</template>

<script setup lang="ts">
const route = useRoute()

const navigation = computed(() => [
  {
    section: 'SEÇÕES',
    items: [
      { label: 'Usuários',     icon: 'user-plus', to: '/configuration/users' },
      { label: 'Filiais',      icon: 'building',  to: '/configuration/branches' },
      { label: 'Permissões',   icon: 'wrench',    to: '/configuration/permissions' },
      { label: 'Empresa',      icon: 'briefcase', to: '/configuration/company' },
      { label: 'Integrações',  icon: 'link',      to: '/configuration/integrations' },
    ]
  }
])

const breadcrumb = computed(() => {
  const allItems = navigation.value.flatMap(group => group.items)
  const activeItem = allItems.find(item => item.to === route.path)

  if (!activeItem) return 'Configurações'

  return `Configurações / ${activeItem.label}`
})

</script>