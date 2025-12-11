import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import { VitePWA } from 'vite-plugin-pwa'

// https://vite.dev/config/
export default defineConfig({
//   base: '/mks',
  plugins: [
    vue(),
    VitePWA({
	  registerType: 'autoUpdate',
	  manifest: {
		name: 'Svasthya',
		short_name: 'Svasthya',
		start_url: '/',
		display: 'standalone',
		theme_color: '#ffffff',
		background_color: '#ffffff',
		icons: [
		  {
			src: '/images/favicon/android-chrome-192x192.png',
			sizes: '192x192',
			type: 'image/png'
		  },
		  {
			src: '/images/favicon/android-chrome-512x512.png',
			sizes: '512x512',
			type: 'image/png'
		  }
		]
	  }
	}),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
})
