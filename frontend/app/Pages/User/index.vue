<template>
  <div>
    <h2>Testando Conexão com Laravel</h2>

    <div v-if="pending">Carregando dados...</div>

    <div style="color: green;">
      {{ userResponse.message }}
    </div>

    <ul>
      <li v-for="user in userResponse.data" :key="user.id">
        {{ user.first_name }} {{ user.last_name }} - {{ user.email }}
      </li>
    </ul>

  </div>
  <NuxtLink to="/">Voltar</NuxtLink>
</template>

<script setup>
const config = useRuntimeConfig()

const { data: userResponse } = await useFetch('/users', {
  baseURL: config.public.apiBase
})

</script>
