<template>
	<div class="container py-4">
		<h4 class="text-center mb-4">Pittsburgh Sleep Quality Index (PSQI)</h4>

		<div class="card shadow-sm">
			<div class="card-header bg-accent">
				<h5 class="mb-0">Answer the questions about your sleep during the past month</h5>
			</div>

			<div class="card-body">

				<!-- Q1 - Bed time (text/time) -->
				<div class="mb-3">
					<p class="fw-semibold">1. During the past month, what time have you usually gone to bed at night?</p>
					<input type="time" class="form-control w-50" v-model="answers.q1" />
				</div>

				<!-- Q2 - Minutes to fall asleep (number) -->
				<div class="mb-3">
					<p class="fw-semibold">2. During the past month, how long (in minutes) has it usually taken you to fall asleep each night?</p>
					<input type="number" min="0" class="form-control w-50" v-model.number="answers.q2" />
				</div>

				<!-- Q3 - Getting up time -->
				<div class="mb-3">
					<p class="fw-semibold">3. During the past month, what time have you usually gotten up in the morning?</p>
					<input type="time" class="form-control w-50" v-model="answers.q3" />
				</div>

				<!-- Q4 - Hours of actual sleep -->
				<div class="mb-3">
					<p class="fw-semibold">4. During the past month, how many hours of actual sleep did you get at night?</p>
					<input type="number" step="0.1" min="0" class="form-control w-50" v-model.number="answers.q4" />
				</div>

				<hr />

				<!-- Q5 - Subquestions a - j (frequency radios) -->
				<div class="mb-3">
					<p class="fw-semibold">5. During the past month, how often have you had trouble sleeping because you...</p>

					<div class="ms-3 mb-2" v-for="(item, idx) in q5items" :key="idx">
						<p class="mb-1">{{ item.label }}</p>

						<div class="d-flex gap-3 flex-wrap">
							<label class="form-check">
								<input class="form-check-input" type="radio" :name="`q5-${idx}`" :value="0" v-model.number="answers.q5[idx]" />
								<span class="form-check-label">Not during the past month</span>
							</label>

							<label class="form-check">
								<input class="form-check-input" type="radio" :name="`q5-${idx}`" :value="1" v-model.number="answers.q5[idx]" />
								<span class="form-check-label">Less than once a week</span>
							</label>

							<label class="form-check">
								<input class="form-check-input" type="radio" :name="`q5-${idx}`" :value="2" v-model.number="answers.q5[idx]" />
								<span class="form-check-label">Once or twice a week</span>
							</label>

							<label class="form-check">
								<input class="form-check-input" type="radio" :name="`q5-${idx}`" :value="3" v-model.number="answers.q5[idx]" />
								<span class="form-check-label">Three or more times a week</span>
							</label>
						</div>

						<!-- If item has a text box (e.g., "Other reason(s)") -->
						<div v-if="item.type === 'text'">
							<textarea class="form-control mt-2 w-75" rows="2" v-model="answers.q5_extra"></textarea>
						</div>
					</div>
				</div>

				<hr />

				<!-- Q6 overall sleep quality -->
				<div class="mb-3">
					<p class="fw-semibold">6. During the past month, how would you rate your sleep quality overall?</p>

					<div class="d-flex flex-column ms-3">
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q6" :value="0" v-model.number="answers.q6" />
							Very good
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q6" :value="1" v-model.number="answers.q6" />
							Fairly good
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q6" :value="2" v-model.number="answers.q6" />
							Fairly bad
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q6" :value="3" v-model.number="answers.q6" />
							Very bad
						</label>
					</div>
				</div>

				<!-- Q7 - Medicine to help you sleep -->
				<div class="mb-3">
					<p class="fw-semibold">7. During the past month, how often have you taken medicine to help you sleep (prescribed or "over the counter")?</p>

					<div class="d-flex flex-column ms-3">
						<label class="form-check">
							<input class="form-check-input" type="radio" name="q7" :value="0" v-model.number="answers.q7" />
							Not during the past month
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="q7" :value="1" v-model.number="answers.q7" />
							Less than once a week
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="q7" :value="2" v-model.number="answers.q7" />
							Once or twice a week
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="q7" :value="3" v-model.number="answers.q7" />
							Three or more times a week
						</label>
					</div>
				</div>

				<!-- Q8 - Trouble staying awake -->
				<div class="mb-3">
					<p class="fw-semibold">8. During the past month, how often have you had trouble staying awake while driving, eating meals, or engaging in social activity?</p>

					<div class="d-flex flex-column ms-3">
						<label class="form-check">
							<input class="form-check-input" type="radio" name="q8" :value="0" v-model.number="answers.q8" />
							Not during the past month
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="q8" :value="1" v-model.number="answers.q8" />
							Less than once a week
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="q8" :value="2" v-model.number="answers.q8" />
							Once or twice a week
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="q8" :value="3" v-model.number="answers.q8" />
							Three or more times a week
						</label>
					</div>
				</div>

				<!-- Q9 - Enthusiasm problem -->
				<div class="mb-3">
					<p class="fw-semibold">9. During the past month, how much of a problem has it been for you to keep up enough enthusiasm to get things done?</p>

					<div class="d-flex flex-column ms-3">
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q9" :value="0" v-model.number="answers.q9" />
							No problem at all
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q9" :value="1" v-model.number="answers.q9" />
							Only a very slight problem
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q9" :value="2" v-model.number="answers.q9" />
							Somewhat of a problem
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q9" :value="3" v-model.number="answers.q9" />
							A very big problem
						</label>
					</div>
				</div>

				<!-- Q10 - Bed partner / room mate -->
				<div class="mb-3">
					<p class="fw-semibold">10. Do you have a bed partner or room mate?</p>

					<div class="d-flex flex-column ms-3">
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q10" :value="'no_partner'" v-model="answers.q10" />
							No bed partner or room mate
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q10" :value="'other_room'" v-model="answers.q10" />
							Partner/room mate in other room
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q10" :value="'same_room_no_bed'" v-model="answers.q10" />
							Partner in same room, but not same bed
						</label>
						<label class="form-check mb-1">
							<input type="radio" class="form-check-input me-2" name="q10" :value="'same_bed'" v-model="answers.q10" />
							Partner in same bed
						</label>
					</div>
				</div>

				<!-- Conditional partner questions -->
				<div v-if="answers.q10 !== 'no_partner'">
					<hr />
					<p class="fw-semibold">If you have a room mate or bed partner, ask him/her how often in the past month you have had . . .</p>

					<div class="ms-3 mb-2" v-for="(item, idx) in partnerItems" :key="idx">
						<p class="mb-1">{{ item }}</p>

						<div class="d-flex gap-3 flex-wrap">
							<label class="form-check">
								<input class="form-check-input" type="radio" :name="`partner-${idx}`" :value="0" v-model.number="answers.partner[idx]" />
								<span class="form-check-label">Not during the past month</span>
							</label>

							<label class="form-check">
								<input class="form-check-input" type="radio" :name="`partner-${idx}`" :value="1" v-model.number="answers.partner[idx]" />
								<span class="form-check-label">Less than once a week</span>
							</label>

							<label class="form-check">
								<input class="form-check-input" type="radio" :name="`partner-${idx}`" :value="2" v-model.number="answers.partner[idx]" />
								<span class="form-check-label">Once or twice a week</span>
							</label>

							<label class="form-check">
								<input class="form-check-input" type="radio" :name="`partner-${idx}`" :value="3" v-model.number="answers.partner[idx]" />
								<span class="form-check-label">Three or more times a week</span>
							</label>
						</div>
					</div>
				</div>

				<!-- Submit -->
				<div class="text-center mt-4" v-if="!submitted">
					<button class="btn btn-success" @click="handleSubmit">Submit Assessment</button>
				</div>

				<!-- After submit: show structured payload -->
				<div v-if="submitted" class="mt-4">
					<div class="alert alert-success">
						<strong>Thanks for submitting.</strong> Below is the collected data (ready for backend).
					</div>

					<pre class="small bg-light p-3 rounded" style="white-space:pre-wrap; word-break:break-word;">
{{ payloadPretty }}
					</pre>

					<div class="text-center mt-3">
						<button class="btn btn-secondary" @click="closeComponent">Close</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</template>

<script setup>
import { reactive, ref, computed } from "vue";

const emit = defineEmits(["close"]);

/*
 Data shape:
 answers = {
	 q1: "HH:MM",
	 q2: number,
	 q3: "HH:MM",
	 q4: number,
	 q5: [0..3 (a), 0..3 (b), ...], // length = q5items.length
	 q5_extra: string, // j other reason text (if provided)
	 q6: 0..3,
	 q7: 0..3,
	 q8: 0..3,
	 q9: 0..3,
	 q10: 'no_partner' | 'other_room' | 'same_room_no_bed' | 'same_bed',
	 partner: [0..3 ...] // length = partnerItems.length
 }
*/

// q5 items a-j (j is "Other reason(s)" with extra text input)
const q5items = [
	{ label: "a) Cannot get to sleep within 30 minutes" },
	{ label: "b) Wake up in the middle of the night or early morning" },
	{ label: "c) Have to get up to use the bathroom" },
	{ label: "d) Cannot breathe comfortably" },
	{ label: "e) Cough or snore loudly" },
	{ label: "f) Feel too cold" },
	{ label: "g) Feel too hot" },
	{ label: "h) Had bad dreams" },
	{ label: "i) Have pain" },
	{ label: "j) Other reason(s), please describe", type: "text" }
];

// partner questions (snoring, long pauses, legs twitching, episodes of disorientation, other restless)
const partnerItems = [
	"Loud snoring",
	"Long pauses between breaths while asleep",
	"Legs twitching or jerking while you sleep",
	"Episodes of disorientation or confusion during sleep",
	"Other restlessness while you sleep; please describe (use text below)"
];

const answers = reactive({
	q1: "",
	q2: null,
	q3: "",
	q4: null,
	q5: Array(q5items.length).fill(null),
	q5_extra: "",
	q6: null,
	q7: null,
	q8: null,
	q9: null,
	q10: null,
	partner: Array(partnerItems.length).fill(null),
	partner_extra: ""
});

const submitted = ref(false);
const payload = ref({});

// pretty JSON for display
const payloadPretty = computed(() => JSON.stringify(payload.value, null, 2));

// Validation helper
function validate() {
	// q1-q4 required
	if (!answers.q1) { alert("Please enter usual bed time (Q1)."); return false; }
	if (answers.q2 === null || answers.q2 === "") { alert("Please enter minutes to fall asleep (Q2)."); return false; }
	if (!answers.q3) { alert("Please enter getting up time (Q3)."); return false; }
	if (answers.q4 === null || answers.q4 === "") { alert("Please enter hours of actual sleep (Q4)."); return false; }

	// q5: at least each subquestion should have a selected radio (allow null? require)
	for (let i = 0; i < q5items.length; i++) {
		// allow j (index 9) extra text optional, but still require a frequency radio
		if (answers.q5[i] === null || answers.q5[i] === undefined) {
			alert(`Please answer Q5 ${String.fromCharCode(97 + i)}: "${q5items[i].label}".`);
			return false;
		}
	}

	// q6 - q9 required
	if (answers.q6 === null) { alert("Please answer Q6."); return false; }
	if (answers.q7 === null) { alert("Please answer Q7."); return false; }
	if (answers.q8 === null) { alert("Please answer Q8."); return false; }
	if (answers.q9 === null) { alert("Please answer Q9."); return false; }

	// q10 required
	if (!answers.q10) { alert("Please answer Q10 about bed partner/room mate."); return false; }

	// if partner exists, require partner items frequencies
	if (answers.q10 !== "no_partner") {
		for (let i = 0; i < partnerItems.length; i++) {
			if (answers.partner[i] === null || answers.partner[i] === undefined) {
				alert(`Please answer partner question: "${partnerItems[i]}".`);
				return false;
			}
		}
	}

	return true;
}

function handleSubmit() {
	if (!validate()) return;

	// Build payload (backend-friendly structure)
	payload.value = {
		q1_bed_time: answers.q1 || null,
		q2_minutes_to_sleep: answers.q2 === null ? null : Number(answers.q2),
		q3_getup_time: answers.q3 || null,
		q4_hours_sleep: answers.q4 === null ? null : Number(answers.q4),
		q5_trouble_reasons: q5items.map((it, idx) => ({
			key: String.fromCharCode(97 + idx),
			question: it.label,
			frequency_value: answers.q5[idx], // 0..3
			extra_text: idx === 9 ? (answers.q5_extra || "") : undefined
		})),
		q6_sleep_quality_value: answers.q6,
		q7_medicine_frequency_value: answers.q7,
		q8_trouble_awake_value: answers.q8,
		q9_enthusiasm_problem_value: answers.q9,
		q10_partner_type: answers.q10,
		partner_observations: partnerItems.map((it, idx) => ({
			question: it,
			frequency_value: answers.partner[idx],
			extra_text: idx === partnerItems.length - 1 ? (answers.partner_extra || "") : undefined
		})),
		submitted_at: new Date().toISOString()
	};

	// set submitted flag to show summary and allow parent close
	submitted.value = true;
}

function closeComponent() {
	emit("close");
}
</script>

<style scoped>
/* small spacing tweaks */
.form-check {
	min-width: 220px;
}
</style>
