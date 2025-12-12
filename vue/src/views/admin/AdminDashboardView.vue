<template>
	<div class="container py-4">
		<h4 class="mb-4">Admin Dashboard</h4>

		<!-- Stats Cards -->
		<div class="row g-3 mb-4">
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Total Users</h6>
						<h3>{{ stats.total_users || 0 }}</h3>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Total Moods</h6>
						<h3>{{ stats.total_moods || 0 }}</h3>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Total Reflections</h6>
						<h3>{{ stats.total_reflections || 0 }}</h3>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Today's Activity</h6>
						<h3>{{ (stats.today_moods || 0) + (stats.today_reflections || 0) }}</h3>
					</div>
				</div>
			</div>
		</div>

		<!-- Tabs -->
		<ul class="nav nav-tabs mb-3" id="adminTabs" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="moods-tab" data-bs-toggle="tab" data-bs-target="#moods"
					type="button" role="tab">Moods</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="reflections-tab" data-bs-toggle="tab" data-bs-target="#reflections"
					type="button" role="tab">Daily Reflections</button>
			</li>
		</ul>

		<div class="tab-content" id="adminTabsContent">
			<!-- Moods Tab -->
			<div class="tab-pane fade show active" id="moods" role="tabpanel">
				<div class="card">
					<div class="card-body">
						<div v-if="loadingMoods" class="text-center py-4">
							<div class="spinner-border" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</div>
						<div v-else-if="moods.length === 0" class="text-center py-4 text-muted">
							No moods recorded yet.
						</div>
						<div v-else>
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>User</th>
											<th>Mood</th>
											<th>Description</th>
											<th>Date</th>
											<th>Time</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="mood in moods" :key="mood.id">
											<td>
												<div>{{ mood.user?.name || 'N/A' }}</div>
												<small class="text-muted">{{ mood.user?.email || '' }}</small>
											</td>
											<td><span class="badge bg-primary">{{ mood.mood_label }}</span></td>
											<td>{{ mood.description || '-' }}</td>
											<td>{{ formatDate(mood.date) }}</td>
											<td>{{ formatTime(mood.created_at) }}</td>
										</tr>
									</tbody>
								</table>
							</div>
							<Pagination v-if="moodsPagination" :pagination="moodsPagination"
								@page-changed="onMoodsPageChange" />
						</div>
					</div>
				</div>
			</div>

			<!-- Reflections Tab -->
			<div class="tab-pane fade" id="reflections" role="tabpanel">
				<div class="card">
					<div class="card-body">
						<div v-if="loadingReflections" class="text-center py-4">
							<div class="spinner-border" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</div>
						<div v-else-if="reflections.length === 0" class="text-center py-4 text-muted">
							No reflections recorded yet.
						</div>
						<div v-else>
							<div v-for="reflection in reflections" :key="reflection.id" class="card mb-3">
								<div class="card-header">
									<strong>{{ reflection.user?.name || 'N/A' }}</strong>
									<small class="text-muted ms-2">{{ reflection.user?.email || '' }}</small>
									<span class="badge bg-secondary float-end">{{ formatDate(reflection.date) }} {{
										formatTime(reflection.created_at) }}</span>
								</div>
								<div class="card-body">
									<div class="mb-3">
										<strong class="text-primary">1️⃣ What happened today that stayed in your
											mind?</strong>
										<p class="mb-0 mt-1">{{ reflection.q1 || '-' }}</p>
									</div>
									<div class="mb-3">
										<strong class="text-primary">2️⃣ What went through your mind right after
											that?</strong>
										<p class="mb-0 mt-1">{{ reflection.q2 || '-' }}</p>
									</div>
									<div class="mb-3">
										<strong class="text-primary">3️⃣ How did that make you feel?</strong>
										<p class="mb-0 mt-1"><span class="badge bg-info">{{ reflection.q3 || '-'
												}}</span></p>
									</div>
									<div class="mb-3">
										<strong class="text-primary">4️⃣ What did you do next?</strong>
										<p class="mb-0 mt-1">{{ reflection.q4 || '-' }}</p>
									</div>
									<div class="mb-3">
										<strong class="text-primary">5️⃣ If you look at it now, is there another way to
											see it?</strong>
										<p class="mb-0 mt-1">{{ reflection.q5 || '-' }}</p>
									</div>
									<div>
										<strong class="text-primary">6️⃣ How do you feel now?</strong>
										<p class="mb-0 mt-1">Rating: <span class="badge bg-success">{{ reflection.q6 ||
											'-' }}/5</span></p>
									</div>
								</div>
							</div>
							<Pagination v-if="reflectionsPagination" :pagination="reflectionsPagination"
								@page-changed="onReflectionsPageChange" />
						</div>
					</div>
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

const stats = ref({});
const moods = ref([]);
const reflections = ref([]);
const moodsPagination = ref(null);
const reflectionsPagination = ref(null);
const loadingMoods = ref(false);
const loadingReflections = ref(false);
const hasRedirected = ref(false); // Prevent multiple redirects

// Pagination state
const moodsPage = ref(1);
const reflectionsPage = ref(1);
const moodsPerPage = 5;
const reflectionsPerPage = 1;

// Admin token is handled by authStore now

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

const fetchDashboardStats = () => {
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		if (!hasRedirected.value) {
			hasRedirected.value = true;
			router.push({ name: 'AdminLoginView' });
		}
		return;
	}

	authStore.get({
		uri: 'admin/dashboard-stats',
		meta: {}
	})
		.then((response) => {
			stats.value = response.data.stats || {};
		})
		.catch((error) => {
			if (error.response?.status === 401) {
				// Clear admin session and redirect (only once)
				if (!hasRedirected.value) {
					hasRedirected.value = true;
					sessionStorage.removeItem('admin-token');
					sessionStorage.removeItem('admin-user');
					sessionStorage.removeItem('is-admin');
					router.push({ name: 'AdminLoginView' });
				}
			} else {
				toastRef.value?.show('Failed to load dashboard stats.', TOAST_ERROR);
			}
		});
};

const fetchMoods = (page = 1) => {
	loadingMoods.value = true;
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		loadingMoods.value = false;
		return;
	}

	authStore.get({
		uri: `admin/moods?page=${page}&per_page=${moodsPerPage}`,
		meta: {}
	})
		.then((response) => {
			moods.value = response.data.moods || [];
			moodsPagination.value = response.data.pagination || null;
			moodsPage.value = page;
		})
		.catch((error) => {
			if (error.response?.status === 401) {
				// Clear admin session and redirect (only once)
				if (!hasRedirected.value) {
					hasRedirected.value = true;
					sessionStorage.removeItem('admin-token');
					sessionStorage.removeItem('admin-user');
					sessionStorage.removeItem('is-admin');
					router.push({ name: 'AdminLoginView' });
				}
			} else {
				toastRef.value?.show('Failed to load moods.', TOAST_ERROR);
			}
		})
		.finally(() => {
			loadingMoods.value = false;
		});
};

const onMoodsPageChange = (page) => {
	fetchMoods(page);
};

const fetchReflections = (page = 1) => {
	loadingReflections.value = true;
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		loadingReflections.value = false;
		return;
	}

	authStore.get({
		uri: `admin/reflections?page=${page}&per_page=${reflectionsPerPage}`,
		meta: {}
	})
		.then((response) => {
			reflections.value = response.data.reflections || [];
			reflectionsPagination.value = response.data.pagination || null;
			reflectionsPage.value = page;
		})
		.catch((error) => {
			if (error.response?.status === 401) {
				// Clear admin session and redirect (only once)
				if (!hasRedirected.value) {
					hasRedirected.value = true;
					sessionStorage.removeItem('admin-token');
					sessionStorage.removeItem('admin-user');
					sessionStorage.removeItem('is-admin');
					router.push({ name: 'AdminLoginView' });
				}
			} else {
				toastRef.value?.show('Failed to load reflections.', TOAST_ERROR);
			}
		})
		.finally(() => {
			loadingReflections.value = false;
		});
};

const onReflectionsPageChange = (page) => {
	fetchReflections(page);
};

onMounted(() => {
	// Verify admin is authenticated before making API calls
	const adminToken = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!adminToken || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		return;
	}

	// Admin is authenticated, fetch data
	fetchDashboardStats();
	fetchMoods();
	fetchReflections();
});
</script>
