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


const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(VCalendar, {})
app.mount('#app')
