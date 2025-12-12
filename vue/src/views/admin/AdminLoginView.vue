<template>
	<!-- standard header -->
	<header class="adminuiux-header">
		<!-- Fixed navbar -->
		<nav class="navbar">
			<div class="container-fluid">
				<div class="ms-auto"></div>
			</div>
		</nav>
	</header>
	<div class="adminuiux-wrap z-index-0 position-relative">
		<main class="adminuiux-content">
			<!--Page body-->
			<div class="container-fluid">
				<div class="row gx-3 align-items-center justify-content-center py-3 mt-auto z-index-1 height-dynamic"
					style="--h-dynamic: calc(100vh - 120px)">
					<div class="col login-box maxwidth-400">
						<div class="mb-4">
							<h3 class="text-theme-1 fw-normal mb-0">Admin Login</h3>
							<h1 class="fw-bold text-theme-accent-1 mb-0">Svasthya</h1>
						</div>
						<div class="form-floating mb-3">
							<input type="text" class="form-control" :class="{ 'border-danger': errors.email }"
								id="email" placeholder="Enter your email address" tabindex="1" autofocus
								v-model="admin.email" ref="emailInput">
							<label for="email">Email</label>
							<span v-if="errors.email" class="text-danger small d-block mt-1">{{ errors.email[0] ||
								errors.email }}</span>
						</div>
						<div class="position-relative mb-3">
							<div class="form-floating">
								<input :type="showPassword ? 'text' : 'password'" class="form-control"
									:class="{ 'border-danger': errors.password }" id="passwd" placeholder="Password"
									tabindex="2" v-model="admin.password">
								<label for="passwd">Password</label>
							</div>
							<button type="button"
								class="btn btn-square btn-link text-theme-1 position-absolute end-0 top-0 mt-2 me-2"
								@click="togglePassword">
								<i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
							</button>
							<span v-if="errors.password" class="text-danger small d-block mt-1">{{ errors.password[0] ||
								errors.password }}</span>
						</div>
						<button class="btn btn-lg btn-theme w-100 mb-3" tabindex="3" @click="login()"
							v-if="!isLoginFormLoading">Login</button>
						<button class="btn btn-lg btn-theme w-100 mb-3" v-else>
							<div class="spinner-border spinner-border-sm" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</button>
						<div class="text-center mt-3">
							<router-link to="/" class="text-decoration-none">
								Back to <span class="fw-bold text-secondary">User Login</span>
							</router-link>
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
	<!-- standard index footer -->
	<footer class="adminuiux-footer mt-auto">
		<div class="container-fluid text-center"></div>
	</footer>
	<Toast ref="toastRef" />
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import Toast from '@/components/Toast.vue';
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';

const router = useRouter();
const authStore = useAuthStore();
const isLoginFormLoading = ref(false);

const admin = ref({
	email: '',
	password: '',
});
const showPassword = ref(false);
const errors = ref({});
const emailInput = ref(null);
const toastRef = ref(null);

const togglePassword = () => {
	showPassword.value = !showPassword.value;
};

const login = () => {
	authStore.auth({
		uri: 'admin/login',
		data: admin.value,
		meta: { loadingRef: isLoginFormLoading }
	})
		.then(async (response) => {
			if (!response.data.token) {
				toastRef.value?.show('Login failed: No token received', TOAST_ERROR);
				return;
			}

			toastRef.value?.show("Login successful", TOAST_SUCCESS);

			// Store admin data separately - ensure all values are set synchronously
			sessionStorage.setItem('admin-token', response.data.token);
			sessionStorage.setItem('admin-user', JSON.stringify(response.data.admin));
			sessionStorage.setItem('is-admin', 'true');

			// Clear any user tokens to avoid conflicts
			sessionStorage.removeItem('event-catering-token');
			sessionStorage.removeItem('event-catering-user');

			// Wait for next tick to ensure sessionStorage is committed
			await nextTick();

			// Verify values are set
			const adminToken = sessionStorage.getItem('admin-token');
			const isAdmin = sessionStorage.getItem('is-admin');

			console.log('Admin login - Token set:', !!adminToken, 'IsAdmin:', isAdmin);

			if (adminToken && isAdmin === 'true') {
				// Small delay to ensure sessionStorage is committed before navigation
				setTimeout(() => {
					console.log('Navigating to AdminDashboardView...');
					router.push({ name: 'AdminDashboardView' }).then(() => {
						console.log('Navigation successful');
					}).catch(err => {
						// If navigation fails, try hash navigation as fallback
						console.error('Router navigation error:', err);
						window.location.hash = '#/admin/dashboard';
					});
				}, 100);
			} else {
				console.error('Admin credentials not set properly', { adminToken: !!adminToken, isAdmin });
				toastRef.value?.show('Login error. Please try again.', TOAST_ERROR);
			}
		})
		.catch((error) => {
			errors.value = error.response?.data?.errors || {};
			if (error.response?.data?.message) {
				toastRef.value?.show(error.response.data.message, TOAST_ERROR);
			} else if (error.response?.data?.errors) {
				const firstError = Object.values(error.response.data.errors)[0];
				if (firstError && firstError[0]) {
					toastRef.value?.show(firstError[0], TOAST_ERROR);
				}
			} else {
				toastRef.value?.show('Login failed. Please try again.', TOAST_ERROR);
			}
		});
}

onMounted(() => {
	if (emailInput.value) {
		emailInput.value.focus();
	}
});
</script>

