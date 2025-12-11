<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Satisfaction With Life Scale (SWLS)</h4>

        <div class="card shadow-sm">
            <div class="card-header bg-accent">
                <h5 class="mb-0">Please indicate how much you agree with each statement</h5>
            </div>

            <div class="card-body">

                <!-- Questions -->
                <div
                    v-for="(q, index) in questions"
                    :key="index"
                    class="mb-4"
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
                                :name="`swls-q-${index}`"
                                :value="7 - rIndex"
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
                    <strong>Total Score:</strong> {{ result.total }} <br />
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

// Rating options (7 → 1)
const ratingOptions = [
    "Strongly agree",      // 7
    "Agree",               // 6
    "Slightly agree",      // 5
    "Neither agree nor disagree", // 4
    "Slightly disagree",   // 3
    "Disagree",            // 2
    "Strongly disagree"    // 1
];

const questions = reactive([
    { text: "In most ways my life is close to my ideal.", answer: null },
    { text: "The conditions of my life are excellent.", answer: null },
    { text: "I am satisfied with my life.", answer: null },
    { text: "So far I have gotten the important things I want in life.", answer: null },
    { text: "If I could live my life over, I would change almost nothing.", answer: null }
]);

const result = ref(null);

// Submit scoring
function submit() {
    let total = 0;

    for (const q of questions) {
        if (q.answer !== null) {
            total += q.answer;
        }
    }

    // Interpretation (standard SWLS scoring)
    let interpretation = "";
    if (total >= 31) interpretation = "Extremely Satisfied";
    else if (total >= 26) interpretation = "Satisfied";
    else if (total >= 21) interpretation = "Slightly Satisfied";
    else if (total >= 20) interpretation = "Neutral";
    else if (total >= 15) interpretation = "Slightly Dissatisfied";
    else if (total >= 10) interpretation = "Dissatisfied";
    else interpretation = "Extremely Dissatisfied";

    result.value = { total, interpretation };
}

// Close
function closeComponent() {
    emit("close");
}
</script>
