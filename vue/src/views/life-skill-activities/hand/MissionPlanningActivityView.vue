<template>
    <div class="container py-5">
        <h1 class="text-center mb-4">Mission Planning</h1>
        <p class="text-muted text-center mb-4">
            Every mission needs a plan. Think ahead, clarify roles, and lead your team with purpose.
        </p>

        <!-- Step 1: Scenario -->
        <div v-if="step === 1">
            <div class="border rounded-3 p-4 bg-light mb-4">
                <h5 class="fw-bold text-accent mb-2">Scenario</h5>
                <p>
                    “Your unit is handling crowd control at a large public event. You’re the team leader.
                    One officer is new, one is on communications, and one is on perimeter watch.
                    Suddenly, the crowd starts pushing against barriers near your zone.”
                </p>
            </div>
            <div class="text-center">
                <button class="btn btn-primary btn-lg" @click="nextStep">Respond</button>
            </div>
        </div>

        <!-- Step 2: Reflection Prompts -->
        <div v-else-if="step === 2">
            <div class="mb-4">
                <h5 class="fw-bold mb-2">A. How would you delegate tasks?</h5>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <button
                        v-for="(s, i) in suggestionsA"
                        :key="i"
                        class="btn btn-outline-secondary btn-sm"
                        @click="answers.a = s"
                    >
                        {{ s }}
                    </button>
                </div>
                <textarea
                    v-model="answers.a"
                    class="form-control"
                    rows="3"
                    placeholder="Type your own response..."
                ></textarea>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold mb-2">B. How would you keep your team informed and supported?</h5>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <button
                        v-for="(s, i) in suggestionsB"
                        :key="i"
                        class="btn btn-outline-secondary btn-sm"
                        @click="answers.b = s"
                    >
                        {{ s }}
                    </button>
                </div>
                <textarea
                    v-model="answers.b"
                    class="form-control"
                    rows="3"
                    placeholder="Type your own response..."
                ></textarea>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold mb-2">C. What could go wrong if roles aren’t clearly communicated?</h5>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <button
                        v-for="(s, i) in suggestionsC"
                        :key="i"
                        class="btn btn-outline-secondary btn-sm"
                        @click="answers.c = s"
                    >
                        {{ s }}
                    </button>
                </div>
                <textarea
                    v-model="answers.c"
                    class="form-control"
                    rows="3"
                    placeholder="Type your own response..."
                ></textarea>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-success" @click="nextStep">Submit</button>
            </div>
        </div>

        <!-- Step 3: Feedback -->
        <div v-else-if="step === 3" class="text-center">
            <div class="alert alert-info text-start mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-accent mb-2">Feedback</h5>
                <p>
                    Great teams know their roles and trust each other.
                    Clear, timely communication avoids confusion and keeps the mission on track.
                </p>
                <p class="mb-0">
                    When everyone knows what to do — and when to ask for help — teams respond smarter and safer.
                </p>
            </div>

            <div class="mt-4 d-grid gap-2" style="max-width: 300px; margin:auto;">
                <button class="btn btn-outline-primary">Save to My Leadership Journal</button>
                <button class="btn btn-outline-secondary">Share Anonymous Insight</button>
                <button class="btn btn-outline-success" @click="restart">Try Another Mission</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const step = ref(1)
const answers = ref({ a: '', b: '', c: '' })

const suggestionsA = [
    'Send the most experienced officer to direct the crowd verbally.',
    'Keep the new officer close to observe and assist.',
    'Reassign perimeter to comms until backup arrives.',
]

const suggestionsB = [
    'Use brief, clear radio check-ins every 30 seconds.',
    'Make sure everyone knows their fallback point.',
    'Visually signal if radios fail.',
]

const suggestionsC = [
    'Officers might duplicate tasks or miss key risks.',
    'Backup may not reach the right location.',
    'The new officer might freeze or overreact.',
]

const nextStep = () => {
    step.value++
}

const restart = () => {
    step.value = 1
    answers.value = { a: '', b: '', c: '' }
}
</script>

<style scoped>
.text-accent {
    color: #d80621;
}
textarea {
    resize: none;
}
</style>
