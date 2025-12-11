<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Brief Resilience Scale (BRS)</h4>

        <div class="card shadow-sm">
            <div class="card-header bg-accent">
                <h5 class="mb-0">Please respond to each item</h5>
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
                                :name="`brs-q-${index}`"
                                :value="rIndex + 1"
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
                    <strong>Average Score:</strong> {{ result.average }} <br />
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
import { reactive, ref } from 'vue'

// Emit event to parent
const emit = defineEmits(['close'])

const ratingOptions = [
    "Strongly Disagree",
    "Disagree",
    "Neutral",
    "Agree",
    "Strongly Agree"
]

// reverse = true → reverse scoring (5→1)
const questions = reactive([
    { text: "I tend to bounce back quickly after hard times.", answer: null, reverse: false },
    { text: "I have a hard time making it through stressful events.", answer: null, reverse: true },
    { text: "It does not take me long to recover from a stressful event.", answer: null, reverse: false },
    { text: "It is hard for me to snap back when something bad happens.", answer: null, reverse: true },
    { text: "I usually come through difficult times with little trouble.", answer: null, reverse: false },
    { text: "I tend to take a long time to get over setbacks in my life.", answer: null, reverse: true }
])

const result = ref(null)

// Submit scoring
function submit() {
    let total = 0
    let count = 0

    for (const q of questions) {
        if (q.answer !== null) {
            count++

            total += q.reverse ? (6 - q.answer) : q.answer
        }
    }

    const average = (total / count).toFixed(2)

    let interpretation = ""
    if (average >= 4.31) interpretation = "High Resilience"
    else if (average >= 3.00) interpretation = "Normal Resilience"
    else interpretation = "Low Resilience"

    result.value = { total, average, interpretation }
}

// Close component
function closeComponent() {
    emit("close")
}
</script>
