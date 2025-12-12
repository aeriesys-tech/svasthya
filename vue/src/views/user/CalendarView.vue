<style>
	.vc-arrow, .vc-title {
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
		<VCalendar
			v-model:current-page="currentPage"
			color="orange"
			:is-dark="true"
			:attributes="attrs"
			:rows="1"
			:columns="1"
			expanded
			@dayclick="onDayClick"
			@update:pages="onMonthChange" 
		/>
		<div class="list-group mt-3" v-if="selectedDay">
			<a href="javascript:void(0)" class="list-group-item list-group-item-action">
				<p class="my-0 text-center"><small>{{ selectedDay?.ariaLabel }}</small></p>
			</a>
			<a href="#" class="list-group-item list-group-item-action list-group-item-secondary" v-if="activities.length === 0">
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
				<a href="javascript:void(0)" class="list-group-item list-group-item-action list-group-item-primary" v-if="activity.activity_type === 'EVENT'">
					<div class="d-flex justify-content-start">
						<div class="avatar avatar-50 rounded-circle me-2" :class="activity.is_published ? 'bg-success' : 'bg-primary'">
							<span class="h5 text-light">E</span>
						</div>
						<div class="flex-fill">
							<p class="mb-0">{{ activity.name }}</p>
							<p class="mb-0">{{ activity.created_by }}</p>
							<p class="mb-0">{{ formatTime(activity.start_date, activity.start_time) }}</p>
							<p class="mb-0">{{ activity.venue }}</p>
							<button class="btn btn-sm btn-accent">Watch Live <i class="bi bi-eye"></i></button>
							<div class="d-flex justify-content-end">
							</div>
						</div>
					</div>
				</a>
				<a href="javascript:void(0)" class="list-group-item list-group-item-action list-group-item-warning" v-else>
					<div class="d-flex justify-content-start">
						<div class="avatar avatar-50 bg-orange rounded-circle me-2">
							<span class="h5 text-light">W</span>
						</div>
						<div class="flex-fill">
							<p class="mb-0">{{ activity.topic }}</p>
							<p class="mb-0">{{ activity.instructor }}</p>
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
    import axios from 'axios';
	import moment from 'moment'
	import 'v-calendar/style.css';

	const API_URL = import.meta.env.VITE_API_URL;
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
		// Dummy Data Source
	const dummyEvents = [
		{
			id: 1,
			name: 'Stress Management Seminar',
			start_date: '2025-07-25',
			start_time: '10:00:00',
			created_by: 'Dr. Smith',
			venue: 'City Wellness Center, Hall A',
			status: 'Confirmed',
			max_pax: 50,
			is_published: true,
			activity_type: 'EVENT'
		},
		{
			id: 2,
			name: 'Morning Yoga for Wellness',
			start_date: '2025-07-28',
			start_time: '07:30:00',
			created_by: 'Yoga Studio',
			venue: 'Riverside Yoga Park',
			status: 'Open',
			max_pax: 20,
			is_published: true,
			activity_type: 'EVENT'
		},
		{
			id: 3,
			name: 'Sleep & Mental Health Talk',
			start_date: '2025-08-03',
			start_time: '16:00:00',
			created_by: 'Dr. Alka Sharma',
			venue: 'Community Health Auditorium',
			status: 'Confirmed',
			max_pax: 100,
			is_published: true,
			activity_type: 'EVENT'
		},
		{
			id: 4,
			name: 'Walking Meditation Session',
			start_date: '2025-08-10',
			start_time: '06:30:00',
			created_by: 'MindfulSteps Org',
			venue: 'Botanical Garden Trail',
			status: 'Open',
			max_pax: 30,
			is_published: false,
			activity_type: 'EVENT'
		},
		{
			id: 5,
			name: 'Healthy Habits for Office Workers',
			start_date: '2025-09-14',
			start_time: '11:00:00',
			created_by: 'Dr. Ramesh Patel',
			venue: 'Tech Park Conference Hall 2',
			status: 'Confirmed',
			max_pax: 60,
			is_published: true,
			activity_type: 'EVENT'
		},
		{
			id: 6,
			name: 'Outdoor Tai Chi Experience',
			start_date: '2025-09-21',
			start_time: '07:00:00',
			created_by: 'TaiChi Club',
			venue: 'Lakeside Ground, Sector 5',
			status: 'Open',
			max_pax: 25,
			is_published: false,
			activity_type: 'EVENT'
		}
	];

	const dummyWorkshops = [
		{
			id: 101,
			topic: 'Anger Management Techniques',
			start_date: '2025-07-25',
			start_time: '14:00:00',
			instructor: 'Dr. Ramchandra Prasad',
			venue: 'Wellness Hall, Block B',
			status: 'Confirmed',
			activity_type: 'WORKSHOP'
		},
		{
			id: 102,
			topic: 'Nutrition for Energy',
			start_date: '2025-07-30',
			start_time: '11:00:00',
			instructor: 'Dr. Gopi',
			venue: 'Diet & Nutrition Lab, Health Tower',
			status: 'Open',
			activity_type: 'WORKSHOP'
		},
		{
			id: 103,
			topic: 'Work-Life Balance for Professionals',
			start_date: '2025-08-06',
			start_time: '10:30:00',
			instructor: 'Dr. Aisha Khan',
			venue: 'Hotel GreenView, Banquet Room 1',
			status: 'Confirmed',
			activity_type: 'WORKSHOP'
		},
		{
			id: 104,
			topic: 'Overcoming Burnout',
			start_date: '2025-08-23',
			start_time: '15:00:00',
			instructor: 'Prof. Sunil Desai',
			venue: 'Metro Wellness Centre',
			status: 'Confirmed',
			activity_type: 'WORKSHOP'
		},
		{
			id: 105,
			topic: 'Posture & Ergonomics for Desk Jobs',
			start_date: '2025-09-05',
			start_time: '09:00:00',
			instructor: 'Physio Expert Team',
			venue: 'Urban Health Club, Workshop Zone',
			status: 'Open',
			activity_type: 'WORKSHOP'
		},
		{
			id: 106,
			topic: 'Healing Through Breathwork',
			start_date: '2025-09-25',
			start_time: '17:30:00',
			instructor: 'Nina Gupta',
			venue: 'Zen Studio, City Center',
			status: 'Confirmed',
			activity_type: 'WORKSHOP'
		}
	];

	// Load dummy data to calendar
	onMounted(() => {
		loadDataForMonth(currentPage.value.year, currentPage.value.month)
	});

	const loadDataForMonth = (year, month) => {
		selectedDay.value = null;
		lastLoadedPage.value = { month, year }
		attrs.value = []

		// Add Events
		dummyEvents.forEach((event, i) => {
			if (parseInt(event.start_date.split('-')[1]) === month) {
				attrs.value.push({
					key: `event-${i}`,
					highlight: {
						backgroundColor: '#28a745',
						borderRadius: '50%'
					},
					dates: new Date(event.start_date),
					customData: event,
				});
			}
		});

		// Add Workshops
		dummyWorkshops.forEach((workshop, i) => {
			if (parseInt(workshop.start_date.split('-')[1]) === month) {
				attrs.value.push({
					key: `workshop-${i}`,
					highlight: {
						backgroundColor: '#28a745',
						borderRadius: '50%'
					},
					dates: new Date(workshop.start_date),
					customData: workshop,
				});
			}
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
		activities.value = day.attributes.map(attr => attr.customData);
	}

	const formatTime = (date, time) => {
		return moment(`${date} ${time}`, 'YYYY-MM-DD HH:mm:ss').format('hh:mm A');
	}

</script>