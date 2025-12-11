import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

export const useAuthStore = defineStore('auth', () => {
	// State
	const user = ref(null);
	const token = ref('');
	const permissions = ref([]);

	// Getters
	const getUser = computed(() => user.value);
	const getToken = computed(() => token.value);
	const getPermissions = computed(() => permissions.value);
	const isAuthenticated = computed(() => {
		return !!(token.value || sessionStorage.getItem('event-catering-token'));
	});

	// Actions
	const setUser = (userData) => {
		user.value = userData;
		if (userData) {
			sessionStorage.setItem('event-catering-user', JSON.stringify(userData));
		} else {
			sessionStorage.removeItem('event-catering-user');
		}
	};

	const setToken = (tokenValue) => {
		token.value = tokenValue;
		if (tokenValue) {
			sessionStorage.setItem('event-catering-token', tokenValue);
		} else {
			sessionStorage.removeItem('event-catering-token');
		}
	};

	const setPermissions = (permissionsData) => {
		permissions.value = permissionsData;
		if (permissionsData) {
			sessionStorage.setItem('event-catering-permissions', JSON.stringify(permissionsData));
		} else {
			sessionStorage.removeItem('event-catering-permissions');
		}
	};

	// Auth action (for login)
	const auth = (payload) => {
		return new Promise((resolve, reject) => {
			axios
				.post(`${API_URL}/${payload.uri}`, payload.data, {
					meta: payload.meta || {}
				})
				.then((response) => {
					resolve(response);
				})
				.catch((error) => {
					reject(error);
				});
		});
	};

	// Post action (for authenticated requests)
	const post = (payload) => {
		return new Promise((resolve, reject) => {
			axios
				.post(`${API_URL}/${payload.uri}`, payload.data, {
					headers: {
						Authorization: `Bearer ${token.value || sessionStorage.getItem('event-catering-token')}`,
					},
					meta: payload.meta || {}
				})
				.then((response) => {
					resolve(response);
				})
				.catch((error) => {
					if (error?.response?.data?.message === 'Unauthenticated.') {
						logout();
						window.location.href = '/#/login';
						window.location.reload();
					}
					reject(error);
				});
		});
	};

	// Get action (for authenticated GET requests)
	const get = (payload) => {
		return new Promise((resolve, reject) => {
			axios
				.get(`${API_URL}/${payload.uri}`, {
					headers: {
						Authorization: `Bearer ${token.value || sessionStorage.getItem('event-catering-token')}`,
					},
					meta: payload.meta || {}
				})
				.then((response) => {
					resolve(response);
				})
				.catch((error) => {
					if (error?.response?.data?.message === 'Unauthenticated.') {
						logout();
						window.location.href = '/#/login';
						window.location.reload();
					}
					reject(error);
				});
		});
	};

	// Logout action
	const logout = () => {
		user.value = null;
		token.value = '';
		permissions.value = [];
		sessionStorage.removeItem('event-catering-token');
		sessionStorage.removeItem('event-catering-id');
		sessionStorage.removeItem('event-catering-name');
		sessionStorage.removeItem('event-catering-email');
		sessionStorage.removeItem('event-catering-user');
		sessionStorage.removeItem('event-catering-permissions');
	};

	// Initialize from sessionStorage
	const init = () => {
		const storedToken = sessionStorage.getItem('event-catering-token');
		const storedUser = sessionStorage.getItem('event-catering-user');
		const storedPermissions = sessionStorage.getItem('event-catering-permissions');

		if (storedToken) {
			token.value = storedToken;
		}
		if (storedUser) {
			user.value = JSON.parse(storedUser);
		}
		if (storedPermissions) {
			permissions.value = JSON.parse(storedPermissions);
		}
	};

	return {
		// State
		user,
		token,
		permissions,
		// Getters
		getUser,
		getToken,
		getPermissions,
		isAuthenticated,
		// Actions
		setUser,
		setToken,
		setPermissions,
		auth,
		post,
		get,
		logout,
		init,
	};
});

