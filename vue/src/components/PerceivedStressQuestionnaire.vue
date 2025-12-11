<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Perceived Stress Scale (PSS)</h4>

        <div class="card shadow-sm">
            <div class="card-header bg-accent">
                <h5 class="mb-0">
                    Please answer how often you felt or thought this way in the last month
                </h5>
            </div>

            <div class="card-body">

                <!-- Questions -->
                <div
                    v-for="(q, index) in questions"
                    :key="index"
                    class="mb-3"
                >
                    <p class="fw-semibold">{{ `${index + 1}. ${q.text}` }}</p>

                    <div class="d-flex flex-column ms-3">
                        <label
                            v-for="(label, rIndex) in ratingOptions"
                            :key="rIndex"
                            class="form-check mb-1 text-start"
                        >
                            <input
                                type="radio"
                                class="form-check-input me-2"
                                :name="`pss-q-${index}`"
                                :value="rIndex"
                                v-model.number="q.answer"
                            />
                            {{ label }}
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="text-center mt-3" v-if="!result">
                    <button class="btn btn-success" @click="submit">
                        Submit Assessment
                    </button>
                </div>

                <!-- Results -->
                <div v-if="result" class="alert alert-info mt-3">
                    <strong>Total Score:</strong> {{ result.total }} <br>
                    <strong>Interpretation:</strong> {{ result.interpretation }}
                </div>

                <!-- Close Button -->
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
import { reactive, ref } from "vue";

const emit = defineEmits(["close"]);

// Rating labels (0 → 4)
const ratingOptions = [
    "Never",
    "Almost never",
    "Sometimes",
    "Fairly often",
    "Very often"
];

// Questions EXACTLY as given
const questions = reactive([
    { text: "In the last month, how often have you been upset because of something that happened unexpectedly?", answer: null },
    { text: "In the last month, how often have you felt that you were unable to control the important things in your life?", answer: null },
    { text: "In the last month, how often have you felt nervous and stressed?", answer: null },
    { text: "In the last month, how often have you felt confident about your ability to handle your personal problems?", answer: null, reverse: true },
    { text: "In the last month, how often have you felt that things were going your way?", answer: null, reverse: true },
    { text: "In the last month, how often have you found that you could not cope with all the things that you had to do?", answer: null },
    { text: "In the last month, how often have you been able to control irritations in your life?", answer: null, reverse: true },
    { text: "In the last month, how often have you felt that you were on top of things?", answer: null, reverse: true },
    { text: "In the last month, how often have you been angered because of things that happened that were outside of your control?", answer: null },
    { text: "In the last month, how often have you felt difficulties were piling up so high that you could not overcome them?", answer: null }
]);

const result = ref(null);

// Submit scoring
function submit() {
    let total = 0;

    for (const q of questions) {
        if (q.answer !== null) {
            // Reverse scoring for Q4, Q5, Q7, Q8
            if (q.reverse) {
                total += (4 - q.answer);
            } else {
                total += q.answer;
            }
        }
    }

    // Interpretation ranges
    let interpretation = "";
    if (total >= 0 && total <= 13) interpretation = "Low Stress";
    else if (total >= 14 && total <= 26) interpretation = "Moderate Stress";
    else interpretation = "High Perceived Stress";

    result.value = { total, interpretation };
}

// Close
function closeComponent() {
    emit("close");
}
</script>
