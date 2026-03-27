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
    composables: 'Composables',
    plugins: 'Plugins',
    middleware: 'Middleware',
  },
  modules: ['@primevue/nuxt-module', '@pinia/nuxt', 'nuxt-toast', 'nuxt-auth-sanctum'],
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
    sanctumBaseUrl: process.env.NUXT_SANCTUM_BASE_URL || 'http://localhost:8000',
    public: {
      sanctum: {
        baseUrl: process.env.NUXT_PUBLIC_SANCTUM_BASE_URL || 'http://localhost:8000',
        endpoints: {
          csrf: '/sanctum/csrf-cookie',
          login: '/api/v1/login',
          logout: '/api/v1/logout',
          user: '/api/v1/users',
        },
        mode: 'token',
        redirect: {
          keepRequestedRoute: true,
          onLogin: '/dashboard',
          onLogout: '/',
          onGuestOnly: '/dashboard',
          onAuthOnly: '/',
        },
        redirectIfAuthenticated: true,
      },
      apiUrl: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api/v1',
    },
    sanctum: {
      baseUrl: process.env.NUXT_PUBLIC_SANCTUM_BASE_URL || 'http://localhost:8000',
      sanctumBaseUrl: process.env.NUXT_SANCTUM_BASE_URL || 'http://localhost:8000',
    },
  },
  css: ['~/assets/css/main.css'],
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
  },
  components: [
    {
      path: '~/components',
      pathPrefix: false,
    },
  ]
})