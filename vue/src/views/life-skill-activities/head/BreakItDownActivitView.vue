<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Break It Down: Problem-Solving Journal</h1>

        <!-- Step 1: Welcome / Orientation -->
        <div v-if="step === 1" class="text-center">
            <p class="lead">
                Feeling stuck or overwhelmed? Let’s break the problem down together.
            </p>
            <button class="btn btn-primary mt-3 d-block mx-auto" @click="step++">Start</button>
        </div>

        <!-- Step 2: Choose the Problem -->
        <div v-else-if="step === 2">
            <label class="form-label">Briefly describe the challenge you want to think through:</label>
            <textarea class="form-control mb-3" v-model="problemText" placeholder="Write 1–2 sentences"></textarea>
            
            <label class="form-label">Category (optional):</label>
            <select class="form-select mb-3" v-model="problemCategory">
                <option value="">Select a category</option>
                <option>Work</option>
                <option>Personal</option>
                <option>Emotional</option>
                <option>Conflict</option>
                <option>Health</option>
            </select>
            
            <button class="btn btn-primary d-block mx-auto" :disabled="!problemText" @click="step++">Next</button>
        </div>

        <!-- Step 3: What I Know -->
        <div v-else-if="step === 3" class="text-center">
            <label class="form-label">What I Know (Facts Only)</label>
            <textarea class="form-control mb-3" v-model="factsText" placeholder="Write only what you know for sure."></textarea>
            <small class="text-muted d-block mb-3">Hint: Write only things you know for sure.</small>
            <button class="btn btn-primary d-block mx-auto" @click="step++">Next</button>
        </div>

        <!-- Step 4: What I Don’t Know -->
        <div v-else-if="step === 4" class="text-center">
            <label class="form-label">What I Don’t Know (Assumptions / Unclear)</label>
            <textarea class="form-control mb-3" v-model="unknownText" placeholder="Include assumptions or unanswered questions."></textarea>
            <small class="text-muted d-block mb-3">Tip: This helps separate uncertainty from facts.</small>
            <button class="btn btn-primary d-block mx-auto" @click="step++">Next</button>
        </div>

        <!-- Step 5: What I Can Control -->
        <div v-else-if="step === 5" class="text-center">
            <label class="form-label">What I Can Control</label>
            <textarea class="form-control mb-3" v-model="controlText" placeholder="Focus on your actions, choices, and mindset."></textarea>
            <small class="text-muted d-block mb-3">Example: My attitude, my next steps, my preparation.</small>
            <button class="btn btn-primary d-block mx-auto" @click="step++">Next</button>
        </div>

        <!-- Step 6: My Next Step -->
        <div v-else-if="step === 6" class="text-center">
            <label class="form-label">My Next Step</label>
            <textarea class="form-control mb-3" v-model="nextStepText" placeholder="Choose one small, realistic action you can take right now."></textarea>
            <button class="btn btn-primary d-block mx-auto" :disabled="!nextStepText" @click="step++">Next</button>
        </div>

        <!-- Step 7: Summary View -->
        <div v-else-if="step === 7" class="text-center">
            <h4 class="mb-3">Summary of Your Problem-Solving Journal</h4>
            <div class="card p-3 mb-2 text-start"><strong>Problem:</strong> {{ problemText }} <br> <em>{{ problemCategory }}</em></div>
            <div class="card p-3 mb-2 text-start"><strong>What I Know:</strong> {{ factsText }}</div>
            <div class="card p-3 mb-2 text-start"><strong>What I Don't Know:</strong> {{ unknownText }}</div>
            <div class="card p-3 mb-2 text-start"><strong>What I Can Control:</strong> {{ controlText }}</div>
            <div class="card p-3 mb-2 text-start"><strong>My Next Step:</strong> {{ nextStepText }}</div>
            
            <!-- Buttons in one line -->
            <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                <button class="btn btn-secondary" @click="step = 2">Edit</button>
                <button class="btn btn-success" @click="saveJournal">Save</button>
                <button class="btn btn-warning" @click="setReminder">Set Reminder</button>
            </div>
        </div>

        <!-- Step 8: Optional Reflection -->
        <div v-else-if="step === 8" class="text-center">
            <label class="form-label d-block mb-2">How do you feel after breaking this problem down?</label>
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                <button v-for="m in moods" :key="m.label" type="button"
                    class="btn"
                    :class="selectedMood === m.label ? 'btn-danger text-dark' : 'btn-outline-secondary text-dark'"
                    @click="selectedMood = m.label"
                >
                    {{ m.emoji }} {{ m.label }}
                </button>
            </div>
            <textarea class="form-control mb-3" v-model="reflectionText" placeholder="Optional reflection..."></textarea>
            <button class="btn btn-success d-block mx-auto" @click="submitReflection">Save Reflection</button>
        </div>

        <!-- Step 9: Outcome / Completion -->
        <div v-else-if="step === 9" class="text-center">
            <h3>Great job!</h3>
            <p>Your problem-solving journal entry has been saved.</p>
            <button class="btn btn-primary mt-3 d-block mx-auto" @click="goHome">Back to Activities</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const step = ref(1)

// Step 2: Problem input
const problemText = ref('')
const problemCategory = ref('')

// Steps 3–6: Journal boxes
const factsText = ref('')
const unknownText = ref('')
const controlText = ref('')
const nextStepText = ref('')

// Step 8: Optional reflection
const moods = [
    { label: 'Sad', emoji: '😞' },
    { label: 'Neutral', emoji: '😐' },
    { label: 'Happy', emoji: '🙂' },
    { label: 'Excited', emoji: '😄' }
]
const selectedMood = ref(null)
const reflectionText = ref('')

// Save journal to localStorage
const saveJournal = () => {
    const saved = JSON.parse(localStorage.getItem('clarity_journal_sessions') || '[]')
    saved.push({
        problemText: problemText.value,
        problemCategory: problemCategory.value,
        factsText: factsText.value,
        unknownText: unknownText.value,
        controlText: controlText.value,
        nextStepText: nextStepText.value,
        date: new Date().toISOString()
    })
    localStorage.setItem('clarity_journal_sessions', JSON.stringify(saved))
    step.value = 8
}

// Save reflection
const submitReflection = () => {
    const savedInsights = JSON.parse(localStorage.getItem('clarity_journal_insights') || '[]')
    savedInsights.push({
        problemText: problemText.value,
        mood: selectedMood.value,
        reflection: reflectionText.value,
        date: new Date().toISOString()
    })
    localStorage.setItem('clarity_journal_insights', JSON.stringify(savedInsights))
    step.value = 9
}

// Reminder placeholder
const setReminder = () => {
    alert('Reminder set successfully.')
}

// Go back to activities
const goHome = () => router.push('/activity')
</script>

<style scoped>
textarea {
    min-height: 80px;
}
.card {
    border-radius: 0.75rem;
    margin-bottom: 0.5rem;
}
</style>
