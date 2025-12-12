<template>
	<div class="container py-4">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h4>Events Management</h4>
			<button class="btn btn-primary" @click="openCreateModal">
				<i class="bi bi-plus-circle me-1"></i> Add Event
			</button>
		</div>

		<div class="card">
			<div class="card-body">
				<div v-if="loading" class="text-center py-4">
					<div class="spinner-border" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
				<div v-else-if="events.length === 0" class="text-center py-4 text-muted">
					No events found.
				</div>
				<div v-else>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Type</th>
									<th>Name/Topic</th>
									<th>Date</th>
									<th>Time</th>
									<th>Venue</th>
									<th>Status</th>
									<th>Published</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="event in events" :key="event.id">
									<td>
										<span class="badge" :class="event.activity_type === 'EVENT' ? 'bg-primary' : 'bg-warning'">
											{{ event.activity_type }}
										</span>
									</td>
									<td>{{ event.name || event.topic }}</td>
									<td>{{ formatDate(event.start_date) }}</td>
									<td>{{ formatTime(event.start_time) }}</td>
									<td>{{ event.venue }}</td>
									<td>
										<span class="badge" :class="getStatusBadgeClass(event.status)">
											{{ event.status }}
										</span>
									</td>
									<td>
										<span class="badge" :class="event.is_published ? 'bg-success' : 'bg-secondary'">
											{{ event.is_published ? 'Yes' : 'No' }}
										</span>
									</td>
									<td>
										<button class="btn btn-sm btn-outline-primary me-1" @click="openEditModal(event)">
											<i class="bi bi-pencil"></i>
										</button>
										<button class="btn btn-sm btn-outline-danger" @click="confirmDelete(event)">
											<i class="bi bi-trash"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<Pagination v-if="pagination" :pagination="pagination" @page-changed="onPageChange" />
				</div>
			</div>
		</div>

		<!-- Create/Edit Modal -->
		<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true" ref="eventModal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">{{ editingEvent ? 'Edit Event' : 'Create Event' }}</h5>
						<button type="button" class="btn-close" @click="closeModal"></button>
					</div>
					<div class="modal-body">
						<form @submit.prevent="saveEvent">
							<div class="mb-3">
								<label class="form-label">Activity Type *</label>
								<select class="form-select" v-model="eventForm.activity_type" required>
									<option value="EVENT">Event</option>
									<option value="WORKSHOP">Workshop</option>
								</select>
							</div>

							<div class="mb-3" v-if="eventForm.activity_type === 'EVENT'">
								<label class="form-label">Event Name *</label>
								<input type="text" class="form-control" v-model="eventForm.name" required />
							</div>

							<div class="mb-3" v-if="eventForm.activity_type === 'WORKSHOP'">
								<label class="form-label">Workshop Topic *</label>
								<input type="text" class="form-control" v-model="eventForm.topic" required />
							</div>

							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="form-label">Start Date *</label>
									<input type="date" class="form-control" v-model="eventForm.start_date" required />
								</div>
								<div class="col-md-6 mb-3">
									<label class="form-label">Start Time *</label>
									<input type="time" class="form-control" v-model="eventForm.start_time" required />
								</div>
							</div>

							<div class="mb-3" v-if="eventForm.activity_type === 'EVENT'">
								<label class="form-label">Created By *</label>
								<input type="text" class="form-control" v-model="eventForm.created_by" required />
							</div>

							<div class="mb-3" v-if="eventForm.activity_type === 'WORKSHOP'">
								<label class="form-label">Instructor *</label>
								<input type="text" class="form-control" v-model="eventForm.instructor" required />
							</div>

							<div class="mb-3">
								<label class="form-label">Venue *</label>
								<input type="text" class="form-control" v-model="eventForm.venue" required />
							</div>

							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="form-label">Status</label>
									<select class="form-select" v-model="eventForm.status">
										<option value="Open">Open</option>
										<option value="Confirmed">Confirmed</option>
										<option value="Cancelled">Cancelled</option>
									</select>
								</div>
								<div class="col-md-6 mb-3">
									<label class="form-label">Max Participants</label>
									<input type="number" class="form-control" v-model.number="eventForm.max_pax" min="1" />
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label">Description</label>
								<textarea class="form-control" v-model="eventForm.description" rows="3"></textarea>
							</div>

							<div class="mb-3 form-check">
								<input type="checkbox" class="form-check-input" v-model="eventForm.is_published" id="isPublished" />
								<label class="form-check-label" for="isPublished">Published</label>
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
								<button type="submit" class="btn btn-primary" :disabled="isSaving">
									<span v-if="isSaving" class="spinner-border spinner-border-sm me-1"></span>
									{{ editingEvent ? 'Update' : 'Create' }}
								</button>
							</div>
						</form>
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
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';
import { Modal } from 'bootstrap';

const router = useRouter();
const authStore = useAuthStore();
const toastRef = ref(null);
const eventModal = ref(null);

const events = ref([]);
const pagination = ref(null);
const loading = ref(false);
const isSaving = ref(false);
const currentPage = ref(1);
const editingEvent = ref(null);

const eventForm = ref({
	activity_type: 'EVENT',
	name: '',
	topic: '',
	start_date: '',
	start_time: '',
	created_by: '',
	instructor: '',
	venue: '',
	status: 'Open',
	max_pax: null,
	is_published: true,
	description: '',
});

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
		case 'Confirmed':
			return 'bg-success';
		case 'Cancelled':
			return 'bg-danger';
		default:
			return 'bg-info';
	}
};

const fetchEvents = (page = 1) => {
	loading.value = true;
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		loading.value = false;
		return;
	}

	authStore.get({
		uri: `admin/events?page=${page}&per_page=15`,
		meta: {}
	})
		.then((response) => {
			events.value = response.data.events || [];
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
				toastRef.value?.show('Failed to load events.', TOAST_ERROR);
			}
		})
		.finally(() => {
			loading.value = false;
		});
};

const onPageChange = (page) => {
	fetchEvents(page);
};

const openCreateModal = () => {
	editingEvent.value = null;
	resetForm();
	const modal = new Modal(eventModal.value);
	modal.show();
};

const openEditModal = (event) => {
	editingEvent.value = event;

	// Format date for HTML date input (YYYY-MM-DD)
	let formattedDate = '';
	if (event.start_date) {
		const date = new Date(event.start_date);
		// Handle both date strings and Date objects
		const year = date.getFullYear();
		const month = String(date.getMonth() + 1).padStart(2, '0');
		const day = String(date.getDate()).padStart(2, '0');
		formattedDate = `${year}-${month}-${day}`;
	}

	eventForm.value = {
		activity_type: event.activity_type,
		name: event.name || '',
		topic: event.topic || '',
		start_date: formattedDate,
		start_time: event.start_time ? event.start_time.substring(0, 5) : '', // Format HH:mm
		created_by: event.created_by || '',
		instructor: event.instructor || '',
		venue: event.venue || '',
		status: event.status || 'Open',
		max_pax: event.max_pax || null,
		is_published: event.is_published !== undefined ? event.is_published : true,
		description: event.description || '',
	};
	const modal = new Modal(eventModal.value);
	modal.show();
};

const closeModal = () => {
	const modal = Modal.getInstance(eventModal.value);
	if (modal) {
		modal.hide();
	}
	resetForm();
};

const resetForm = () => {
	eventForm.value = {
		activity_type: 'EVENT',
		name: '',
		topic: '',
		start_date: '',
		start_time: '',
		created_by: '',
		instructor: '',
		venue: '',
		status: 'Open',
		max_pax: null,
		is_published: true,
		description: '',
	};
	editingEvent.value = null;
};

const saveEvent = () => {
	isSaving.value = true;
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		return;
	}

	const payload = {
		...eventForm.value,
		start_time: eventForm.value.start_time + ':00', // Add seconds
	};

	const uri = editingEvent.value
		? `admin/events/${editingEvent.value.id}`
		: 'admin/events';

	const method = editingEvent.value ? 'put' : 'post';

	authStore[method]({
		uri: uri,
		data: payload,
		meta: {}
	})
		.then(() => {
			toastRef.value?.show(
				editingEvent.value ? 'Event updated successfully' : 'Event created successfully',
				TOAST_SUCCESS
			);
			closeModal();
			fetchEvents(currentPage.value);
		})
		.catch((error) => {
			if (error.response?.status === 401) {
				sessionStorage.removeItem('admin-token');
				sessionStorage.removeItem('admin-user');
				sessionStorage.removeItem('is-admin');
				router.push({ name: 'AdminLoginView' });
			} else {
				const message = error.response?.data?.message || 'Failed to save event.';
				toastRef.value?.show(message, TOAST_ERROR);
			}
		})
		.finally(() => {
			isSaving.value = false;
		});
};

const confirmDelete = (event) => {
	if (confirm(`Are you sure you want to delete "${event.name || event.topic}"?`)) {
		deleteEvent(event.id);
	}
};

const deleteEvent = (id) => {
	const token = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!token || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		return;
	}

	authStore.delete({
		uri: `admin/events/${id}`,
		meta: {}
	})
		.then(() => {
			toastRef.value?.show('Event deleted successfully', TOAST_SUCCESS);
			fetchEvents(currentPage.value);
		})
		.catch((error) => {
			if (error.response?.status === 401) {
				sessionStorage.removeItem('admin-token');
				sessionStorage.removeItem('admin-user');
				sessionStorage.removeItem('is-admin');
				router.push({ name: 'AdminLoginView' });
			} else {
				toastRef.value?.show('Failed to delete event.', TOAST_ERROR);
			}
		});
};

onMounted(() => {
	const adminToken = sessionStorage.getItem('admin-token');
	const isAdmin = sessionStorage.getItem('is-admin') === 'true';

	if (!adminToken || !isAdmin) {
		router.push({ name: 'AdminLoginView' });
		return;
	}

	fetchEvents();
});
</script>

