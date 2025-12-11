<template>
    <div class="container py-5">
        <div v-if="step === 1" class="text-center">
            <h2 class="mb-3 text-primary">Language Matters</h2>
            <p class="lead">
                Words carry weight. In high-pressure moments, they can either calm things down or make them worse. 
                Let’s explore how small shifts in language can make a big difference.
            </p>
            <button class="btn btn-accent mt-4" @click="nextStep">Begin</button>
        </div>

        <!-- Step 2: Scenario Selection -->
        <div v-else-if="step === 2">
            <h4 class="text-center mb-3">Select a Scenario</h4>
            <div class="list-group">
                <button
                    v-for="(scenario, index) in scenarios"
                    :key="index"
                    class="list-group-item list-group-item-action"
                    @click="selectScenario(index)"
                >
                    {{ scenario.title }}
                </button>
            </div>
        </div>

        <!-- Step 3: Scenario Presentation -->
        <div v-else-if="step === 3">
            <h4 class="text-center mb-3">{{ currentScenario.title }}</h4>
            <div class="card shadow-sm p-3 mb-3">
                <p class="fw-bold">Scenario:</p>
                <p>{{ currentScenario.context }}</p>
                <blockquote class="blockquote text-center text-muted border-start ps-3">
                    “{{ currentScenario.quote }}”
                </blockquote>
            </div>

            <div class="list-group">
                <button
                    v-for="(option, i) in currentScenario.responses"
                    :key="i"
                    class="list-group-item list-group-item-action"
                    @click="handleResponse(option)"
                >
                    {{ option.text }}
                </button>
            </div>
        </div>

        <!-- Step 4: Feedback -->
        <div v-else-if="step === 4" class="text-center">
            <h4 class="mb-3" :class="feedback.isPositive ? 'text-success' : 'text-danger'">
                {{ feedback.title }}
            </h4>
            <p class="mb-4">{{ feedback.message }}</p>

            <button class="btn btn-outline-primary me-2" @click="retryScenario">
                Try Again
            </button>
            <button class="btn btn-accent" @click="nextScenario">
                Next Scenario
            </button>
        </div>

        <!-- Step 5: Completion -->
        <div v-else-if="step === 'done'" class="text-center">
            <h3 class="mb-3 text-success">Well done!</h3>
            <p>You’ve explored how words shape outcomes — calm, empathy, and clarity go a long way.</p>
            <button class="btn btn-outline-primary mt-3" @click="restart">
                Replay Activity
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const step = ref(1)
const currentIndex = ref(null)

const nextStep = () => step.value++
const selectScenario = (index) => {
    currentIndex.value = index
    step.value = 3
}

const scenarios = [
    {
        title: 'Domestic Disturbance',
        context: 'You arrive at an apartment after a noise complaint. A woman answers the door, visibly shaken. Inside, her partner is pacing and agitated.',
        quote: 'He’s not dangerous, okay? Just leave us alone. Every time you people show up, it makes things worse!',
        responses: [
            { text: 'If there’s yelling, it’s our business.', correct: false },
            { text: 'We have to follow the procedure whether you like it or not.', correct: false },
            { text: 'You’re overreacting — this is routine.', correct: false },
            { text: 'I understand this is stressful. We’re here to help, not cause trouble.', correct: true },
        ],
    },
    {
        title: 'Traffic Stop',
        context: 'You’ve pulled over a driver for speeding. The person immediately starts recording you with their phone, voice raised.',
        quote: 'I know my rights! Don’t even think about harassing me like you do everyone else.',
        responses: [
            { text: 'Put the phone down or I’ll write you up for something else.', correct: false },
            { text: 'You’re being paranoid. No one’s harassing you.', correct: false },
            { text: 'You’re escalating this, not me.', correct: false },
            { text: 'It’s your right to record. I just want to explain why I stopped you.', correct: true },
        ],
    },
    {
        title: 'Mental Health Call',
        context: 'You’re called to a park where a young man is talking to himself loudly. He becomes defensive as you approach.',
        quote: 'I don’t need help! Stop treating me like I’m crazy. I’m not going anywhere with you!',
        responses: [
            { text: 'You’re clearly not thinking straight right now.', correct: false },
            { text: 'If you don’t calm down, we’ll have to force you.', correct: false },
            { text: 'Keep yelling and I’ll call for backup.', correct: false },
            { text: 'You don’t have to go anywhere — I just want to understand what’s going on.', correct: true },
        ],
    },
]

const currentScenario = computed(() => scenarios[currentIndex.value])

const feedback = ref({ title: '', message: '', isPositive: false })

const handleResponse = (option) => {
    if (option.correct) {
        feedback.value = {
            title: 'Encourager!',
            message:
                'That response shows empathy and keeps communication open. Calm, clear, and respectful — excellent choice!',
            isPositive: true,
        }
    } else {
        feedback.value = {
            title: 'Discourager',
            message:
                'That response may sound dismissive or controlling. Try showing understanding first before asserting authority.',
            isPositive: false,
        }
    }
    step.value = 4
}

const retryScenario = () => (step.value = 3)
const nextScenario = () => {
    if (currentIndex.value < scenarios.length - 1) {
        currentIndex.value++
        step.value = 3
    } else {
        step.value = 'done'
    }
}
const restart = () => {
    step.value = 1
    currentIndex.value = null
}
</script>

<style scoped>
.list-group-item {
    cursor: pointer;
    transition: background-color 0.2s;
}
.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>
