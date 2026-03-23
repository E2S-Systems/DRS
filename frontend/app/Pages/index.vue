<template>
  <main class="flex h-lvh max-h-full">
    <div id="left-side" class="bg-bg w-1/2 h-full flex items-center justify-center">
      <div class="flex flex-col w-full max-w-lg px-16 gap-8">
        <div class="flex flex-col italic">
          <span class="font-bold text-xl">DRS ERP</span>
          <span class="text-text-muted tracking-wide">E2S SYSTEMS</span>
        </div>

        <div class="flex flex-col">
          <span class="font-bold text-3xl">BEM-VINDO DE VOLTA</span>
          <span class="text-text-muted">Acesse sua conta para continuar</span>
        </div>

        <div class="h-1.5 bg-primary max-w-1/2 w-full"></div>

        <form class="flex flex-col gap-4">
          <label class="text-xs text-text-muted uppercase tracking-widest">E-mail</label>
          <input
            class="border-b-2 border-border-input hover:border-text-hint focus:border-primary focus:outline-none p-2"
            type="text" v-model="form.email" name="email" placeholder="Digite seu e-mail">

          <label class="text-xs text-text-muted uppercase tracking-widest">Senha</label>
          <input
            class="border-b-2 border-border-input hover:border-text-hint focus:border-primary focus:outline-none p-2"
            type="password" v-model="form.password" name="password" placeholder="Digite a sua senha">
          <button @click.prevent="userLogin"
            class="bg-primary hover:bg-primary-hover h-10 tracking-widest cursor-pointer ">ENTRAR</button>
          <div>
            <span>Esqueceu a senha?</span> <span class="text-primary hover:text-primary-hover cursor-pointer">Recuperar
              acesso</span>
          </div>
        </form>
      </div>
    </div>

    <div id="right-side" class="w-1/2 h-full relative border-y-4 border-primary overflow-hidden">

      <div class="absolute inset-0 bg-bg-panel"></div>

      <div class="absolute inset-10 opacity-30 overflow-hidden sm:hidden xl:block">
        <img src="/images/f1_car.png" alt="" aria-hidden="true"
          class="absolute w-full h-full object-cover object-top rotate-90" />
      </div>

      <div class="absolute bottom-10 left-10 right-10 flex flex-col gap-4">
        <span class="text-primary text-sm tracking-widest uppercase font-light">
          PERFORMANCE. PRECISÃO. RESULTADO.
        </span>
        <p class="text-text text-5xl font-normal leading-tight">
          Na pista, <span class="text-primary font-bold">SEGUNDOS</span> decidem as corridas.
        </p>
        <p class="text-text text-5xl font-normal leading-tight">
          Nos negócios, <span class="text-primary font-bold">DADOS</span> decidem o futuro.
        </p>
      </div>

    </div>
  </main>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'custom',
  middleware: ['sanctum:guest'],
})

const { login } = useSanctumAuth();
const loading = ref(false);
const toast = useToast()

const form = ref({
  email: '',
  password: '',
  remember: true,
});

async function userLogin() {
  try {
    await login(form.value);
  }
  catch (error) {
    loading.value = false;
    toast.error({
      title: 'Erro!',
      message: 'Credenciais inválidas. Tente novamente.',
      timeout: 3000,
    });
  }
}

</script>

<style scoped></style>