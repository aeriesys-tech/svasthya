<template>
	<div class="container py-4">

		<!-- Quotes -->
		<div class="card mb-3">
			<div class="px-2 theme-red">
				<button class="btn btn-outline-accent my-1 w-100 bg-theme-l-gradient-light">
					<div class="row">
						<div class="col text-start">🌞</div>
						<div class="col text-center">{{ t('daily_affirmation') }}</div>
						<div class="col text-end"></div>
					</div>
				</button>
			</div>

			<div id="healthTipsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
				<div class="carousel-inner">
					<div v-for="(tip, index) in healthTips" :key="index"
						:class="['carousel-item', { active: index === 0 }]">
						<div class="d-flex justify-content-center align-items-center">
							<div class="text-center">
								<p class="lead">{{ tip }}</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Greeting -->
		<h4 class="mb-4 ps-2">Hi {{ userName }},&nbsp;{{ t('greeting') }}</h4>

		<!-- ⭐ FINAL MOOD TRACKER ⭐ -->
		<div class="card p-3 mb-4">
			<h5>{{ t('how_are_you') }}</h5>

			<div class="row mt-3">
				<div class="col-4 col-md-2 col-lg-2 text-center mb-3" v-for="(mood, index) in moods" :key="index"
					@click="selectedMood = mood.label"
					:class="selectedMood === mood.label ? 'fw-bold text-danger' : 'text-secondary'"
					style="cursor: pointer;">
					<img :src="mood.src" class="mood-icon mb-1" />
					<div class="small">{{ mood.label }}</div>
				</div>
			</div>

			<input type="text" class="w-100 ps-3 rounded" placeholder="Describe your mood"
				v-model.trim="moodDescription" />

			<button class="btn btn-danger w-100 mt-1" @click="saveMood" :disabled="isSavingMood" v-if="!isSavingMood">
				{{ t('save_mood') }}
			</button>
			<button class="btn btn-danger w-100 mt-1" disabled v-else>
				<div class="spinner-border spinner-border-sm" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
			</button>
		</div>

		<!-- Reflection Modal -->
		<div class="modal fade" id="reflectionModal" tabindex="-1" aria-hidden="true" ref="reflectionModal">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content rounded-4 border-danger">
					<div class="modal-header border-0">
						<h5 class="modal-title fw-bold">{{ t('daily_reflection_loop') }}</h5>
						<button type="button" class="btn-close" @click="closeReflectionModal"></button>
					</div>
					<div class="modal-body">

						<!-- Question Wizard -->
						<div v-if="currentStep === 1" class="mb-3">
							<label class="form-label fw-semibold">
								What happened today that stayed in your mind?
							</label>
							<input type="text" class="form-control" v-model="reflectionAnswers.q1"
								placeholder="Write one line..." />
						</div>

						<div v-if="currentStep === 2" class="mb-3">
							<label class="form-label fw-semibold">
								What went through your mind right after that?
							</label>
							<input type="text" class="form-control" v-model="reflectionAnswers.q2"
								placeholder="Your first thought..." />
						</div>

						<div v-if="currentStep === 3" class="mb-3">
							<label class="form-label fw-semibold">
								How did that make you feel?
							</label>
							<div class="d-flex flex-wrap gap-2 mt-2">
								<button v-for="(option, index) in reflectionMoods" :key="index" type="button"
									class="btn" :class="reflectionAnswers.q3 === option.label
										? 'btn-danger text-white'
										: 'btn-outline-secondary text-dark'" @click="reflectionAnswers.q3 = option.label">
									{{ option.emoji }} {{ option.label }}
								</button>
							</div>
						</div>

						<div v-if="currentStep === 4" class="mb-3">
							<label class="form-label fw-semibold">
								What did you do next?
							</label>
							<input type="text" class="form-control" v-model="reflectionAnswers.q4"
								placeholder="Your action or reaction..." />
						</div>

						<div v-if="currentStep === 5" class="mb-3">
							<label class="form-label fw-semibold">
								If you look at it now, is there another way to see it?
							</label>
							<input type="text" class="form-control" v-model="reflectionAnswers.q5"
								placeholder="A fairer or lighter view..." />
						</div>

						<div v-if="currentStep === 6" class="mb-3">
							<label class="form-label fw-semibold">
								How do you feel now?
							</label>
							<div class="d-flex justify-content-between mt-2">
								<div v-for="i in 5" :key="i">
									<input type="radio" class="btn-check" :name="'reflectionRate'" :id="'rate' + i"
										:value="i" v-model="reflectionAnswers.q6" />
									<label class="btn btn-outline-secondary btn-sm" :for="'rate' + i">{{ i }}</label>
								</div>
							</div>
							<small class="text-muted d-block mt-1">
								⭐ 1 = Tense 2 = Calmer 3 = Okay 4 = Light 5 = Peaceful
							</small>
						</div>

					</div>
					<div class="modal-footer border-0">
						<button v-if="currentStep > 1" type="button" class="btn btn-secondary" @click="prevStep"
							:disabled="isSavingReflection">
							Previous
						</button>

						<button v-if="currentStep < 6" type="button" class="btn btn-danger" @click="nextStep"
							:disabled="!isCurrentStepAnswered || isSavingReflection">
							Next
						</button>

						<button v-if="currentStep === 6" type="button" class="btn btn-success" @click="submitReflection"
							:disabled="!isCurrentStepAnswered || isSavingReflection" v-show="!isSavingReflection">
							Submit
						</button>
						<button v-if="currentStep === 6" type="button" class="btn btn-success" disabled
							v-show="isSavingReflection">
							<div class="spinner-border spinner-border-sm" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Intro Video -->
		<h5 class="mb-3 text-center">{{ t('intro_svasthya') }}</h5>

		<div class="video-container">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/N_28cfeGeQM?si=OwnoXY8Y_bGTZI_-"
				frameborder="0" allowfullscreen></iframe>
		</div>

		<div class="mb-3 mt-3">
			<img src="/images/dailyselfcheck.png" class="img-fluid contain" />
		</div>

		<!-- Progress -->
		<div class="mb-4 ps-2 mt-3">
			<div class="row g-3 mt-2">
				<div class="col-6 text-center">
					<!-- <div class="border rounded p-4 hover-box" @click="router.push('/assessment')">
                        <div>Physical Assessment</div>
                    </div> -->
					<div class="video-container mt-3 rounded">
						<iframe width="560" height="315"
							src="https://www.youtube.com/embed/nxThLGUFNmw?si=3HuWagJup2JI_yFK" frameborder="0"
							allowfullscreen></iframe>
					</div>
					<div class="card audio-card mt-3 text-center">
						<i class="bi bi-mic fs-4 mb-1"></i>
						<audio controls class="w-100">
							<source src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"
								type="audio/mpeg" />
						</audio>
					</div>
				</div>

				<div class="col-6 text-center">
					<!-- <div class="border rounded p-4 hover-box" @click="router.push('/assessment')">
                        <div>Mental Health Assessment</div>
                    </div> -->
					<div class="video-container mt-3 rounded">
						<iframe width="560" height="315"
							src="https://www.youtube.com/embed/IyqLDC4US6Y?si=JxfAOoLMtt8YPezP" frameborder="0"
							allowfullscreen></iframe>
					</div>
					<div class="card audio-card mt-3 text-center">
						<i class="bi bi-mic fs-4 mb-1"></i>
						<audio controls class="w-100">
							<source src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3"
								type="audio/mpeg" />
						</audio>
					</div>
				</div>
			</div>
		</div>

		<!-- Quick Activities -->
		<div class="mb-4 ps-2">
			<h5>{{ t('quick_activities') }}</h5>
			<div class="row g-3 mt-2">
				<div v-for="(activity, index) in activities" :key="index" class="col-12">
					<div class="border rounded p-2 h-100 d-flex flex-column justify-content-between"
						@click="navigateTo(activity.path)" style="cursor: pointer;">
						<div>
							<i :class="['fa-2x mb-1', activity.icon]"></i>
							{{ activity.title }}
						</div>
						<small class="text-muted">{{ activity.duration }}</small>
					</div>
				</div>
			</div>
		</div>

	</div>
	<Toast ref="toastRef" />
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from 'vue-i18n';
import { useAuthStore } from "@/stores/authStore";
import Toast from '@/components/Toast.vue';
import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';

const router = useRouter();
const { t } = useI18n();
const authStore = useAuthStore();
const selectedMood = ref(null);
const moodDescription = ref('');
const isSavingMood = ref(false);
const isSavingReflection = ref(false);
const toastRef = ref(null);

const userName = computed(() => {
	const user = authStore.getUser;
	return user?.name || "User";
});

const reflectionMoods = [
	{ label: "Good", emoji: "😊" },
	{ label: "Low", emoji: "😞" },
	{ label: "Upset", emoji: "😠" },
	{ label: "Neutral", emoji: "😐" },
];

const moods = [
	{ label: "HAPPY", src: "/images/moods/HAPPY.png" },
	{ label: "ANGRY", src: "/images/moods/ANGRY.png" },
	{ label: "CONFUSED", src: "/images/moods/CONFUSED.png" },
	{ label: "LOVED", src: "/images/moods/LOVED.png" },
	{ label: "DISGUSTED", src: "/images/moods/DISGUSTED.png" },
	{ label: "ANXIOUS", src: "/images/moods/ANXIOUS.png" },
	{ label: "SICK", src: "/images/moods/SICK.png" },
	{ label: "FUNNY", src: "/images/moods/FUNNY.png" },
	{ label: "LONELY", src: "/images/moods/LONELY.png" },
	{ label: "DISAPPOINTED", src: "/images/moods/DISAPPOINTED.png" },
	{ label: "SAD", src: "/images/moods/SAD.png" },
	{ label: "WORRIED", src: "/images/moods/WORRIED.png" },
	{ label: "EXCITED", src: "/images/moods/EXCITED.png" },
	{ label: "ANNOYED", src: "/images/moods/ANNOYED.png" },
	{ label: "AFRAID", src: "/images/moods/AFRAID.png" },
	{ label: "TIRED", src: "/images/moods/TIRED.png" },
	{ label: "NERVOUS", src: "/images/moods/NERVOUS.png" },
	{ label: "AMAZED", src: "/images/moods/AMAZED.png" },
	{ label: "SILLY", src: "/images/moods/SILLY.png" },
	{ label: "STRESSED", src: "/images/moods/STRESSED.png" },
	{ label: "EMBARRASSED", src: "/images/moods/EMBARRASSED.png" },
	{ label: "JEALOUS", src: "/images/moods/JEALOUS.png" },
	{ label: "FRUSTRATED", src: "/images/moods/FRUSTRATED.png" },
	{ label: "SCARED", src: "/images/moods/SCARED.png" },
	{ label: "SURPRISED", src: "/images/moods/SURPRISED.png" },
	{ label: "INSECURE", src: "/images/moods/INSECURE.png" },
	{ label: "HURT", src: "/images/moods/HURT.png" },
	{ label: "CONTENT", src: "/images/moods/CONTENT.png" },
	{ label: "MAD", src: "/images/moods/MAD.png" },
	{ label: "THANKFUL", src: "/images/moods/THANKFUL.png" }
];

const healthTips = ref([
	"Strength is not in shouting louder - it's in staying calmer.",
	"Drink at least 8 glasses of water a day.",
	"Take a 10-minute walk after meals to aid digestion.",
	"Get 7-8 hours of sleep every night.",
	"Include fruits and vegetables in every meal.",
	"Avoid screen time 30 minutes before bed.",
	"Stretch every morning to improve flexibility.",
	"Practice mindful breathing for stress relief."
]);

const activities = [
	{ title: 'Anger Thermometer & Punch Pillow', duration: '2 min', icon: 'bi bi-soundwave', path: '/activity/anger-thermometer' },
	{ title: 'THE FORK IN THE ROAD ACTIVITY', duration: '5 min', icon: 'bi bi-sign-railroad', path: '/activity/life-skills/head/decision-making' },
	{ title: 'Daily Target Practice – Micro Goal Tracking', duration: '5 min', icon: 'bi bi-bullseye', path: '/activity/life-skills/head/goal-setting' },
];

function navigateTo(path) {
	router.push(path);
}

const greeting = computed(() => {
	const hour = new Date().getHours();
	if (hour < 12) return "Good morning";
	if (hour < 17) return "Good afternoon";
	return "Good evening";
});

// Reflection modal logic
const currentStep = ref(1);
const reflectionAnswers = ref({
	q1: "", q2: "", q3: "", q4: "", q5: "", q6: null
});
const reflectionModal = ref(null);

const openReflectionModal = () => {
	const modalEl = new bootstrap.Modal(reflectionModal.value);
	modalEl.show();
};

const closeReflectionModal = () => {
	const modalEl = bootstrap.Modal.getInstance(reflectionModal.value);
	modalEl.hide();
};

const nextStep = () => {
	if (currentStep.value < 6) currentStep.value++;
};
const prevStep = () => {
	if (currentStep.value > 1) currentStep.value--;
};
const isCurrentStepAnswered = computed(() => {
	const step = currentStep.value;
	return step === 1 ? reflectionAnswers.value.q1.trim() !== ""
		: step === 2 ? reflectionAnswers.value.q2.trim() !== ""
			: step === 3 ? reflectionAnswers.value.q3 !== ""
				: step === 4 ? reflectionAnswers.value.q4.trim() !== ""
					: step === 5 ? reflectionAnswers.value.q5.trim() !== ""
						: step === 6 ? reflectionAnswers.value.q6 !== null
							: false;
});
const submitReflection = () => {
	if (!isCurrentStepAnswered.value) {
		return;
	}

	isSavingReflection.value = true;

	authStore.post({
		uri: 'reflection',
		data: {
			q1: reflectionAnswers.value.q1,
			q2: reflectionAnswers.value.q2,
			q3: reflectionAnswers.value.q3,
			q4: reflectionAnswers.value.q4,
			q5: reflectionAnswers.value.q5,
			q6: reflectionAnswers.value.q6,
			date: new Date().toISOString().split('T')[0],
		},
		meta: { loadingRef: isSavingReflection }
	})
		.then((response) => {
			toastRef.value?.show(response.data.message || 'Reflection saved successfully!', TOAST_SUCCESS);
			closeReflectionModal();
			currentStep.value = 1;
			reflectionAnswers.value = { q1: "", q2: "", q3: "", q4: "", q5: "", q6: null };
		})
		.catch((error) => {
			if (error.response?.data?.message) {
				toastRef.value?.show(error.response.data.message, TOAST_ERROR);
			} else if (error.response?.data?.errors) {
				const firstError = Object.values(error.response.data.errors)[0];
				if (firstError && firstError[0]) {
					toastRef.value?.show(firstError[0], TOAST_ERROR);
				}
			} else {
				toastRef.value?.show('Failed to save reflection. Please try again.', TOAST_ERROR);
			}
		})
		.finally(() => {
			isSavingReflection.value = false;
		});
};

const saveMood = () => {
	if (!selectedMood.value) {
		toastRef.value?.show('Please select a mood.', TOAST_ERROR);
		return;
	}

	isSavingMood.value = true;

	authStore.post({
		uri: 'mood',
		data: {
			mood_label: selectedMood.value,
			description: moodDescription.value || null,
			date: new Date().toISOString().split('T')[0],
		},
		meta: { loadingRef: isSavingMood }
	})
		.then((response) => {
			toastRef.value?.show(response.data.message || 'Mood saved successfully!', TOAST_SUCCESS);
			// Reset form
			selectedMood.value = null;
			moodDescription.value = '';
			openReflectionModal()
		})
		.catch((error) => {
			if (error.response?.data?.message) {
				toastRef.value?.show(error.response.data.message, TOAST_ERROR);
			} else if (error.response?.data?.errors) {
				const firstError = Object.values(error.response.data.errors)[0];
				if (firstError && firstError[0]) {
					toastRef.value?.show(firstError[0], TOAST_ERROR);
				}
			} else {
				toastRef.value?.show('Failed to save mood. Please try again.', TOAST_ERROR);
			}
		})
		.finally(() => {
			isSavingMood.value = false;
		});
};
</script>

<style scoped>
.mood-icon {
	width: 60px;
	height: 60px;
	object-fit: contain;
}

.video-container {
	position: relative;
	width: 100%;
	padding-bottom: 56.25%;
	height: 0;
}

.video-container iframe {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	border: none;
}

.hover-box {
	transition: background-color 0.3s ease;
}

.hover-box:hover {
	background-color: #A7C1A8;
}

.btn-close {
	cursor: pointer;
}

.reflectionbutton {
	background-color: #f8d7da;
	color: #111;
}

/* Prevent mobile active/focus from overriding selected btn-danger */
.modal-content .btn.btn-danger {
	background-color: #dc3545 !important;
	color: #fff !important;
	border-color: #dc3545 !important;
	box-shadow: none !important;
}
</style>
