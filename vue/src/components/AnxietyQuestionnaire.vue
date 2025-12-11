<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Anxiety Assessment</h4>

        <div class="card shadow-sm">
            <div class="card-header bg-accent">
                <h5 class="mb-0">Over the last two weeks, how often have you been bothered by the following problems?</h5>
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
                                :name="`gad7-q-${index}`"
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

// Emit event to parent
const emit = defineEmits(["close"]);

const ratingOptions = [
    "Not at all",
    "Several days",
    "More than half the days",
    "Nearly every day"
];

// Questions with answer = null
const questions = reactive([
    { text: "Feeling nervous, anxious, or on edge", answer: null },
    { text: "Not being able to stop or control worrying", answer: null },
    { text: "Worrying too much about different things", answer: null },
    { text: "Trouble relaxing", answer: null },
    { text: "Being so restless that it is hard to sit still", answer: null },
    { text: "Becoming easily annoyed or irritable", answer: null },
    { text: "Feeling afraid, as if something awful might happen", answer: null }
]);

const result = ref(null);

// Submit logic
function submit() {
    let total = 0;

    for (const q of questions) {
        if (q.answer !== null) {
            total += q.answer;
        }
    }

    // Interpretation
    let interpretation = "";
    if (total <= 4) interpretation = "Minimal Anxiety";
    else if (total <= 9) interpretation = "Mild Anxiety";
    else if (total <= 14) interpretation = "Moderate Anxiety";
    else interpretation = "Severe Anxiety";

    result.value = { total, interpretation };
}

// Close component
function closeComponent() {
    emit("close");
}
</script>
