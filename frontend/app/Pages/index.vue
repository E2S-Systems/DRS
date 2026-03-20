<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 gap-4 border">
    <div class="border w-auto flex flex-col p-8 gap-4 justify-center items-center">
      <h1 class="text-2xl font-bold">Seja bem-vindo</h1>
      <p>Preencha seu e-mail e senha para acessar sua conta.</p>

      <form class="text-gray-700">
        <div class="flex flex-col gap-2 w-full">
          <span class="text-gray-700">E-mail</span>
          <input v-model="form.email" name="email" type="text" placeholder="Digite seu e-mail"
            class="border border-gray-300 bg-white rounded-md py-2 px-4" />
          <span class="text-gray-700">Senha</span>
          <input v-model="form.password" name="password" type="password" placeholder="Digite sua senha"
            class="border border-gray-300 bg-white rounded-md py-2 px-4" />
        </div>

        <div class="flex items-center justify-between w-full">
          <div class="flex gap-3"><input type="checkbox">Lembre-se de mim</div>
          <span>Esqueceu sua senha?</span>
        </div>
        <button @click.prevent="userLogin" class="bg-[#E10600] text-white py-2 px-4 rounded-md">
          <template v-if="loading">Carregando...</template>
          <template v-else>Login</template>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'custom',
})

const form = ref({
  email: 'valeria.deaguiar@gmail.com',
  password: 'password',
  remeber: true,
})

const { login } = useSanctumAuth()

async function userLogin(){

  await login(form.value);
}

</script>

<style scoped></style>