<style>
.vc-arrow,
.vc-title {
	background-color: transparent !important;
}

.calendar-loading {
	background-color: var(--base-background-color);
	border-radius: 1rem;
	height: 270px;
	display: grid;
	grid-template-rows: 1fr max-content;
	box-shadow: var(--container-box-shadow);
}
</style>

<template>
	<div class="container mt-4">
		<div class="mb-3 d-flex justify-content-end gap-3">
			<div class="spinner-grow text-warning" role="status" v-if="isCalendarLoadingRef">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>
		<VCalendar v-model:current-page="currentPage" color="orange" :is-dark="true" :attributes="attrs" :rows="1"
			:columns="1" expanded @dayclick="onDayClick" @update:pages="onMonthChange" />
		<div class="list-group mt-3" v-if="selectedDay">
			<a href="javascript:void(0)" class="list-group-item list-group-item-action">
				<p class="my-0 text-center"><small>{{ selectedDay?.ariaLabel }}</small></p>
			</a>
			<a href="#" class="list-group-item list-group-item-action list-group-item-secondary"
				v-if="activities.length === 0">
				<div class="d-flex justify-content-center">
					<p class="mb-0">No activities found</p>
				</div>
			</a>
			<!-- <template v-for="(activity, index) in activities" :key="index">
				<router-link :to="`/events/show/${activity.id}`" class="list-group-item list-group-item-action list-group-item-primary" v-if="activity.activity_type === 'EVENT'">
					<div class="d-flex justify-content-start">
						<div class="avatar avatar-50 rounded-circle me-2" :class="activity.is_published ? 'bg-success' : 'bg-primary'">
							<span class="h5 text-light">E</span>
						</div>
						<div class="flex-fill">
							<p class="mb-0">{{ activity.name }}</p>
							<small>pax: {{ activity.max_pax }}</small>
							<p class="mb-0">{{ activity.created_by }}</p>
							<div class="d-flex justify-content-between">
								<small>{{ moment(`${activity.start_date} ${activity.start_time}`, 'YYYY-MM-DD HH:mm:ss').format('hh:mm A') }}</small>
								<span class=" mx-2 badge badge-light text-bg-dark">{{ activity.status }}</span>
							</div>
						</div>
					</div>
				</router-link>
				<router-link :to="`/tasks/show/${activity.id}`" class="list-group-item list-group-item-action list-group-item-warning" v-else>
					<div class="d-flex justify-content-start">
						<div class="avatar avatar-50 bg-orange rounded-circle me-2">
							<span class="h5 text-light">T</span>
						</div>
						<div class="flex-fill">
							<p class="mb-0">{{ activity.task_type }} of {{ activity.event.name }}</p>
							<p class="mb-0">{{ activity.task_users?.map(taskUser => taskUser.user?.username).join(', ') || 'No users assigned' }}</p>
							<div class="d-flex justify-content-between">
								<small>{{ moment(`${activity.start_date} ${activity.start_time}`, 'YYYY-MM-DD HH:mm:ss').format('hh:mm A') }}</small>
								<span class=" mx-2 badge badge-light text-bg-dark">{{ activity.status }}</span>
							</div>
						</div>
					</div>
				</router-link>
			</template> -->
			<template v-for="(activity, index) in activities" :key="index">
				<a href="javascript:void(0)" class="list-group-item list-group-item-action list-group-item-primary"
					v-if="activity.activity_type === 'EVENT'">
					<div class="d-flex justify-content-start">
						<div class="avatar avatar-50 rounded-circle me-2"
							:class="activity.is_published ? 'bg-success' : 'bg-primary'">
							<span class="h5 text-light">E</span>
						</div>
						<div class="flex-fill">
							<p class="mb-0">{{ activity.name }}</p>
							<p class="mb-0">{{ activity.created_by || '-' }}</p>
							<p class="mb-0">{{ formatTime(activity.start_date, activity.start_time) }}</p>
							<p class="mb-0">{{ activity.venue }}</p>
							<button class="btn btn-sm btn-accent">Watch Live <i class="bi bi-eye"></i></button>
							<div class="d-flex justify-content-end">
							</div>
						</div>
					</div>
				</a>
				<a href="javascript:void(0)" class="list-group-item list-group-item-action list-group-item-warning"
					v-else>
					<div class="d-flex justify-content-start">
						<div class="avatar avatar-50 bg-orange rounded-circle me-2">
							<span class="h5 text-light">W</span>
						</div>
						<div class="flex-fill">
							<p class="mb-0">{{ activity.topic }}</p>
							<p class="mb-0">{{ activity.instructor || '-' }}</p>
							<p class="mb-0">{{ formatTime(activity.start_date, activity.start_time) }}</p>
							<p class="mb-0">{{ activity.venue }}</p>
							<button class="btn btn-sm btn-accent">Watch Live <i class="bi bi-eye"></i></button>
							<div class="d-flex justify-content-between">
							</div>
						</div>
					</div>
				</a>
			</template>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, inject, watch } from 'vue'
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import axios from 'axios';
import moment from 'moment'
import 'v-calendar/style.css';

const API_URL = import.meta.env.VITE_API_URL;
const authStore = useAuthStore();
const today = new Date();
const selectedDay = ref(null);
const currentPage = ref({
	month: today.getMonth() + 1,
	year: today.getFullYear()
});
const lastLoadedPage = ref({ month: null, year: null })
const attrs = ref([]);
const activities = ref([]);
const showToast = inject('showToast')
const route = useRoute();
const router = useRouter();
const isCalendarLoadingRef = ref(false);

/**
 * Loaders
 */
onMounted(() => {
	loadDataForMonth(currentPage.value.year, currentPage.value.month)
});

const loadDataForMonth = (year, month) => {
	selectedDay.value = null;
	lastLoadedPage.value = { month, year }
	attrs.value = []
	isCalendarLoadingRef.value = true;

	// Fetch events from API
	authStore.get({
		uri: `events?year=${year}&month=${month}`,
		meta: {}
	})
		.then((response) => {
			const events = response.data.events || [];
			const seenIds = new Set(); // Track seen event IDs to prevent duplicates

			events.forEach((event) => {
				// Skip if we've already processed this event
				if (seenIds.has(event.id)) {
					return;
				}
				seenIds.add(event.id);

				const eventDate = new Date(event.start_date);
				// API already filters by year/month, but double-check to be safe
				if (eventDate.getMonth() + 1 === month && eventDate.getFullYear() === year) {
					attrs.value.push({
						key: `event-${event.id}`,
						highlight: {
							backgroundColor: event.activity_type === 'EVENT' ? '#28a745' : '#ffc107',
							borderRadius: '50%'
						},
						dates: eventDate,
						customData: {
							id: event.id,
							name: event.name,
							topic: event.topic,
							activity_type: event.activity_type,
							start_date: event.start_date,
							start_time: event.start_time,
							created_by: event.created_by,
							instructor: event.instructor,
							venue: event.venue,
							status: event.status,
							max_pax: event.max_pax,
							is_published: event.is_published,
						},
					});
				}
			});
		})
		.catch((error) => {
			console.error('Failed to load events:', error);
			// Show error message if API fails
			if (showToast) {
				showToast('Failed to load events. Please try again later.', TOAST_ERROR);
			}
		})
		.finally(() => {
			isCalendarLoadingRef.value = false;
		});
}

const onMonthChange = (pages) => {
	const page = pages[0]

	if (page.month === lastLoadedPage.value.month && page.year === lastLoadedPage.value.year) {
		return
	}

	loadDataForMonth(page.year, page.month)
}

const onDayClick = (day) => {
	selectedDay.value = day;
	// Remove duplicates by using a Map with event ID as key
	const uniqueActivities = new Map();
	day.attributes.forEach(attr => {
		if (attr.customData && attr.customData.id) {
			// Use event ID as key to prevent duplicates
			if (!uniqueActivities.has(attr.customData.id)) {
				uniqueActivities.set(attr.customData.id, attr.customData);
			}
		}
	});
	activities.value = Array.from(uniqueActivities.values());
}

const formatTime = (date, time) => {
	return moment(`${date} ${time}`, 'YYYY-MM-DD HH:mm:ss').format('hh:mm A');
}

</script>