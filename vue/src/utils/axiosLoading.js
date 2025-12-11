import axios from 'axios';
import { ref } from 'vue';

export const isGlobalLoading = ref(false);

axios.interceptors.request.use(config => {
    const loadingRef = config.meta?.loadingRef || isGlobalLoading;
    loadingRef.value = true;
    return config;
}, error => {
    const loadingRef = error.config?.meta?.loadingRef || isGlobalLoading;
    loadingRef.value = false;
    return Promise.reject(error);
});

axios.interceptors.response.use(response => {
    const loadingRef = response.config.meta?.loadingRef || isGlobalLoading;
    loadingRef.value = false;
    return response;
}, error => {
    const loadingRef = error.config?.meta?.loadingRef || isGlobalLoading;
    loadingRef.value = false;
    return Promise.reject(error);
});

export default function useAxiosLoading() {
    return { isGlobalLoading };
}
