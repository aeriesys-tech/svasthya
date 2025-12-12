import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import VCalendar from 'v-calendar'

import '@/utils/axiosLoading.js'
import 'vue3-toastify/dist/index.css';
import '@/assets/css/toast-mods.css';
import '@/assets/css/main.css';

import '@fortawesome/fontawesome-free/css/all.min.css'
import i18n from './i18n';

import { useAuthStore } from '@/stores/authStore'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(i18n)
app.use(VCalendar, {})

// Initialize auth store from sessionStorage
const authStore = useAuthStore()
authStore.init()

app.mount('#app')
