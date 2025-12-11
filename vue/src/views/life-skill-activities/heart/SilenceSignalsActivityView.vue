<template>
    <div class="container py-5">
        <div v-if="step === 1" class="text-center">
            <h2 class="mb-3 text-primary">Silence Signals</h2>
            <p class="lead">
                Your eyes, tone, and stance say more than you think. Let’s train your instincts to read and respond — without relying only on words.
            </p>
            <button class="btn btn-accent mt-4" @click="nextStep">Start Practice</button>
        </div>

        <!-- Step 2: Emotion Recognition -->
        <div v-else-if="step === 2">
            <h4 class="text-center mb-3">Emotion Match Challenge</h4>
            <div class="card shadow-sm p-3 text-center mb-3">
                <p>“Tight jaw + fixed eye contact”</p>
                <p class="text-muted small">(What emotion is being shown?)</p>
            </div>
            <div class="list-group">
                <button
                    v-for="(option, i) in emotions"
                    :key="i"
                    class="list-group-item list-group-item-action"
                    @click="handleEmotion(option)"
                >
                    {{ option.text }}
                </button>
            </div>
        </div>

        <!-- Step 3: Tone Practice -->
        <div v-else-if="step === 3" class="text-center">
            <h4 class="mb-3">Tone Practice: “Stop right there.”</h4>
            <p class="text-muted">Say it three ways — Calm, Assertive, Aggressive — and reflect on how each feels.</p>
            <div class="d-grid gap-2 col-md-6 mx-auto mt-4">
                <button class="btn btn-outline-secondary">🎙️ Record Calm</button>
                <button class="btn btn-outline-secondary">🎙️ Record Assertive</button>
                <button class="btn btn-outline-secondary">🎙️ Record Aggressive</button>
            </div>
            <button class="btn btn-accent mt-4" @click="nextStep">Next</button>
        </div>

        <!-- Step 4: Reflection -->
        <div v-else-if="step === 4" class="text-center">
            <h4 class="mb-3 text-success">Good Work!</h4>
            <p>
                Nonverbal control builds trust. You’ve practiced reading cues and managing tone — skills that calm situations before words even start.
            </p>
            <button class="btn btn-outline-primary mt-3" @click="restart">
                Replay Activity
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const step = ref(1)
const emotions = [
    { text: 'Fear', correct: false },
    { text: 'Anger', correct: true },
    { text: 'Disinterest', correct: false },
    { text: 'Confusion', correct: false },
]

const nextStep = () => step.value++
const handleEmotion = (option) => {
    if (option.correct) {
        alert('Correct! Tight jaw + fixed eyes = suppressed anger.')
        step.value = 3
    } else {
        alert('Try again! Look for body tension and direct eye contact.')
    }
}
const restart = () => (step.value = 1)
</script>

<style scoped>
.list-group-item {
    cursor: pointer;
}
</style>
