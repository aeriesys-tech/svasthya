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

						<form @submit.prevent="resetPassword">
							<div class="position-relative mb-3">
								<div class="form-floating">
									<input :type="showPassword ? 'text' : 'password'" class="form-control"
										:class="{ 'border-danger': errors.password }" id="password"
										placeholder="New Password" v-model.trim="form.password" ref="passwordInput" autofocus>
									<label for="password">New Password*</label>
								</div>
								<button type="button"
									class="btn btn-square btn-link text-theme-1 position-absolute end-0 top-0 mt-2 me-2"
									@click="togglePassword">
									<i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
								</button>
								<span v-if="errors.password" class="text-danger small d-block mt-1">{{
									errors.password[0] ||
									errors.password }}</span>
							</div>

							<div class="position-relative mb-3">
								<div class="form-floating">
									<input :type="showConfirmPassword ? 'text' : 'password'" class="form-control"
										:class="{ 'border-danger': errors.password_confirmation }" id="password_confirmation"
										placeholder="Confirm Password" v-model.trim="form.password_confirmation">
									<label for="password_confirmation">Confirm Password*</label>
								</div>
								<button type="button"
									class="btn btn-square btn-link text-theme-1 position-absolute end-0 top-0 mt-2 me-2"
									@click="toggleConfirmPassword">
									<i :class="showConfirmPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
								</button>
								<span v-if="errors.password_confirmation" class="text-danger small d-block mt-1">{{
									errors.password_confirmation[0] ||
									errors.password_confirmation }}</span>
							</div>

							<button class="btn btn-lg btn-theme w-100 mb-3" type="submit" :disabled="loading"
								v-if="!loading">
								Reset Password
							</button>
							<button class="btn btn-lg btn-theme w-100 mb-3" v-else>
								<div class="spinner-border spinner-border-sm" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</button>

							<div class="text-center mt-3">
								<router-link to="/" class="text-decoration-none">
									Remember your password? <span class="fw-bold text-secondary">Login</span>
								</router-link>
							</div>
						</form>
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
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import Toast from '@/components/Toast.vue';
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const toastRef = ref(null);
const loading = ref(false);
const errors = ref({});
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const passwordInput = ref(null);

const form = ref({
	email: route.query.email || '',
	otp: route.query.otp || '',
	password: '',
	password_confirmation: ''
});

const togglePassword = () => {
	showPassword.value = !showPassword.value;
};

const toggleConfirmPassword = () => {
	showConfirmPassword.value = !showConfirmPassword.value;
};

const resetPassword = () => {
	if (!form.value.password || !form.value.password_confirmation) {
		toastRef.value.show("Please fill all required fields.", TOAST_ERROR);
		return;
	}

	if (form.value.password !== form.value.password_confirmation) {
		toastRef.value.show("Passwords do not match.", TOAST_ERROR);
		return;
	}

	if (!form.value.email || !form.value.otp) {
		toastRef.value.show("Invalid reset link. Please request a new password reset.", TOAST_ERROR);
		router.push('/forgot-password');
		return;
	}

	loading.value = true;
	errors.value = {};

	authStore.auth({
		uri: 'reset-password',
		data: {
			email: form.value.email,
			otp: form.value.otp,
			password: form.value.password,
			password_confirmation: form.value.password_confirmation
		},
		meta: { loadingRef: loading }
	})
		.then((response) => {
			toastRef.value.show("Password reset successfully. Redirecting to login...", TOAST_SUCCESS);
			setTimeout(() => {
				router.push('/');
			}, 1500);
		})
		.catch((error) => {
			errors.value = error.response?.data?.errors || {};
			if (error.response?.data?.message) {
				toastRef.value.show(error.response.data.message, TOAST_ERROR);
			} else if (error.response?.data?.errors) {
				const firstError = Object.values(error.response.data.errors)[0];
				if (firstError && firstError[0]) {
					toastRef.value.show(firstError[0], TOAST_ERROR);
				}
			} else {
				toastRef.value.show('Password reset failed. Please try again.', TOAST_ERROR);
			}
		});
};

onMounted(() => {
	if (!form.value.email || !form.value.otp) {
		toastRef.value.show("Invalid reset link. Please request a new password reset.", TOAST_ERROR);
		router.push('/forgot-password');
		return;
	}

	if (passwordInput.value) {
		passwordInput.value.focus();
	}
});
</script>

