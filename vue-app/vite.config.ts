import { fileURLToPath, URL } from 'node:url';

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import vueDevTools from 'vite-plugin-vue-devtools';
import Components from 'unplugin-vue-components/vite';
import AutoImport from 'unplugin-auto-import/vite';
import { PuikResolver } from '@prestashopcorp/puik-resolver';
import cssInjectedByJsPlugin from 'vite-plugin-css-injected-by-js';

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
    Components({
      resolvers: [PuikResolver()],
    }),
    AutoImport({
      resolvers: [PuikResolver()],
    }),
    cssInjectedByJsPlugin(),
  ],
  server: {
    origin: "http://localhost:5173",
  },
  base: '/modules/ps_distributionapiclient/views/js/vue/',
  build: {
    cssCodeSplit: false,
    outDir: "../views/js/vue",
    emptyOutDir: true,
    rollupOptions: {
      output: {
        manualChunks: () => "index.js",
        entryFileNames: `assets/[name].js`,
      },
    },
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
})
