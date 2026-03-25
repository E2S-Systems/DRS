<template>
  <form class="flex flex-col gap-4" @submit.prevent="submit">
    <div class="flex flex-col gap-1">
      <label for="email" class="text-xs text-text-muted uppercase tracking-widest">E-mail</label>
      <input id="email"
        class="border-b-2 border-border-input hover:border-text-hint focus:border-primary focus:outline-none p-2"
        type="email" v-model="form.email" name="email" placeholder="Digite seu e-mail" />
    </div>

    <div class="flex flex-col gap-1">
      <label for="password" class="text-xs text-text-muted uppercase tracking-widest">Senha</label>
      <div class="relative flex items-center">
        <input id="password"
          class="w-full border-b-2 border-border-input hover:border-text-hint focus:border-primary focus:outline-none p-2 pr-8"
          :type="showPassword ? 'text' : 'password'" v-model="form.password" name="password"
          placeholder="Digite a sua senha" />
        <button type="button" @click="showPassword = !showPassword"
          class="absolute right-2 text-text-hint hover:text-white transition-colors"
          :aria-label="showPassword ? 'Hide password' : 'Show password'" :aria-pressed="showPassword.toString()">
          <i v-if="showPassword" class="pi pi-eye" style="font-size: 1rem"></i>
          <i v-else xmlns="http://www.w3.org/2000/svg" class="pi pi-eye-slash" style="font-size: 1rem"></i>
        </button>
      </div>
    </div>

    <button type="submit" :disabled="loading"
      class="bg-primary hover:bg-primary-hover h-10 tracking-widest cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
      {{ loading ? 'ENTRANDO...' : 'ENTRAR' }}
    </button>

    <div>
      <span>Esqueceu a senha?</span>
      <span class="text-primary hover:text-primary-hover"> Recuperar acesso</span>
    </div>
  </form>
</template>

<script setup lang="ts">
import { useLoginForm } from '~/Composables/useLoginForm';

const { form, loading, submit } = useLoginForm()
const showPassword = ref(false)
</script>