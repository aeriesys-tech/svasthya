<template>
    <div class="container py-5">
        <h1 class="text-center mb-4">Make the Call</h1>
        <p class="text-muted text-center mb-5">
            Leadership is built one decision at a time. Step into command, make your call, and reflect.
        </p>

        <!-- Step 1: Scenario -->
        <div v-if="step === 1">
            <div class="p-4 border rounded-3 bg-light mb-4">
                <h5 class="fw-bold text-uppercase text-accent mb-3">Scenario of the Week</h5>
                <p>
                    “You’re the senior officer at a protest site.
                    One radio call reports a group acting aggressively.
                    Another alerts you to a medical emergency 100 meters away.
                    You have only 2 available officers to respond. What do you do?”
                </p>
            </div>
            <div class="text-center">
                <button class="btn btn-primary btn-lg" @click="nextStep">Make the Call</button>
            </div>
        </div>

        <!-- Step 2: Choose Decision -->
        <div v-else-if="step === 2">
            <h4 class="mb-3">As the leader on scene, what’s your decision?</h4>

            <div class="d-grid gap-3">
                <div
                    v-for="(option, index) in decisionOptions"
                    :key="index"
                    class="card decision-card p-3"
                    :class="{ selected: selectedOption === option.key }"
                    @click="selectOption(option.key)"
                >
                    <strong>{{ option.label }}</strong>
                    <p class="mb-0 text-muted">{{ option.text }}</p>
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-secondary" :disabled="!selectedOption" @click="nextStep">
                    Next
                </button>
            </div>
        </div>

        <!-- Step 3: Rationale -->
        <div v-else-if="step === 3">
            <h4 class="mb-3">Why did you make this call?</h4>
            <p class="text-muted mb-2">In a few lines, explain your decision (100–200 characters).</p>
            <textarea
                v-model="rationale"
                class="form-control"
                rows="4"
                maxlength="200"
                placeholder="Type your reasoning here..."
            ></textarea>

            <div class="text-center mt-4">
                <button class="btn btn-secondary" :disabled="!rationale.trim()" @click="showFeedback">
                    Submit
                </button>
            </div>
        </div>

        <!-- Step 4: Feedback -->
        <div v-else-if="step === 4" class="text-center">
            <div class="alert alert-info text-start mx-auto" style="max-width: 600px">
                <h5 class="fw-bold text-accent mb-2">Feedback</h5>
                <p class="mb-0">{{ feedbackMessage }}</p>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary" @click="nextStep">Continue</button>
            </div>
        </div>

        <!-- Step 5: Reflection -->
        <div v-else-if="step === 5">
            <h4 class="mb-3 text-center">Reflection</h4>
            <p class="text-muted text-center mb-3">
                One thing you learned from this, or something you’d do differently next time:
            </p>
            <textarea
                v-model="reflection"
                class="form-control"
                rows="4"
                placeholder="Write your takeaway..."
            ></textarea>

            <div class="text-center mt-4">
                <button class="btn btn-success" :disabled="!reflection.trim()" @click="finishActivity">
                    Save to Leadership Logbook
                </button>
            </div>
        </div>

        <!-- Step 6: Done -->
        <div v-else-if="step === 6" class="text-center">
            <h3 class="text-success mb-3">Decision logged successfully!</h3>
            <p>Your reflection has been saved to your Leadership Logbook.</p>

            <div class="d-grid gap-3 mt-4" style="max-width: 300px; margin: auto;">
                <button class="btn btn-outline-primary">View My Decisions</button>
                <button class="btn btn-outline-secondary">Leadership Growth Summary</button>
                <button class="btn btn-outline-success" @click="restart">Try Another Scenario</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const step = ref(1)
const selectedOption = ref(null)
const rationale = ref('')
const reflection = ref('')

const decisionOptions = [
    {
        key: 'A',
        label: 'Option A',
        text: 'Send one officer to each situation',
    },
    {
        key: 'B',
        label: 'Option B',
        text: 'Deploy both officers to the aggressive group',
    },
    {
        key: 'C',
        label: 'Option C',
        text: 'Send both to the medical emergency and call backup for the protest',
    },
]

const feedbackMap = {
    A: 'This split strategy gives both issues attention but may spread resources thin. Shows balanced thinking under pressure.',
    B: 'Focusing both officers on the threat ensures safety, but risks delaying medical aid. Reflects tactical control under stress.',
    C: 'Prioritizing the medical emergency shows ethical focus, but requires strong backup coordination. Reflects values-based decision-making.',
}

const feedbackMessage = computed(() => feedbackMap[selectedOption.value] || '')

const nextStep = () => {
    step.value++
}

const selectOption = (key) => {
    selectedOption.value = key
}

const showFeedback = () => {
    step.value = 4
}

const finishActivity = () => {
    step.value = 6
    // TODO: save rationale + reflection + choice to backend/logbook later
}

const restart = () => {
    step.value = 1
    selectedOption.value = null
    rationale.value = ''
    reflection.value = ''
}
</script>

<style scoped>
.text-accent {
    color: #d80621;
}

.decision-card {
    border: 2px solid transparent;
    cursor: pointer;
    transition: 0.3s;
}
.decision-card:hover {
    border-color: #ccc;
}
.decision-card.selected {
    border-color: #d80621;
    background-color: #fff5f6;
}
</style>
