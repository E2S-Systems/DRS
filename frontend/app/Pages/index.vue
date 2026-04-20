<template>
  <main class="flex h-lvh min-h-screen max-h-full">
    <div id="left-side" class="bg-bg w-full md:w-1/2 h-full flex items-center justify-center">
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
 
        <LoginForm/>
      </div>
    </div>
 
    <LoginBanner/>
  </main>
</template>
 
<script setup lang="ts">

definePageMeta({
  layout: 'empty',
  middleware: ['sanctum:guest'],
})

useHead({
    title: "Login"
})
</script>