<template>
	<div class="container mt-5 mb-5">
		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="card shadow-lg border-0 rounded-4">
					<div class="card-body p-4">
						<h3 class="text-center mb-4 fw-bold">Feedback</h3>

						<!-- Rating Section -->
						<div class="text-center mb-3">
							<div class="star-rating">
								<i v-for="n in 5" :key="n" class="bi"
									:class="n <= rating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
									@click="setRating(n)" style="font-size: 2rem; cursor: pointer;"></i>
							</div>
							<p class="mt-2 mb-0">
								<small v-if="rating">You rated {{ rating }} out of 5</small>
								<small v-else class="text-muted">Click a star to rate</small>
							</p>
						</div>

						<!-- Opinion -->
						<div class="form-floating mb-4">
							<textarea class="form-control" id="opinion" placeholder="Your opinion (optional)"
								style="height: 120px" v-model.trim="opinion"></textarea>
							<label for="opinion">Your opinion (optional)</label>
						</div>

						<!-- Submit Button -->
						<button class="btn btn-danger w-100 py-2 fw-semibold" @click="submitFeedback"
							:disabled="isSubmitting">
							<span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
							{{ isSubmitting ? 'Submitting...' : 'Submit Feedback' }}
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<Toast ref="toastRef" />
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from '@/stores/authStore';
import Toast from '@/components/Toast.vue';
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';

const router = useRouter();
const authStore = useAuthStore();
const toastRef = ref(null);
const isSubmitting = ref(false);
const rating = ref(0);
const opinion = ref("");

function setRating(n) {
	rating.value = n;
}

function submitFeedback() {
	if (rating.value === 0) {
		toastRef.value?.show("Please select a rating before submitting.", TOAST_ERROR);
		return;
	}

	isSubmitting.value = true;

	authStore.post({
		uri: 'feedback',
		data: {
			rating: rating.value,
			opinion: opinion.value || null,
		},
		meta: {}
	})
		.then(() => {
			toastRef.value?.show("Thank you for your valuable feedback!", TOAST_SUCCESS);
			// Reset form
			rating.value = 0;
			opinion.value = "";
			// Redirect after a short delay
			setTimeout(() => {
				router.push("/dashboard");
			}, 1500);
		})
		.catch((error) => {
			const message = error.response?.data?.message || 'Failed to submit feedback. Please try again.';
			toastRef.value?.show(message, TOAST_ERROR);
		})
		.finally(() => {
			isSubmitting.value = false;
		});
}
</script>

<style scoped>
.star-rating i {
	transition: transform 0.2s;
}

.star-rating i:hover {
	transform: scale(1.2);
}
</style>
