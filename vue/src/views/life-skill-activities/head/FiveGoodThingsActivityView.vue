<template>
    <div class="container py-5">
        <!-- Intro / Welcome Screen -->
        <div v-if="step === 'intro'" class="text-center">
            <h1 class="mb-3">Five Good Things</h1>
            <h5 class="text-muted mb-4">
                Build your daily resilience by recognising the wins — big or small.
            </h5>
            <p class="lead">
                Resilience is built one step at a time. Today, take a moment to reflect on yesterday. 
                What went well? What did you achieve — no matter how small?
            </p>
            <p class="fw-semibold text-accent mt-3">
                “Small wins help build strong resilience. Celebrate yours today!”
            </p>
            <button class="btn btn-accent mt-4" @click="startActivity">
                Start My 5 Wins
            </button>
        </div>

        <!-- Entry Screen -->
        <div v-else-if="step === 'entry'">
            <h3 class="mb-4 text-center">Your Five Wins</h3>
            <p class="text-center mb-3">
                Think of something that went well yesterday. What did you do or experience that felt like a win?
            </p>

            <div v-for="(win, index) in wins" :key="index" class="mb-3">
                <label :for="'win-' + index" class="form-label fw-semibold">Win #{{ index + 1 }}</label>
                <div class="input-group">
                    <input
                        type="text"
                        class="form-control"
                        v-model="wins[index]"
                        :id="'win-' + index"
                        maxlength="100"
                        placeholder="Enter your win"
                    />
                    <button class="btn btn-outline-secondary" title="Voice input (not yet implemented)">
                        <i class="bi bi-mic"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <small class="text-muted">Progress: {{ completedWins }} of 5 entered</small>
                <button class="btn btn-outline-primary btn-sm" @click="showExamples = !showExamples">
                    {{ showExamples ? 'Hide Examples' : 'Show Me Examples' }}
                </button>
            </div>

            <transition name="fade">
                <ul v-if="showExamples" class="mt-3 text-muted small">
                    <li>I handled a stressful call professionally.</li>
                    <li>I drank enough water.</li>
                    <li>I checked in with a colleague.</li>
                    <li>I went to bed on time.</li>
                </ul>
            </transition>

            <div class="text-center mt-5">
                <button class="btn btn-secondary me-2" @click="saveDraft">Save as Draft</button>
                <button
                    class="btn btn-accent"
                    :disabled="completedWins < 5"
                    @click="step = 'mood'"
                >
                    Next: Mood Check
                </button>
            </div>
        </div>

        <!-- Mood Check -->
        <div v-else-if="step === 'mood'" class="text-center">
            <h3 class="mb-3">How do you feel after reflecting on these wins?</h3>

            <div class="mood-slider my-4">
                <input
                    type="range"
                    min="1"
                    max="5"
                    step="1"
                    v-model="mood"
                    class="form-range"
                />
                <div class="d-flex justify-content-between mt-2 emoji-row">
                    <span v-for="n in 5" :key="n" :class="['emoji', { active: mood == n }]">
                        {{ emojis[n - 1] }}
                    </span>
                </div>
            </div>

            <textarea
                class="form-control mb-3"
                rows="3"
                placeholder="Want to describe why you feel this way?"
                v-model="moodComment"
            ></textarea>

            <button class="btn btn-accent" @click="completeActivity">Submit</button>
        </div>

        <!-- Confirmation Screen -->
        <div v-else-if="step === 'done'" class="text-center">
            <h3 class="mb-3 text-success">Great job!</h3>
            <p>Your five wins have been saved. Small steps lead to strong foundations.</p>

            <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                <button class="btn btn-outline-primary">View Past Wins</button>
                <button class="btn btn-outline-secondary">Set Daily Reminder</button>
                <button class="btn btn-outline-success">Edit Entry</button>
            </div>

            <hr class="my-4" />
            <p>Would you like to anonymously share one of your wins to inspire others?</p>
            <div class="d-flex justify-content-center gap-3">
                <button class="btn btn-success">Yes</button>
                <button class="btn btn-secondary">No</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const step = ref('intro')
const wins = ref(['', '', '', '', ''])
const showExamples = ref(false)
const mood = ref(3)
const moodComment = ref('')
const emojis = ['😄', '😊', '😐', '😕', '😠']
const completedWins = computed(() => wins.value.filter(w => w.trim()).length)

const startActivity = () => (step.value = 'entry')

const saveDraft = () => {
    localStorage.setItem('fiveGoodThingsDraft', JSON.stringify(wins.value))
    alert('Draft saved successfully!')
}

// Auto-save every 60 seconds
setInterval(() => {
    localStorage.setItem('fiveGoodThingsDraft', JSON.stringify(wins.value))
}, 60000)

const completeActivity = () => {
    localStorage.removeItem('fiveGoodThingsDraft')
    step.value = 'done'
}

// Restore draft if exists
const savedDraft = localStorage.getItem('fiveGoodThingsDraft')
if (savedDraft) wins.value = JSON.parse(savedDraft)
</script>

<style scoped>
.btn {
    padding: 0.9rem 1.2rem;
    font-size: 1rem;
    border-radius: 0.5rem;
}
.btn-accent {
    background-color: #d80621;
    color: #fff;
}
.btn-accent:hover {
    background-color: #b4051b;
}
.mood-slider input[type="range"] {
    width: 100%;
}
.emoji-row {
    font-size: 1.8rem;
}
.emoji {
    opacity: 0.5;
    transition: 0.2s;
}
.emoji.active {
    opacity: 1;
    transform: scale(1.1);
}
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>
