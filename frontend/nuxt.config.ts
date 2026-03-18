// https://nuxt.com/docs/api/configuration/nuxt-config
import Aura from '@primevue/themes/aura';
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  srcDir: 'app/',
  dir: {
    pages: 'Pages',
    layouts: 'Layouts',
    components: 'Components',
    composables: 'Composables',
    plugins: 'Plugins',
    middleware: 'Middleware'
  },
  modules: ['@primevue/nuxt-module', '@pinia/nuxt'],
  primevue: {
    options: {
      ripple: true,
      inputVariant: 'filled',
      theme: {
        preset: Aura,
        options: {
          prefix: 'p',
          darkModeSelector: 'system',
          cssLayer: false
        }
      }
    }
  },
  runtimeConfig: {
    public: {
      urlBase: 'http://localhost:8000/',
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    }
  },
  css: ['./app/assets/css/main.css'],
  vite: {
    server: {
      hmr: {
        protocol: 'ws',
        host: 'localhost',
        port: 24678
      }
    },
    plugins: [
      tailwindcss(),
    ],
  }
})