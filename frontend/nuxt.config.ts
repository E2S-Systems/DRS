// https://nuxt.com/docs/api/configuration/nuxt-config
import Aura from '@primevue/themes/aura';
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
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://backend:8000/api'
    }
  },
  vite: {
    server: {
      hmr: {
        protocol: 'ws',
        host: 'localhost',
        port: 24678
      }
    }
  }
})