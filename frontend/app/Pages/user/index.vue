<template>
  <div>
    <h2 class="text-white">Testando Conexão com Laravel</h2>

    <div v-if="pending">Carregando dados...</div>

    <div v-else-if="error">
      <p style="color: red;">Erro: {{ error.message || 'Erro desconhecido' }}</p>
    </div>

    <div v-else>
      <div style="color: green;">
        {{ userResponse?.message || 'Sem mensagem' }}
      </div>

      <ul v-if="userResponse?.data && userResponse.data.length > 0">
        <li v-for="user in userResponse.data" :key="user.id">
          {{ user.first_name }} {{ user.last_name }} - {{ user.email }}
        </li>
      </ul>
      <div v-else style="color: orange;">
        Nenhum usuário encontrado
      </div>
    </div>

  </div>
  <button @click="userLogout" class="mt-4 px-4 py-2 bg-red-500 text-white rounded">Logout</button>
  <NuxtLink to="/">Voltar</NuxtLink>
</template>

<script setup>
definePageMeta({
  middleware: ['sanctum:auth'],
})

const { data: userResponse, pending, error } = await useSanctumFetch('/api/v1/users');
const { logout } = useSanctumAuth()

async function userLogout() {
  await logout();
}


if (error.value) {
  console.error('Erro na requisição:', error.value)
}

console.log('Resposta completa:', userResponse.value)

</script>
