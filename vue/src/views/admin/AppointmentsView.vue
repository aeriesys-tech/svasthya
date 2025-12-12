<template>
	<div class="container py-4">
		<h4 class="mb-4">Appointments Management</h4>

		<div class="card">
			<div class="card-body">
				<div v-if="loading" class="text-center py-4">
					<div class="spinner-border" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
				<div v-else-if="appointments.length === 0" class="text-center py-4 text-muted">
					No appointments found.
				</div>
				<div v-else>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>User</th>
									<th>Officer</th>
									<th>Date</th>
									<th>Time</th>
									<th>Place</th>
									<th>Slot</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="appointment in appointments" :key="appointment.id">
									<td>
										<div>{{ appointment.user?.name || 'N/A' }}</div>
										<small class="text-muted">{{ appointment.user?.email || '' }}</small>
										<div v-if="appointment.user?.mobile" class="small text-muted">
											{{ appointment.user.mobile }}
										</div>
									</td>
									<td>{{ appointment.officer_name }}</td>
									<td>{{ formatDate(appointment.appointment_date) }}</td>
									<td>{{ formatTime(appointment.appointment_time) }}</td>
									<td>{{ appointment.place }}</td>
									<td>{{ appointment.slot }}</td>
									<td>
										<span class="badge mb-2 d-block" :class="getStatusBadgeClass(appointment.status)">
											{{ appointment.status }}
										</span>
										<select class="form-select form-select-sm" :value="appointment.status"
											@change="updateStatus(appointment.id, $event.target.value)">
											<option value="pending">Pending</option>
											<option value="confirmed">Confirmed</option>
											<option value="cancelled">Cancelled</option>
											<option value="completed">Completed</option>
										</select>
									</td>
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
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';

const router = useRouter();
const authStore = useAuthStore();
const toastRef = ref(null);

const appointments = ref([]);
const pagination = ref(null);
const loading = ref(false);
const currentPage = ref(1);

const formatDate = (dateString) => {
	if (!dateString) return '-';
	const date = new Date(dateString);
	return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatTime = (timeString) => {
	if (!timeString) return '-';
	// Handle both "HH:mm:ss" and "HH:mm" formats
	const parts = timeString.split(':');
	return `${parts[0]}:${parts[1]}`;
};

const getStatusBadgeClass = (status) => {
	switch (status) {
		case 'confirmed':
			return 'bg-success';
		case 'cancelled':
			return 'bg-danger';
		case 'completed':
			return 'bg-info';
		default:
			return 'bg-warning';
	}
};

const fetchAppointments = (page = 1) => {
	loading.value = true;
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		loading.value = false;
		return;
	}

	authStore.get({
		uri: `admin/appointments?page=${page}&per_page=15`,
		meta: {}
	})
		.then((response) => {
			appointments.value = response.data.appointments || [];
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
				toastRef.value?.show('Failed to load appointments.', TOAST_ERROR);
			}
		})
		.finally(() => {
			loading.value = false;
		});
};

const updateStatus = (appointmentId, newStatus) => {
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		return;
	}

	authStore.put({
		uri: `admin/appointments/${appointmentId}/status`,
		data: {
			status: newStatus,
		},
		meta: {}
	})
		.then(() => {
			toastRef.value?.show('Appointment status updated successfully.', TOAST_SUCCESS);
			// Refresh the list
			fetchAppointments(currentPage.value);
		})
		.catch((error) => {
			if (error.response?.status === 401) {
				sessionStorage.removeItem('admin-token');
				sessionStorage.removeItem('admin-user');
				sessionStorage.removeItem('is-admin');
				router.push({ name: 'AdminLoginView' });
			} else {
				const message = error.response?.data?.message || 'Failed to update appointment status.';
				toastRef.value?.show(message, TOAST_ERROR);
				// Refresh to revert the change
				fetchAppointments(currentPage.value);
			}
		});
};

const onPageChange = (page) => {
	fetchAppointments(page);
};

onMounted(() => {
	const adminToken = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!adminToken || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		return;
	}

	fetchAppointments();
});
</script>

