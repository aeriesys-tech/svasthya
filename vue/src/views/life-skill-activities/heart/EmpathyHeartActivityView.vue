<template>
    <div class="container py-5">
        <!-- Step 1: Intro -->
        <div v-if="step === 'intro'" class="text-center">
            <h1 class="mb-3">Empathy Challenge</h1>
            <h5 class="text-muted mb-4">
                Small acts of understanding can build stronger teams and communities.
            </h5>
            <p class="lead">
                Each day, take one simple empathy challenge — listen, connect, and learn from
                someone different from you. Reflection helps deepen awareness and compassion.
            </p>
            <button class="btn btn-accent mt-4" @click="step = 'prompt'">
                View Today’s Prompt
            </button>
        </div>

        <!-- Step 2: View Prompt -->
        <div v-else-if="step === 'prompt'" class="text-center">
            <h3 class="mb-3">Empathy Prompt of the Day</h3>
            <div class="card mx-auto shadow-sm p-4" style="max-width: 500px;">
                <p class="fs-5 mb-4">{{ currentPrompt.text }}</p>
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-success" @click="markCompleted">Mark as Completed</button>
                    <button class="btn btn-outline-secondary" @click="skipPrompt">Skip</button>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-outline-primary" @click="step = 'intro'">
                    Back
                </button>
            </div>
        </div>

        <!-- Step 3: Reflection -->
        <div v-else-if="step === 'reflect'">
            <h3 class="mb-3 text-center">Reflection</h3>
            <p class="text-center text-muted">
                Take a moment to think about your experience. What did you notice or feel?
            </p>

            <div class="mb-3">
                <label class="form-label fw-semibold">Your Reflection</label>
                <textarea
                    v-model="reflection"
                    class="form-control"
                    rows="4"
                    maxlength="500"
                    placeholder="Write about your experience..."
                ></textarea>
                <small class="text-muted">{{ reflection.length }}/500 characters</small>
            </div>

            <div class="mb-4 text-center">
                <h6 class="fw-semibold mb-2">How did you feel during this interaction?</h6>
                <div class="d-flex justify-content-center gap-2">
                    <span
                        v-for="(emoji, index) in emojis"
                        :key="index"
                        class="emoji"
                        :class="{ active: mood === index + 1 }"
                        @click="mood = index + 1"
                    >
                        {{ emoji }}
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Tag this experience</label>
                <select v-model="tag" class="form-select">
                    <option disabled value="">Select category</option>
                    <option>Personal</option>
                    <option>Colleague</option>
                    <option>Community</option>
                    <option>Stranger</option>
                </select>
            </div>

            <div class="form-check mb-4">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="privateEntry"
                    v-model="privateEntry"
                />
                <label class="form-check-label" for="privateEntry">
                    Keep this reflection private
                </label>
            </div>

            <div class="text-center">
                <button
                    class="btn btn-accent"
                    :disabled="reflection.trim().length < 15"
                    @click="saveReflection"
                >
                    Save Reflection
                </button>
            </div>
        </div>

        <!-- Step 4: Confirmation -->
        <div v-else-if="step === 'done'" class="text-center">
            <h3 class="mb-3 text-success">Reflection Saved</h3>
            <p>Every act of understanding counts. You’ve added one more ripple of empathy.</p>

            <div class="badge-display mt-4">
                <h5 class="mb-3">Your Streak: {{ streak }} Days</h5>
                <span
                    v-if="streak >= 14"
                    class="badge bg-warning text-dark p-2"
                >🏆 Empathy Champion</span>
                <span
                    v-else-if="streak >= 7"
                    class="badge bg-secondary p-2"
                >🥈 Silver Badge</span>
                <span
                    v-else-if="streak >= 3"
                    class="badge bg-bronze text-dark p-2"
                >🥉 Bronze Badge</span>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                <button class="btn btn-outline-primary">View My Empathy Log</button>
                <button class="btn btn-outline-secondary" @click="step = 'prompt'">
                    Next Prompt
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const step = ref('intro')
const streak = ref(parseInt(localStorage.getItem('empathyStreak') || '0'))
const reflection = ref('')
const mood = ref(null)
const tag = ref('')
const privateEntry = ref(false)

const emojis = ['😊', '🤔', '😐', '🙄', '😞']

const prompts = [
    { id: 1, text: 'Ask a colleague what their biggest challenge was this week.' },
    { id: 2, text: 'Start a respectful conversation with someone who disagrees with you.' },
    { id: 3, text: 'Notice and appreciate something about someone from a different background.' },
]
const currentPrompt = ref(prompts[Math.floor(Math.random() * prompts.length)])

const markCompleted = () => {
    step.value = 'reflect'
}
const skipPrompt = () => {
    currentPrompt.value = prompts[Math.floor(Math.random() * prompts.length)]
}
const saveReflection = () => {
    const logs = JSON.parse(localStorage.getItem('empathyLogs') || '[]')
    logs.push({
        date: new Date().toLocaleDateString(),
        prompt: currentPrompt.value.text,
        reflection: reflection.value,
        mood: mood.value,
        tag: tag.value,
        private: privateEntry.value
    })
    localStorage.setItem('empathyLogs', JSON.stringify(logs))

    // update streak
    streak.value++
    localStorage.setItem('empathyStreak', streak.value.toString())

    reflection.value = ''
    mood.value = null
    tag.value = ''
    privateEntry.value = false
    step.value = 'done'
}
</script>

<style scoped>
.btn-accent {
    background-color: #d80621;
    color: #fff;
}
.btn-accent:hover {
    background-color: #b4051b;
}
.emoji {
    font-size: 1.8rem;
    cursor: pointer;
    opacity: 0.5;
    transition: 0.2s;
}
.emoji.active {
    opacity: 1;
    transform: scale(1.1);
}
.badge-display {
    margin-top: 1rem;
}
.badge.bg-bronze {
    background-color: #cd7f32;
}
textarea {
    resize: none;
}
</style>
