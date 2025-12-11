<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Well-Being Index</h4>

        <div class="card shadow-sm">
            <div class="card-header bg-accent">
                <h5 class="mb-0">Please indicate how you felt in the last 2 weeks</h5>
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
                                :name="`who5-q-${index}`"
                                :value="5 - rIndex"
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
                    <strong>Raw Score:</strong> {{ result.raw }} / 25 <br />
                    <strong>Percentage Score:</strong> {{ result.percent }}% <br />
                    <strong>Interpretation:</strong> {{ result.interpretation }}
                </div>

                <!-- Close -->
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

// Clean rating labels (NO numbers shown)
const ratingOptions = [
    "All of the time",          // 5
    "Most of the time",         // 4
    "More than half the time",  // 3
    "Less than half the time",  // 2
    "Some of the time",         // 1
    "At no time"                // 0
];

const questions = reactive([
    { text: "I have felt cheerful and in good spirits.", answer: null },
    { text: "I have felt calm and relaxed.", answer: null },
    { text: "I have felt active and vigorous.", answer: null },
    { text: "I woke up feeling fresh and rested.", answer: null },
    { text: "My daily life has been filled with things that interest me.", answer: null }
]);

const result = ref(null);

function submit() {
    let raw = 0;

    for (const q of questions) {
        if (q.answer !== null) {
            raw += q.answer;
        }
    }

    const percent = raw * 4;

    let interpretation = "";
    if (percent >= 70) interpretation = "High well-being";
    else if (percent >= 50) interpretation = "Moderate well-being";
    else if (percent >= 28) interpretation = "Low well-being — possible risk of poor well-being";
    else interpretation = "Very low well-being — clinical evaluation recommended";

    result.value = { raw, percent, interpretation };
}

function closeComponent() {
    emit("close");
}
</script>
