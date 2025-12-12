<template>
	<!-- standard header -->
	<header class="adminuiux-header">
		<!-- Fixed navbar -->
		<nav class="navbar">
			<div class="container-fluid">
				<div class="ms-auto">
				</div>
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
							<h3 class="text-theme-1 fw-normal mb-0">Welcome to,</h3>
							<h1 class="fw-bold text-theme-accent-1 mb-0">Svasthya</h1>
						</div>
						<div class="form-floating mb-3">
							<input type="text" class="form-control" :class="{ 'border-danger': errors.email }"
								id="email" placeholder="Enter your email address" tabindex="1" autofocus
								v-model="user.email" ref="emailInput">
							<label for="email">Email</label>
							<span v-if="errors.email" class="text-danger small d-block mt-1">{{ errors.email[0] ||
								errors.email }}</span>
						</div>
						<div class="position-relative mb-3">
							<div class="form-floating">
								<input :type="showPassword ? 'text' : 'password'" class="form-control"
									:class="{ 'border-danger': errors.password }" id="passwd" placeholder="Password"
									tabindex="2" v-model="user.password">
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
						<div class="text-center mt-3 mb-2">
							<router-link to="/forgot-password" class="text-decoration-none">
								Forgot Password?
							</router-link>
						</div>
						<div class="text-center mt-3">
							<router-link to="/register" class="text-decoration-none">
								Don't have an account? <span class="fw-bold text-secondary">Register</span>
							</router-link>
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
	<!-- standard index footer -->
	<footer class="adminuiux-footer mt-auto">
		<div class="container-fluid text-center">
			<!-- <span class="small">Copyright @2025, <a href="https://adminuiux.com" target="_blank">eCommuiux</a> on Earth ❤️ </span> -->
		</div>
	</footer>
	<Toast ref="toastRef" />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import Toast from '@/components/Toast.vue';
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';

const router = useRouter();
const authStore = useAuthStore();
const isLoginFormLoading = ref(false);

const user = ref({
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
		uri: 'login',
		data: user.value,
		meta: { loadingRef: isLoginFormLoading }
	})
		.then((response) => {
			toastRef.value?.show("Login successful", TOAST_SUCCESS);
			authStore.setUser(response.data.user);
			authStore.setToken(response.data.token);
			router.push({ path: '/dashboard' });
		})
		.catch((error) => {
			errors.value = error.response?.data?.errors || {};
			if (error.response?.data?.message) {
				toastRef.value?.show(error.response.data.message, TOAST_ERROR);
			} else if (error.response?.data?.errors) {
				// Show first error message
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