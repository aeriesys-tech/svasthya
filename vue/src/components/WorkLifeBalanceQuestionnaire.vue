<template>
	<div class="container py-4">
		<h4 class="text-center mb-4">Work–Life Balance Assessment</h4>

		<div class="card shadow-sm">
			<div class="card-header bg-accent">
				<h5 class="mb-0">Answer honestly. Select one option per question.</h5>
			</div>

			<div class="card-body">

				<!-- Progress + Page indicator -->
				<div class="mb-3">
					<div class="progress" style="height: 12px;">
						<div
							class="progress-bar bg-primary"
							role="progressbar"
							:style="{ width: progressPercent + '%' }"
						></div>
					</div>

					<p class="mt-2 text-center fw-semibold">
						Page {{ page }} of {{ totalPages }}
					</p>
				</div>

				<!-- QUESTIONS (Paginated) -->
				<div
					v-for="(q, index) in paginatedQuestions"
					:key="index"
					class="mb-4"
				>
					<p class="fw-semibold">{{ currentStartIndex + index + 1 }}. {{ q.text }}</p>

					<div class="d-flex flex-column ms-3">
						<label
							v-for="(label, rIndex) in q.scale"
							:key="rIndex"
							class="form-check mb-1 text-start"
						>
							<input
								type="radio"
								class="form-check-input me-2"
								:name="`wlb-q-${currentStartIndex + index}`"
								:value="q.values[rIndex]"
								v-model.number="q.answer"
							/>
							{{ label }}
						</label>
					</div>
				</div>

				<!-- Pagination Buttons -->
				<div class="d-flex justify-content-between mt-3" v-if="!result">
					<button class="btn btn-outline-secondary" @click="previousPage" :disabled="page === 1">
						Previous
					</button>

					<button class="btn btn-outline-primary" @click="nextPage" :disabled="page === totalPages">
						Next
					</button>
				</div>

				<!-- Submit Button (only on last page) -->
				<div class="text-center mt-3" v-if="!result && page === totalPages">
					<button class="btn btn-success" @click="submit">
						Submit Assessment
					</button>
				</div>

				<!-- Results -->
				<div v-if="result" class="alert alert-info mt-3">
					<strong>Your Score:</strong> {{ result.score.toFixed(2) }} / 5<br />
					<strong>Interpretation:</strong> {{ result.interpretation }}
				</div>

				<!-- Close button -->
				<div v-if="result" class="text-center mt-3">
					<button class="btn btn-secondary" @click="closeComponent">
						Close
					</button>
				</div>

			</div>
		</div>
	</div>
</template>

<script setup>
import { reactive, ref, computed, watch } from "vue";

const emit = defineEmits(["close"]);

const SCALE_A = ["Always", "Almost always", "Sometimes", "Seldom", "Never", "No comment"];
const SCALE_B = ["Always", "Almost always", "Sometimes", "Seldom", "Never", "No comment"];
const SCALE_C = ["Very", "Satisfied", "Somewhat", "Hardly", "Not satisfied", "No comment"];

const VAL_A = [1,2,3,4,5,0];
const VAL_B = [5,4,3,2,1,0];
const VAL_C = [5,4,3,2,1,0];

const questions = reactive([
  { text: "Do you feel that you are wasting your time if you are not accomplishing something?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you feel that you never have a chance to catch your breath before you have to move on to the next project/crisis?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you take at least one consecutive full week of vacation each year?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you use all of your vacation days and personal days each year?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you frequently delegate work to others?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you work more than one hour per day while on vacation?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you look forward to starting your day each morning?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you feel boxed in; that is, you do what you do because others depend on you for support?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Is your work satisfying and rewarding?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you feel burnt out, exhausted and unable to give your all to any area of your life?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you feel that you are reasonably in control over your work day?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Are you impatient and short with your coworkers or your family?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you dedicate time to having lunch each day (versus multitask while you eat or skip lunch)?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Are you satisfied with where you are at this point in your journey of your business life?", scale: SCALE_C, values: VAL_C, answer: null },
  { text: "Do you reserve at least 30 minutes of “me time” each day?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you start your day tired?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you feel you are missing out on the things that mean the most to you?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do thoughts of work interrupt a good night’s rest?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you bring work home with you?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you reply to texts while in the company of others?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Are you satisfied with where you are at this point in your journey of your personal life?", scale: SCALE_C, values: VAL_C, answer: null },
  { text: "Do you have a hard time saying “no” to requests at work?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you miss special family events?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you feel you don’t have time for yourself or for your family and friends?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you prioritize your to-do list each day and especially focus on your highest priority items?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you feel that your personal needs are secondary?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Is clutter building in your office and at home?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you feel you spend too much time reacting and too little time thinking?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you feel stressed out most of the time?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you feel guilty because you can’t make time for things outside of work?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you hire people in your personal life to handle some of your chores such as yard work and other home tasks?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you squeeze every bit of productivity you can out of each day?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you lose sight of who you are and what you’re doing?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you experience actions at least monthly that allow you to de-stress and rebuild your energy?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you spend too much time doing things you don’t want to do or spending time with people you don’t want to?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you feel overwhelmed and over committed?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you find it hard to relax and just do nothing, even when you are away on holiday?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you frequently feel anxious or upset because of what is happening at work?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Are you satisfied with your work-life balance?", scale: SCALE_C, values: VAL_C, answer: null },
  { text: "Do you take time off from work and do fun activities?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you work more than you think is reasonable?", scale: SCALE_A, values: VAL_A, answer: null },
  { text: "Do you read and finish at least one book for pleasure every 6 months?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you find time to exercise, eat properly and keep yourself healthy?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you feel relaxed and comfortable when you are at home?", scale: SCALE_B, values: VAL_B, answer: null },
  { text: "Do you get adequate sleep most nights?", scale: SCALE_B, values: VAL_B, answer: null }
]);

/* PAGINATION */
const page = ref(1);
const perPage = 9;

const totalPages = Math.ceil(questions.length / perPage);

const currentStartIndex = computed(() => (page.value - 1) * perPage);

const paginatedQuestions = computed(() => {
	return questions.slice(currentStartIndex.value, currentStartIndex.value + perPage);
});

/* Progress Bar */
const progressPercent = computed(() => {
	return Math.round((page.value / totalPages) * 100);
});

/* Auto-scroll to top */
watch(page, () => {
	window.scrollTo({ top: 700, behavior: "smooth" });
});

function nextPage() {
	if (page.value < totalPages) page.value++;
}

function previousPage() {
	if (page.value > 1) page.value--;
}

const result = ref(null);

/* Scoring */
function submit() {
	let totalScore = 0;
	let answeredCount = 0;

	for (const q of questions) {
		if (q.answer !== null && q.answer !== 0) {
			totalScore += q.answer;
			answeredCount++;
		}
	}

	const score = totalScore / answeredCount;

	let interpretation = "";
	if (score >= 4.0) interpretation = "GOOD — your work-life balance is healthy.";
	else if (score >= 3.0) interpretation = "BORDERLINE — take action soon.";
	else interpretation = "POOR — immediate changes recommended.";

	result.value = { score, interpretation };
}

function closeComponent() {
	emit("close");
}
</script>
