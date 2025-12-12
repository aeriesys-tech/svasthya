<template>
	<div class="container py-4">
		<h4 class="mb-4">Feedbacks Management</h4>

		<div class="card">
			<div class="card-body">
				<div v-if="loading" class="text-center py-4">
					<div class="spinner-border" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
				<div v-else-if="feedbacks.length === 0" class="text-center py-4 text-muted">
					No feedbacks found.
				</div>
				<div v-else>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>User</th>
									<th>Rating</th>
									<th>Opinion</th>
									<th>Date</th>
									<th>Time</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="feedback in feedbacks" :key="feedback.id">
									<td>
										<div>{{ feedback.user?.name || 'N/A' }}</div>
										<small class="text-muted">{{ feedback.user?.email || '' }}</small>
									</td>
									<td>
										<div class="d-flex align-items-center">
											<span class="me-2">{{ feedback.rating }}/5</span>
											<div class="star-rating-small">
												<i
													v-for="n in 5"
													:key="n"
													class="bi"
													:class="n <= feedback.rating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
												></i>
											</div>
										</div>
									</td>
									<td>
										<div v-if="feedback.opinion" class="text-break" style="max-width: 400px;">
											{{ feedback.opinion }}
										</div>
										<span v-else class="text-muted">-</span>
									</td>
									<td>{{ formatDate(feedback.created_at) }}</td>
									<td>{{ formatTime(feedback.created_at) }}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<Pagination v-if="pagination" :pagination="pagination" @page-changed="onPageChange" />
				</div>
			</div>
		</div>
	</div>
	<Toast ref="toastRef" />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import Toast from '@/components/Toast.vue';
import Pagination from '@/components/Pagination.vue';
import { TOAST_ERROR } from '@/utils/config';

const router = useRouter();
const authStore = useAuthStore();
const toastRef = ref(null);

const feedbacks = ref([]);
const pagination = ref(null);
const loading = ref(false);
const currentPage = ref(1);

const formatDate = (dateString) => {
	if (!dateString) return '-';
	const date = new Date(dateString);
	return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatTime = (dateString) => {
	if (!dateString) return '-';
	const date = new Date(dateString);
	return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const fetchFeedbacks = (page = 1) => {
	loading.value = true;
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		loading.value = false;
		return;
	}

	authStore.get({
		uri: `admin/feedbacks?page=${page}&per_page=15`,
		meta: {}
	})
		.then((response) => {
			feedbacks.value = response.data.feedbacks || [];
			pagination.value = response.data.pagination || null;
			currentPage.value = page;
		})
		.catch((error) => {
			if (error.response?.status === 401) {
				sessionStorage.removeItem('admin-token');
				sessionStorage.removeItem('admin-user');
				sessionStorage.removeItem('is-admin');
				router.push({ name: 'AdminLoginView' });
			} else {
				toastRef.value?.show('Failed to load feedbacks.', TOAST_ERROR);
			}
		})
		.finally(() => {
			loading.value = false;
		});
};

const onPageChange = (page) => {
	fetchFeedbacks(page);
};

onMounted(() => {
	const adminToken = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!adminToken || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		return;
	}

	fetchFeedbacks();
});
</script>

<style scoped>
.star-rating-small i {
	font-size: 0.9rem;
}
</style>

