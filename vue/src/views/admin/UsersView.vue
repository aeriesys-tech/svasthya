<template>
	<div class="container py-4">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h4>Users Management</h4>
		</div>

		<div class="card">
			<div class="card-body">
				<div v-if="loading" class="text-center py-4">
					<div class="spinner-border" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
				<div v-else-if="users.length === 0" class="text-center py-4 text-muted">
					No users found.
				</div>
				<div v-else>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Email</th>
									<th>KGID</th>
									<th>Mobile</th>
									<th>Registered</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="user in users" :key="user.id">
									<td>{{ user.id }}</td>
									<td>{{ user.name || '-' }}</td>
									<td>{{ user.email || '-' }}</td>
									<td>{{ user.kgid || '-' }}</td>
									<td>{{ user.mobile || '-' }}</td>
									<td>{{ formatDate(user.created_at) }}</td>
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

const users = ref([]);
const pagination = ref(null);
const loading = ref(false);
const currentPage = ref(1);
const perPage = 15;
const hasRedirected = ref(false);

const formatDate = (dateString) => {
	if (!dateString) return '-';
	const date = new Date(dateString);
	return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const fetchUsers = (page = 1) => {
	loading.value = true;
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		loading.value = false;
		return;
	}

	authStore.get({
		uri: `admin/users?page=${page}&per_page=${perPage}`,
		meta: {}
	})
		.then((response) => {
			users.value = response.data.users || [];
			pagination.value = response.data.pagination || null;
			currentPage.value = page;
		})
		.catch((error) => {
			if (error.response?.status === 401) {
				if (!hasRedirected.value) {
					hasRedirected.value = true;
					sessionStorage.removeItem('admin-token');
					sessionStorage.removeItem('admin-user');
					sessionStorage.removeItem('is-admin');
					router.push({ name: 'AdminLoginView' });
				}
			} else {
				toastRef.value?.show('Failed to load users.', TOAST_ERROR);
			}
		})
		.finally(() => {
			loading.value = false;
		});
};

const onPageChange = (page) => {
	fetchUsers(page);
};

onMounted(() => {
	const adminToken = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!adminToken || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		return;
	}

	fetchUsers();
});
</script>

