<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">The Fork in the Road Activity</h1>

        <!-- Step 1: Intro / Welcome -->
        <div v-if="step === 1" class="text-center">
            <img src="/images/roadfork.webp" class="img-fluid mb-3 rounded" alt="Fork in the road">
            <p>You’re at a metaphorical fork in the road — two paths ahead. Each path represents a choice you could make. You’ll get a peek at how each path fits your goals, values, and wellbeing.</p>
            <br>
            <!-- <iframe width="100%" height="200" src="https://www.youtube.com/embed/7LJMHXYgoWE?si=GVSCZJ7DL8LCfoeQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe> -->
            <!-- <br> -->
            <button class="btn btn-primary mt-3" @click="step++">Start Journey</button>
        </div>

        <!-- Step 2: Aim -->
        <div v-else-if="step === 2" class="text-center">
            <p class="lead">Let’s help you find clarity and choose the path that feels right.</p>
            <button class="btn btn-primary mt-3" @click="step++">Next</button>
        </div>

        <!-- Step 3: Identify Dilemma -->
        <div v-else-if="step === 3">
            <label class="form-label">What’s the challenge or decision you’re facing?</label>
            <textarea class="form-control mb-3" v-model="challenge" placeholder="e.g. Should I ask for a shift change?"></textarea>
            <button class="btn btn-primary" :disabled="!challenge" @click="step++">Next</button>
        </div>

        <!-- Step 4: Choose Two Paths -->
        <div v-else-if="step === 4">
            <label class="form-label">Label each path with one possible action you’re considering:</label>
            <input type="text" class="form-control mb-3" v-model="pathA" placeholder="Path A e.g. Speak to my senior">
            <input type="text" class="form-control mb-3" v-model="pathB" placeholder="Path B e.g. Keep managing alone">
            <button class="btn btn-primary" :disabled="!pathA || !pathB" @click="step++">Next</button>
        </div>

        <!-- Step 5: Journey Along Each Path -->
        <div v-else-if="step === 5">
            <h4 class="mb-3">{{ currentPath === 'A' ? `Path A: ${pathA}` : `Path B: ${pathB}` }}</h4>

            <label>Will this help me long-term?</label>
            <select class="form-select mb-2" v-model="currentAnswers.longTerm">
                <option disabled value="">Select</option>
                <option>Definitely</option>
                <option>Maybe</option>
                <option>Not likely</option>
            </select>

            <label>Does this match my role and values?</label>
            <select class="form-select mb-2" v-model="currentAnswers.values">
                <option disabled value="">Select</option>
                <option>Yes</option>
                <option>Maybe</option>
                <option>No</option>
            </select>

            <label>Is this the honest, constructive step?</label>
            <select class="form-select mb-2" v-model="currentAnswers.constructive">
                <option disabled value="">Select</option>
                <option>😊 Happy</option>
                <option>😐 Unsure</option>
                <option>😞 Sad</option>
            </select>

            <button class="btn btn-primary mt-2" :disabled="!answersComplete" @click="nextPath">Next</button>
        </div>

        <!-- Step 6: Summary Comparison -->
        <div v-else-if="step === 6">
            <h4 class="text-center mb-3">Which path feels right for you now?</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card p-3">
                        <h5>Path A: {{ pathA }}</h5>
                        <ul>
                            <li>Long-term: {{ answersA.longTerm }}</li>
                            <li>Values: {{ answersA.values }}</li>
                            <li>Constructive: {{ answersA.constructive }}</li>
                        </ul>
                        <button class="btn btn-success w-100" @click="choosePath('A')">Choose Path A</button>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card p-3">
                        <h5>Path B: {{ pathB }}</h5>
                        <ul>
                            <li>Long-term: {{ answersB.longTerm }}</li>
                            <li>Values: {{ answersB.values }}</li>
                            <li>Constructive: {{ answersB.constructive }}</li>
                        </ul>
                        <button class="btn btn-success w-100" @click="choosePath('B')">Choose Path B</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 7: Reason for Choice -->
        <div v-else-if="step === 7">
            <label>Why does this path feel right for you?</label>
            <textarea class="form-control mb-3" v-model="reason" placeholder="e.g. It matches my values and long-term goals"></textarea>
            <button class="btn btn-primary" :disabled="!reason" @click="step++">Save</button>
        </div>

        <!-- Step 8: Save / Reminder Options -->
        <div v-else-if="step === 8" class="text-center">
            <p>Would you like to save this decision or set a reminder to revisit it?</p>
            <button class="btn btn-secondary m-2" @click="saveDecision">Save as Journal Entry</button>
            <button class="btn btn-warning m-2" @click="setReminder">Set Reminder Notification</button>
            <button class="btn btn-primary m-2" @click="goHome">Return to Activities</button>
        </div>

        <!-- Step 9: Outcome Message -->
        <div v-else-if="step === 9" class="text-center">
            <h3>Great job working through your decision!</h3>
            <p>You’ve taken a big step toward clarity and confidence.</p>
            <button class="btn btn-primary mt-3" @click="goHome">Back to Activities</button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const step = ref(1)
const currentPath = ref('A')

// Decision text and paths
const challenge = ref('')
const pathA = ref('')
const pathB = ref('')

// Two separate answer models
const answersA = ref({ longTerm: '', values: '', constructive: '' })
const answersB = ref({ longTerm: '', values: '', constructive: '' })

const chosenPath = ref('')
const reason = ref('')

// Step 5 next path logic
const nextPath = () => {
    if (currentPath.value === 'A') {
        currentPath.value = 'B'
    } else {
        step.value = 6
    }
}

// Current answers computed for form binding
const currentAnswers = computed({
    get() {
        return currentPath.value === 'A' ? answersA.value : answersB.value
    },
    set(val) {
        if (currentPath.value === 'A') answersA.value = val
        else answersB.value = val
    }
})

// Check if all answers are filled
const answersComplete = computed(() => {
    return currentAnswers.value.longTerm && currentAnswers.value.values && currentAnswers.value.constructive
})

const choosePath = (path) => {
    chosenPath.value = path
    step.value = 7
}

const saveDecision = () => {
    const saved = JSON.parse(localStorage.getItem('decisions') || '[]')
    saved.push({
        challenge: challenge.value,
        pathA: pathA.value,
        pathB: pathB.value,
        answersA: answersA.value,
        answersB: answersB.value,
        chosenPath: chosenPath.value,
        reason: reason.value
    })
    localStorage.setItem('decisions', JSON.stringify(saved))
    step.value = 9
}

const setReminder = () => {
    alert('Reminder set successfully.')
}

const goHome = () => router.push('/activity')
</script>

<style scoped>
textarea {
    min-height: 80px;
}
img {
    height: 400px;
    object-fit: cover;
}
.card {
    border-radius: 0.75rem;
}
</style>
