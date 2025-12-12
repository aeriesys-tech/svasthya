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

						<!-- Step 1: Request OTP -->
						<div v-if="step === 1">
							<div class="form-floating mb-3">
								<input type="email" class="form-control" :class="{ 'border-danger': errors.email }"
									id="email" placeholder="Enter your email address" v-model.trim="form.email"
									ref="emailInput" autofocus />
								<label for="email">Email*</label>
								<span v-if="errors.email" class="text-danger small d-block mt-1">{{ errors.email[0] ||
									errors.email
								}}</span>
							</div>

							<button class="btn btn-lg btn-theme w-100 mb-3" @click="requestOtp" :disabled="loading"
								v-if="!loading">
								Send OTP
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
						</div>

						<!-- Step 2: OTP Verification -->
						<div v-else-if="step === 2">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" :class="{ 'border-danger': errors.otp }"
									id="otp" placeholder="Enter OTP" v-model.trim="form.otp" />
								<label for="otp">Enter OTP*</label>
								<span v-if="errors.otp" class="text-danger small d-block mt-1">{{ errors.otp[0] ||
									errors.otp
								}}</span>
							</div>

							<button class="btn btn-lg btn-theme w-100 mb-3" @click="verifyOtp" :disabled="loading"
								v-if="!loading">
								Verify OTP
							</button>
							<button class="btn btn-lg btn-theme w-100 mb-3" v-else>
								<div class="spinner-border spinner-border-sm" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</button>

							<!-- Resend OTP Section -->
							<div class="text-center small mb-3">
								<span v-if="countdown > 0">
									Resend OTP in <strong>{{ countdown }}</strong> seconds
								</span>
								<button v-else class="btn btn-link p-0 text-decoration-none" @click="resendOtp"
									:disabled="loading">
									Resend OTP
								</button>
							</div>

							<button class="btn btn-outline-secondary w-100 mb-3" @click="goBackToStep(1)">← Back</button>
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
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import Toast from '@/components/Toast.vue';
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';

const router = useRouter();
const authStore = useAuthStore();
const toastRef = ref(null);
const step = ref(1);
const loading = ref(false);
const countdown = ref(0);
const errors = ref({});
const emailInput = ref(null);
let timer = null;

const form = ref({
	email: '',
	otp: ''
});

function startCountdown() {
	countdown.value = 30;
	timer = setInterval(() => {
		if (countdown.value > 0) {
			countdown.value--;
		} else {
			clearInterval(timer);
		}
	}, 1000);
}

const requestOtp = () => {
	if (!form.value.email) {
		toastRef.value.show("Please enter your email address.", TOAST_ERROR);
		return;
	}

	loading.value = true;
	errors.value = {};

	authStore.auth({
		uri: 'forgot-password',
		data: {
			email: form.value.email
		},
		meta: { loadingRef: loading }
	})
		.then((response) => {
			toastRef.value.show("OTP sent successfully to your email.", TOAST_SUCCESS);
			step.value = 2;
			startCountdown();
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
				toastRef.value.show('Failed to send OTP. Please try again.', TOAST_ERROR);
			}
		});
};

const resendOtp = () => {
	loading.value = true;
	errors.value = {};

	authStore.auth({
		uri: 'forgot-password/resend-otp',
		data: {
			email: form.value.email
		},
		meta: { loadingRef: loading }
	})
		.then((response) => {
			toastRef.value.show("OTP resent successfully to your email.", TOAST_SUCCESS);
			startCountdown();
		})
		.catch((error) => {
			errors.value = error.response?.data?.errors || {};
			if (error.response?.data?.message) {
				toastRef.value.show(error.response.data.message, TOAST_ERROR);
			} else {
				toastRef.value.show('Failed to resend OTP. Please try again.', TOAST_ERROR);
			}
		});
};

const verifyOtp = () => {
	if (!form.value.otp) {
		toastRef.value.show("Please enter the OTP.", TOAST_ERROR);
		return;
	}

	loading.value = true;
	errors.value = {};

	authStore.auth({
		uri: 'forgot-password/verify-otp',
		data: {
			email: form.value.email,
			otp: form.value.otp
		},
		meta: { loadingRef: loading }
	})
		.then((response) => {
			toastRef.value.show("OTP verified successfully.", TOAST_SUCCESS);
			clearInterval(timer);
			// Redirect to reset password page
			router.push({
				path: '/reset-password',
				query: {
					email: form.value.email,
					otp: form.value.otp
				}
			});
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
				toastRef.value.show('OTP verification failed. Please try again.', TOAST_ERROR);
			}
		});
};

const goBackToStep = (n) => {
	step.value = n;
	window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
	if (emailInput.value) {
		emailInput.value.focus();
	}
});

onUnmounted(() => {
	if (timer) clearInterval(timer);
});
</script>
