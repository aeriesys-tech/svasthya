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

						<!-- Step 1: Basic Info -->
						<div v-if="step === 1">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" :class="{ 'border-danger': errors.name }"
									id="name" placeholder="Enter your name" v-model.trim="form.name" ref="nameInput"
									autofocus />
								<label for="name">Name*</label>
								<span v-if="errors.name" class="text-danger small d-block mt-1">{{ errors.name[0] ||
									errors.name
								}}</span>
							</div>

							<div class="form-floating mb-3">
								<input type="text" class="form-control" :class="{ 'border-danger': errors.kgid }"
									id="kgid" placeholder="KGID Number" v-model.trim="form.kgid" />
								<label for="kgid">KGID Number*</label>
								<span v-if="errors.kgid" class="text-danger small d-block mt-1">{{ errors.kgid[0] ||
									errors.kgid
								}}</span>
							</div>

							<div class="form-floating mb-3">
								<input type="email" class="form-control" :class="{ 'border-danger': errors.email }"
									id="email" placeholder="Enter your email address" v-model.trim="form.email" />
								<label for="email">Email*</label>
								<span v-if="errors.email" class="text-danger small d-block mt-1">{{ errors.email[0] ||
									errors.email
								}}</span>
							</div>

							<div class="form-floating mb-3">
								<input type="tel" class="form-control" :class="{ 'border-danger': errors.mobile }"
									id="mobile" placeholder="Mobile Number" v-model.trim="form.mobile" />
								<label for="mobile">Mobile Number*</label>
								<span v-if="errors.mobile" class="text-danger small d-block mt-1">{{ errors.mobile[0] ||
									errors.mobile
								}}</span>
							</div>

							<button class="btn btn-lg btn-theme w-100 mb-3" @click="requestOtp" :disabled="loading"
								v-if="!loading">
								Request OTP
							</button>
							<button class="btn btn-lg btn-theme w-100 mb-3" v-else>
								<div class="spinner-border spinner-border-sm" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</button>
							<div class="text-center mt-3">
								<router-link to="/" class="text-decoration-none">
									Already have an account? <span class="fw-bold text-secondary">Login</span>
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

							<button class="btn btn-outline-secondary w-100 mb-3" @click="goBackToStep(1)">←
								Back</button>
						</div>

						<!-- Step 3: Profile Details -->
						<div v-else-if="step === 3">
							<form @submit.prevent="submitProfile">
								<div class="row">
									<!-- Password -->
									<div class="col-12">
										<div class="position-relative mb-3">
											<div class="form-floating">
												<input :type="showPassword ? 'text' : 'password'" class="form-control"
													:class="{ 'border-danger': errors.password }" id="password"
													placeholder="Password" v-model.trim="form.password">
												<label for="password">Password*</label>
											</div>
											<button type="button"
												class="btn btn-square btn-link text-theme-1 position-absolute end-0 top-0 mt-2 me-2"
												@click="togglePassword">
												<i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
											</button>
											<span v-if="errors.password" class="text-danger small d-block mt-1">{{
												errors.password[0] ||
												errors.password }}</span>
										</div>
									</div>

									<!-- Date of Birth -->
									<div class="col-12">
										<div class="form-floating mb-3">
											<input type="date" class="form-control" id="dob" v-model="profile.dob" />
											<label for="dob">Date of Birth*</label>
										</div>
									</div>

									<!-- Gender -->
									<div class="col-12">
										<div class="form-floating mb-3">
											<select class="form-select" id="gender" v-model="profile.gender">
												<option value="" disabled>Select Gender</option>
												<option value="Male">Male</option>
												<option value="Female">Female</option>
												<option value="Others">Others</option>
											</select>
											<label for="gender">Gender*</label>
										</div>
									</div>

									<!-- Designation -->
									<div class="col-12">
										<div class="form-floating mb-3">
											<select class="form-select" id="designation" v-model="profile.designation">
												<option value="" disabled>Select Designation</option>
												<option v-for="d in designations" :key="d" :value="d">{{ d }}</option>
											</select>
											<label for="designation">Designation*</label>
										</div>
									</div>

									<!-- Division -->
									<div class="col-12">
										<div class="form-floating mb-3">
											<select class="form-select" id="division" v-model="profile.division"
												@change="updatePlaces">
												<option value="" disabled>Select Division</option>
												<option v-for="(division, index) in divisions" :key="index"
													:value="division">
													{{ division }}
												</option>
											</select>
											<label for="division">Division*</label>
										</div>
									</div>

									<!-- Current place of working -->
									<div class="col-12">
										<div class="form-floating mb-3">
											<select class="form-select" v-model="profile.currentWorking">
												<option value="" disabled>Select Current Place of Working</option>
												<option v-for="(place, index) in currentPlaces" :key="index"
													:value="place">
													{{ place }}
												</option>
											</select>
											<label for="current-working">Current Place of Working*</label>
										</div>
									</div>

									<!-- Qualification -->
									<div class="col-12">
										<div class="form-floating mb-3">
											<select class="form-select" id="qualification"
												v-model="profile.qualification">
												<option value="" disabled>Select Qualification</option>
												<option v-for="q in qualifications" :key="q" :value="q">{{ q }}</option>
											</select>
											<label for="qualification">Qualification*</label>
										</div>
									</div>
								</div>

								<button type="submit" class="btn btn-lg btn-theme w-100 mb-3" :disabled="loading"
									v-if="!loading">
									Submit & Continue
								</button>
								<button class="btn btn-lg btn-theme w-100 mb-3" v-else>
									<div class="spinner-border spinner-border-sm" role="status">
										<span class="visually-hidden">Loading...</span>
									</div>
								</button>

								<button class="btn btn-outline-secondary w-100 mb-3" type="button"
									@click="goBackToStep(2)">← Back</button>
							</form>
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
import { ref, onUnmounted, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';
import Toast from '@/components/Toast.vue';


const router = useRouter()
const authStore = useAuthStore()
const toastRef = ref(null);
const step = ref(1)
const loading = ref(false)
const countdown = ref(0)
const errors = ref({})
const showPassword = ref(false)
const nameInput = ref(null)
let timer = null

const togglePassword = () => {
	showPassword.value = !showPassword.value;
};

const form = ref({
	name: '',
	kgid: '',
	email: '',
	mobile: '',
	password: '',
	otp: ''
})

const profile = ref({
	dob: '',
	gender: '',
	designation: '',
	division: '',
	currentWorking: '',
	physicalHealth: '',
	qualification: ''
})

const designations = [
	"DGP", "ADGP", "IGP", "DIGP", "SGSP", "SP", "Addl SP",
	"DSP", "PI", "SI", "ASI", "HC", "PC"
]

// Updated divisions
const divisions = [
	"Central Division",
	"East Division",
	"West Division",
	"North Division",
	"South Division",
	"South East Division",
	"North East Division",
	"Whitefield Division",
	"Soth West Division",
	"North West Division",
	"Electronic City Division",
	"Traffic East Division",
	"Traffic West Division",
	"Traffic North Division",
	"Traffic South Division",
	"CAR South Division",
	"CAR North Division",
	"CAR Head Quarters",
	"CAR West"
]

// Dummy places associated with divisions
const divisionPlaces = {
	"Central Division": [
		"DCP CENTRAL",
		"ACP SESHADRIPURAM",
		"PI SESHADRIPURAM PS",
		"PI HIGH GROUND PS",
		"PI VYALIKAVAL PS",
		"PI SADASHIVA NAGAR PS",
		"ACP CUBBON PARK",
		"PI CUBBON PARK PS",
		"PI ASHOKNAGAR",
		"PI VIVEKNAGAR",
		"PI VIDHANA SOUDHA PS",
		"ACP HALASURU GATE",
		"PI HALASURU GATE PS",
		"PI WILSON GARDEN PS",
		"PI S.R NAGAR PS",
		"PI S.J PARK PS",
		"CEN PS (CYBER ECONOMIC NARCOTICS)"
	],
	"East Division": [
		"DCP EAST",
		"ACP HALASURU",
		"ACP PULAKESHINAGAR",
		"ACP BANASWADI",
		"ACP K G HALLI",
		"PI HALASURU PS",
		"PI INDIRANAGAR PS",
		"PI BYAPPANAHALLI PS",
		"PI J.B.NAGAR PS",
		"PI BHARATHINAGAR PS",
		"PI PULAKESHINAGAR PS",
		"PI SHIVAJINAGAR PS",
		"PI COMMERCIAL STREET PS",
		"PI SHIVAJINAGAR WOMEM PS",
		"PI K.G.HALLI PS",
		"PI RAMAMURTHY NAGAR PS",
		"PI HENNURU PS",
		"PI BANASWADI PS",
		"PI D.J.HALLI PS",
		"PI GOVINDRAJPURA PS",
		"CEN PS (CYBER ECONOMIC NARCOTIC)"
	],
	"West Division": [
		"DCP West",
		"ACP Chickpete",
		"PI Upparpete",
		"PI Kalasipalya",
		"PI City Market",
		"PI Chamarajpete",
		"PI Cottonpete",
		"ACP Byatarayanapura",
		"PI Byatarayanapura",
		"PI Annapurneshwari Nagar",
		"PI Chandra Layout",
		"PI J.J.Nagar",
		"PI R R NAGAR",
		"ACP Vijayanagar",
		"PI Vijayanagar",
		"PI Kamakshipalya",
		"PI BasaveshwaraNagar",
		"PI Govindarajanagar",
		"PI Magadi Road",
		"PI K.P.Agrahara",
		"PI Women PS",
		"ACP CEN WEST",
		"PI CEN Police Station-"
	],
	"North Division": [
		"DCP North",
		"ACP Malleshwaram",
		"PI Malleshwaram",
		"PI Srirampura",
		"PI Rajajinagar",
		"PI SubramanyaNagar",
		"PI Mahalakshmi Layout",
		"PI Nandini Layout",
		"PI Women PS",
		"ACP Yeshwanthapura",
		"PI Yeshwanthapura",
		"PI R.M.C Yard",
		"PI Jalahalli",
		"ACP J.C.Nagar",
		"PI J.C.Nagar",
		"PI Sanjayanagar",
		"PI Hebbala",
		"PI R.T.Nagar",
		"ACP CEN NORTH",
		"PI CEN Police Station- DCP"
	],
	"South Division": [
		"DCP South",
		"ACP Jayanagar",
		"PI Jayanagar",
		"PI Basavanagudi",
		"PI J.P.Nagar",
		"PI Siddapura",
		"PI Banashankari",
		"PI PuttenaHalli",
		"ACP V.V.Puram",
		"PI V V Puram",
		"PI Hanumantha Nagara",
		"PI K G Nagar",
		"PI ShankaraPuram",
		"PI Giri Nagar",
		"PI C K Acchukattu",
		"PI WOMEN B.Gudi",
		"ACP CEN SOUTH",
		"PI CEN Police Station-"
	],
	"South East Division": [
		"DCP South-East",
		"ACP Mico Layout",
		"PI Thilakanagar",
		"PI Mico Layout",
		"PI Bommanahalli",
		"PI Suddaguntepalya",
		"ACP Madivala",
		"PI Koramangala",
		"PI Madivala",
		"PI H.S.R Layout",
		"PI Adugodi",
		"PI Women",
		"ACP CEN South East",
		"PI CEN Police Station- HSR"
	],
	"North East Division": [
		"DCP NORTH-EAST",
		"ACP YELAHANKA",
		"PI YELAHANKA PS",
		"PI YELAHANKA NEW TOWN PS",
		"PI KODIGEHALLI PS",
		"PI VIDYARANYAPURA PS",
		"ACP SAMPIGEHALLI",
		"PI SAMPIGEHALLI PS",
		"PI KOTHANURU PS",
		"PI BHAGALURU PS",
		"PI AMRUTHAHALLI PS",
		"ACP DEVANAHALLI",
		"PI INTERNATIONAL AIRPORT PS",
		"PI DEVANAHALLI PS",
		"PI CHIKKAJALA PS",
		"ACP CEN PS",
		"CEN PS",
		"PI WOMEN PS NORTHEAST"
	],
	"Whitefield Division": [
		"DCP WhiteField",
		"ACP WhiteField",
		"PI White Field",
		"PI K.R.Puram",
		"PI Mahadevapura",
		"PI Kadugodi",
		"PI Avalahalli",
		"PI Women PS",
		"ACP Marathahalli",
		"PI Marathahalli",
		"PI H.A.L",
		"PI Bellanduru",
		"PI Varthur",
		"ACP CEN Whitefield",
		"PI CEN Police Station-Whitefield PS"
	],
	"Soth West Division": [
		"DCP South West",
		"ACP Kengeri",
		"PI Kengeri",
		"PI Jnanabharathi",
		"PI Kumbalagodu",
		"ACP SubramanyaPura",
		"PI SubramanyaPura",
		"PI KS Layout",
		"PI TalaghattaPura",
		"PI Konanakunte"
	],
	"North West Division": [
		"DCP North West",
		"ACP Peenya",
		"PI Peenya",
		"PI Rajagopal Nagar",
		"PI Bagalgunte",
		"PI Byadralli",
		"PI Soladevanahalli",
		"PI Gangammanagudi"
	],
	"Electronic City Division": [
		"DCP Electronic City",
		"ACP Electronic City",
		"PI Electronic City",
		"PI Bandepalya",
		"PI Hulimavu",
		"PI Beguru",
		"PI ParappanaAgrahara",
		"PI Hebbagodi"
	],
	"Traffic East Division": [
		"DCP TRAFFIC EAST OFFICE",
		"ACP EAST TRAFFIC OFFICE",
		"ACP WHITEFIELD TRAFFIC",
		"ACP TRAFFIC PLANNING",
		"BANASAWADI TR. PS",
		"FRAZER TOWN TR. PS",
		"SHIVAJINAGAR TR.PS",
		"INDIRANAGAR TR.PS",
		"HALASOOR TR.PS",
		"KGHALLI TR. PS",
		"AIRPORT TR. PS",
		"K.R.PURAM TR.PS",
		"WHITEFIELD TR. PS",
		"MAHADEVAPURA TR. PS"
	],
	"Traffic West Division": [
		"DCP Traffic West Division",
		"ACP Traffic Central Sub Div",
		"ACP Traffic West Sub Div",
		"ACP Traffic Vijayanagara Sub Div",
		"Ashoknagara Traffic PS",
		"Cubbon park Traffic PS",
		"Halsur gate Traffic PS",
		"High Grounds Traffic PS",
		"Sadashivanagara Traffic PS",
		"Wilson Garden traffic PS",
		"Upparpete Traffic PS",
		"Chamarajapete Traffic PS",
		"City Market Traffic PS",
		"Byatarayanapura Traffic PS",
		"Kengeri Traffic PS",
		"Kamakshipalya Traffic PS",
		"Vijaynagara Traffic PS",
		"Magadi Road Traffic Ps",
		"Jnanabharathi Traffic Ps",
		"Sheshadripuram Traffic PS"
	],
	"Traffic North Division": [
		"DCP Traffic North",
		"ACP Traffic North",
		"PI Tr. Rajajinagar",
		"PI Tr. Yeshwanthapura",
		"PI TR. Jalahalli",
		"PI Tr. Peenya",
		"PI Tr. R T Nagar",
		"PI Tr. Hebbal",
		"PI Tr. Devanahalli",
		"PI Tr. Yelahanka",
		"PI Tr. Chikkajala",
		"PI Tr. Hennuru"
	],
	"Traffic South Division": [
		"DCP Traffic South",
		"ACP Tr. Southeast",
		"PI Tr. MicoLayout",
		"PI Tr. Madivala",
		"PI Tr. Hulimavu",
		"PI Tr. Adugodi",
		"ACP Tr. HSR Layout",
		"PI Tr. Electronic City",
		"PI Tr. Bellanduru",
		"ACP Tr. South",
		"PI Tr. Jayanagara",
		"PI Tr. VV Puram",
		"PI Tr. Basavanagudi",
		"PI Tr. KS Layout",
		"PI Tr. Banashankari",
		"PI Tr. TalaghattaPur"
	],
	"CAR South Division": [
		"DCP CAR (South)",
		"CAR South Control Room",
		"ACP CAR South-I",
		"ACP CAR South-II",
		"ACP CAR South-III",
		"ACP CAR South-IV"
	],
	"CAR North Division": [
		"DCP CAR North",
		"CAR North Control Room",
		"ACP CAR North-I",
		"ACP CAR North-II",
		"ACP CAR North-III"
	],
	"CAR Head Quarters": [
		"CAR HQRS.Control Room",
		"DCP CAR (HQ)",
		"ACP CAR HQRS-I",
		"ACP CAR HQRS-II",
		"ACP CAR HQRS-III",
		"ACP CAR HQRS-IV",
		"ACP CAR HQRS-V"
	],
	"CAR West": [
		"DCP CAR (WEST)",
		"CAR WEST CONTROL ROOM",
		"ACP CAR WEST"
	]
}

const currentPlaces = ref([])

const updatePlaces = () => {
	currentPlaces.value = divisionPlaces[profile.value.division] || []
	profile.value.currentWorking = '' // reset current working place when division changes
}

const qualifications = [
	"10th", "PUC", "Degree", "Post Graduation", "MPhil", "PhD", "Others"
]

function startCountdown() {
	countdown.value = 30
	timer = setInterval(() => {
		if (countdown.value > 0) {
			countdown.value--
		} else {
			clearInterval(timer)
		}
	}, 1000)
}

const requestOtp = () => {
	if (!form.value.name || !form.value.kgid || !form.value.email || !form.value.mobile) {
		toastRef.value.show("Please fill all required fields.", TOAST_ERROR);
		return
	}

	loading.value = true
	errors.value = {}

	authStore.auth({
		uri: 'register/request-otp',
		data: {
			name: form.value.name,
			kgid: form.value.kgid,
			email: form.value.email,
			mobile: form.value.mobile
		},
		meta: { loadingRef: loading }
	})
		.then((response) => {
			toastRef.value.show("OTP sent successfully to your email.", TOAST_SUCCESS);
			step.value = 2
			startCountdown()
		})
		.catch((error) => {
			errors.value = error.response?.data?.errors || {}
			if (error.response?.data?.message) {
				toastRef.value.show(error.response.data.message, TOAST_ERROR);
			} else if (error.response?.data?.errors) {
				const firstError = Object.values(error.response.data.errors)[0]
				if (firstError && firstError[0]) {
					toastRef.value.show(firstError[0], TOAST_ERROR);
				}
			} else {
				toastRef.value.show('Failed to send OTP. Please try again.', TOAST_ERROR);
			}
		})
}

const resendOtp = () => {
	loading.value = true
	errors.value = {}

	authStore.auth({
		uri: 'register/resend-otp',
		data: {
			email: form.value.email
		},
		meta: { loadingRef: loading }
	})
		.then((response) => {
			toastRef.value.show("OTP resent successfully to your email.", TOAST_SUCCESS);
			startCountdown()
		})
		.catch((error) => {
			errors.value = error.response?.data?.errors || {}
			if (error.response?.data?.message) {
				toastRef.value.show(error.response.data.message, TOAST_ERROR);
			} else {
				toastRef.value.show('Failed to resend OTP. Please try again.', TOAST_ERROR);
			}
		})
}

const verifyOtp = () => {
	if (!form.value.otp) {
		toastRef.value.show("Please enter the OTP.", TOAST_ERROR);
		return
	}

	loading.value = true
	errors.value = {}

	authStore.auth({
		uri: 'register/verify-otp',
		data: {
			email: form.value.email,
			otp: form.value.otp
		},
		meta: { loadingRef: loading }
	})
		.then((response) => {
			toastRef.value.show("OTP verified successfully.", TOAST_SUCCESS);
			step.value = 3
			clearInterval(timer)
		})
		.catch((error) => {
			errors.value = error.response?.data?.errors || {}
			if (error.response?.data?.message) {
				toastRef.value.show(error.response.data.message, TOAST_ERROR);
			} else if (error.response?.data?.errors) {
				const firstError = Object.values(error.response.data.errors)[0]
				if (firstError && firstError[0]) {
					toastRef.value.show(firstError[0], TOAST_ERROR);
				}
			} else {
				toastRef.value.show('OTP verification failed. Please try again.', TOAST_ERROR);
			}
		})
}

const submitProfile = () => {
	// Validate required fields
	if (!form.value.password || !profile.value.dob ||
		!profile.value.gender || !profile.value.designation || !profile.value.division ||
		!profile.value.currentWorking || !profile.value.qualification) {
		toastRef.value.show("Please complete all required fields.", TOAST_ERROR);
		return
	}

	loading.value = true
	errors.value = {}

	// Combine all registration data
	const registrationData = {
		name: form.value.name,
		kgid: form.value.kgid,
		email: form.value.email,
		mobile: form.value.mobile,
		password: form.value.password,
		otp: form.value.otp,
		...profile.value
	}

	authStore.auth({
		uri: 'register',
		data: registrationData,
		meta: { loadingRef: loading }
	})
		.then((response) => {
			toastRef.value.show("Registration complete! Redirecting...", TOAST_SUCCESS);
			// If registration returns token and user, login automatically
			if (response.data.token && response.data.user) {
				authStore.setUser(response.data.user);
				authStore.setToken(response.data.token);
				setTimeout(() => {
					router.push('/dashboard')
				}, 1500)
			} else {
				// Otherwise redirect to login
				setTimeout(() => {
					router.push('/')
				}, 1500)
			}
		})
		.catch((error) => {
			errors.value = error.response?.data?.errors || {}
			if (error.response?.data?.message) {
				toastRef.value.show(error.response.data.message, TOAST_ERROR);
			} else if (error.response?.data?.errors) {
				const firstError = Object.values(error.response.data.errors)[0]
				if (firstError && firstError[0]) {
					toastRef.value.show(firstError[0], TOAST_ERROR);
				}
			} else {
				toastRef.value.show('Registration failed. Please try again.', TOAST_ERROR);
			}
		})
}

const goBackToStep = (n) => {
	step.value = n
	window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
	if (nameInput.value) {
		nameInput.value.focus();
	}
});

onUnmounted(() => {
	if (timer) clearInterval(timer)
});
</script>
