<template>
	<div class="container py-4">
		<h2 class="mb-3">Book Your Appointment</h2>

		<div class="row">
			<!-- Wellness Officer Dropdown -->
			<div class="col-12">
				<div class="form-floating mb-3">
					<select class="form-select" :class="{ 'is-invalid': errors.officer }"
						v-model.trim="appointment.officer" @blur="validateOfficer">
						<option value="">Select</option>
						<option v-for="(officer, index) in wellnessOfficers" :key="index" :value="officer.id">
							{{ officer.name }}
						</option>
					</select>
					<label>Well Being Officer*</label>
					<span class="error-wrapper invalid-feedback">{{ errors.officer }}</span>
				</div>
			</div>

			<!-- Appointment Date -->
			<div class="col-12">
				<div class="form-floating mb-3">
					<input type="date" class="form-control" :class="{ 'is-invalid': errors.date }" id="appointmentDate"
						v-model="appointment.date" @blur="validateDate" />
					<label for="appointmentDate">Select Date*</label>
					<span class="error-wrapper invalid-feedback">{{ errors.date }}</span>
				</div>
			</div>

			<!-- Appointment Time -->
			<div class="col-12">
				<div class="form-floating mb-3">
					<input type="time" class="form-control" :class="{ 'is-invalid': errors.time }" id="appointmentTime"
						v-model="appointment.time" @blur="validateTime" />
					<label for="appointmentTime">Select Time*</label>
					<span class="error-wrapper invalid-feedback">{{ errors.time }}</span>
				</div>
			</div>

			<!-- Place of WBO Available (Dropdown) -->
			<div class="col-12">
				<div class="form-floating mb-3">
					<select class="form-select" :class="{ 'is-invalid': errors.place }" id="appointmentPlace"
						v-model.trim="appointment.place" @blur="validatePlace">
						<option value="">Select</option>
						<option v-for="(place, index) in places" :key="index" :value="place">
							{{ place }}
						</option>
					</select>
					<label for="appointmentPlace">Place of WBO Available*</label>
					<span class="error-wrapper invalid-feedback">{{ errors.place }}</span>
				</div>
			</div>

			<!-- Available Slots -->
			<div class="col-12">
				<div class="form-floating mb-3">
					<select class="form-select" :class="{ 'is-invalid': errors.slots }" id="availableSlots"
						v-model.trim="appointment.slots" @blur="validateSlots">
						<option value="">Select</option>
						<option v-for="(slot, index) in availableSlots" :key="index" :value="slot">
							{{ slot }}
						</option>
					</select>
					<label for="availableSlots">Available Slots*</label>
					<span class="error-wrapper invalid-feedback">{{ errors.slots }}</span>
				</div>
			</div>

			<!-- Submit Button -->
			<div class="col-12">
				<button class="btn btn-outline-accent text-dark w-100" @click="submitBooking" :disabled="isSubmitting">
					<span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
					{{ isSubmitting ? 'Booking...' : 'Book Appointment' }}
				</button>
			</div>
		</div>
	</div>
	<Toast ref="toastRef" />
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import Toast from '@/components/Toast.vue'
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config'

const router = useRouter()
const authStore = useAuthStore()
const toastRef = ref(null)
const isSubmitting = ref(false)

const wellnessOfficers = [
	{ id: 1, name: 'Dr. Aisha Khan' },
	{ id: 2, name: 'Mr. Ramesh Patel' },
	{ id: 3, name: 'Dr. Nina Gupta' }
]

const places = [
	'Jayanagara',
	'Koramangala',
	'JP Nagara',
	'Bannerghatta'
]

const availableSlots = [
	'09:00 AM - 12:00 PM',
	'02:00 PM - 05:00 PM',
	'06:00 PM - 08:00 PM'
]

const appointment = reactive({
	officer: '',
	date: '',
	time: '',
	place: '',
	slots: ''
})

const errors = reactive({
	officer: '',
	date: '',
	time: '',
	place: '',
	slots: ''
})

function validateOfficer() {
	errors.officer = appointment.officer ? '' : 'Please select a wellness officer'
}

function validateDate() {
	errors.date = appointment.date ? '' : 'Please select a date'
}

function validateTime() {
	errors.time = appointment.time ? '' : 'Please select a time'
}

function validatePlace() {
	errors.place = appointment.place ? '' : 'Please select a place'
}

function validateSlots() {
	errors.slots = appointment.slots ? '' : 'Please select an available slot'
}

function submitBooking() {
	validateOfficer()
	validateDate()
	validateTime()
	validatePlace()
	validateSlots()

	if (!errors.officer && !errors.date && !errors.time && !errors.place && !errors.slots) {
		isSubmitting.value = true

		// Find officer name
		const selectedOfficer = wellnessOfficers.find(o => o.id === parseInt(appointment.officer))

		authStore.post({
			uri: 'appointment',
			data: {
				officer_id: parseInt(appointment.officer),
				officer_name: selectedOfficer?.name || '',
				appointment_date: appointment.date,
				appointment_time: appointment.time,
				place: appointment.place,
				slot: appointment.slots,
			},
			meta: {}
		})
			.then(() => {
				toastRef.value?.show('Appointment booked successfully!', TOAST_SUCCESS)
				// Reset form
				appointment.officer = ''
				appointment.date = ''
				appointment.time = ''
				appointment.place = ''
				appointment.slots = ''
				// Redirect after a short delay
				setTimeout(() => {
					router.push('/dashboard')
				}, 1500)
			})
			.catch((error) => {
				const message = error.response?.data?.message || 'Failed to book appointment. Please try again.'
				toastRef.value?.show(message, TOAST_ERROR)
			})
			.finally(() => {
				isSubmitting.value = false
			})
	}
}
</script>
