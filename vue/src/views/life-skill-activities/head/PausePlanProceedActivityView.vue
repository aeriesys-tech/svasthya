<template>
    <div class="container py-4">
        <h1 class="text-center mb-4">Pause–Plan–Proceed Simulation</h1>

        <!-- Step 1: Start Simulation -->
        <div v-if="step === 1" class="text-center">
            <p>Ready to practice decision-making in a real-world scenario?</p>
            <button class="btn btn-primary mt-3" @click="step++">Start Scenario Simulation</button>
        </div>

        <!-- Step 2: Choose Scenario Category -->
        <div v-else-if="step === 2">
            <p class="text-center mb-3">Select a Scenario Category:</p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    class="btn btn-outline-primary flex-grow-1"
                    style="min-width: 120px;"
                    @click="selectCategory(cat)"
                >
                    {{ cat.name }}
                </button>
            </div>
        </div>

        <!-- Step 3: Present Scenario -->
        <div v-else-if="step === 3" class="text-center">
            <h5>{{ currentScenario.title }}</h5>
            <p>{{ currentScenario.narrative }}</p>
            <button class="btn btn-primary mt-3" @click="step++">Pause</button>
        </div>

        <!-- Step 4: PAUSE -->
        <div v-else-if="step === 4" class="text-center">
            <p>How are you feeling right now?</p>
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-2">
                <button
                    v-for="emo in emotions"
                    :key="emo"
                    :class="['btn', selectedEmotion === emo ? 'btn-success' : 'btn-outline-secondary']"
                    @click="selectedEmotion = emo"
                >
                    {{ emo }}
                </button>
            </div>
            <label class="d-block mt-2">Intensity (1–5):</label>
            <input type="range" min="1" max="5" v-model="emotionIntensity" class="form-range mb-3">

            <div class="mb-3">
                <p>Take a 30-second calming exercise:</p>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" :style="{width: `${breathProgress}%`}"></div>
                </div>
            </div>

            <button
                class="btn btn-primary"
                :disabled="!selectedEmotion || emotionIntensity < 1 || breathProgress < 100"
                @click="step++"
            >Plan</button>
        </div>

        <!-- Step 5: PLAN -->
        <div v-else-if="step === 5">
            <p class="text-center mb-2">Plan: List 3 possible responses</p>
            <div v-for="(option, index) in planOptions" :key="index" class="mb-2">
                <input
                    v-model="planOptions[index].text"
                    class="form-control mb-1"
                    :placeholder="`Option ${index+1}`"
                />
                <select v-model="planOptions[index].color" class="form-select mb-2">
                    <option disabled value="">Select</option>
                    <option value="success">Helpful 🟢</option>
                    <option value="warning">Risky 🟡</option>
                    <option value="danger">Harmful 🔴</option>
                </select>
            </div>
            <button
                class="btn btn-primary w-100 mt-2"
                :disabled="!allPlanFilled"
                @click="step++"
            >Proceed</button>
        </div>

        <!-- Step 6: PROCEED -->
        <div v-else-if="step === 6">
            <p class="text-center mb-2">Choose the most appropriate option:</p>
            <div class="d-flex flex-column gap-2 mb-3">
                <button
                    v-for="(option, index) in planOptions"
                    :key="index"
                    :class="['btn', selectedOption === index ? `btn-${option.color}` : 'btn-outline-secondary']"
                    @click="selectedOption = index"
                >
                    {{ option.text }}
                </button>
            </div>

            <label class="form-label">Reflection: Why this choice?</label>
            <textarea v-model="reflection" class="form-control mb-2" rows="3" placeholder="Type your reflection..."></textarea>

            <button
                class="btn btn-primary w-100 mt-2"
                :disabled="selectedOption === null || !reflection"
                @click="step++"
            >See Feedback</button>
        </div>

        <!-- Step 7: Feedback -->
        <div v-else-if="step === 7" class="text-center">
            <h5>Best-Practice Feedback</h5>
            <p>{{ currentScenario.feedback }}</p>

            <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                <button class="btn btn-success" @click="saveSession">Save Thoughts</button>
                <button class="btn btn-primary" @click="step = 1">Back to Start</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'

// Step tracker
const step = ref(1)

// Scenario categories
const categories = [
    { id: 1, name: 'Workplace Conflict' },
    { id: 2, name: 'Citizen Interaction' },
    { id: 3, name: 'Team Dynamics' },
    { id: 4, name: 'Personal Stress & Home Life' },
    { id: 5, name: 'Ethical & Emotional Dilemmas' },
    { id: 6, name: 'Safety & High-Pressure' },
    { id: 7, name: 'Self-Doubt & Motivation' },
]

const scenarios = {
    1: [ // Workplace Conflict
        {
            title: "Senior’s Harsh Feedback",
            narrative: "Your senior publicly criticizes your report, calling it “careless” in front of colleagues.",
            emotions: ["Anger", "Embarrassment", "Frustration"],
            suggestions: [
                { text: "Ask privately for specific improvements.", color: "success" },
                { text: "Ignore and move on.", color: "warning" },
                { text: "Snap back angrily.", color: "danger" }
            ],
            feedback: "Seek clarification privately; maintain professionalism; address issue calmly."
        },
        {
            title: "Colleague Avoiding Work",
            narrative: "A colleague regularly leaves paperwork for you to finish at the end of your long shifts.",
            emotions: ["Irritation", "Resentment", "Exhaustion"],
            suggestions: [
                { text: "Have a private conversation to understand the issue.", color: "success" },
                { text: "Keep doing the work silently.", color: "warning" },
                { text: "Confront them publicly.", color: "danger" }
            ],
            feedback: "Address issue privately, be curious not accusatory, aim for cooperative solution."
        },
        {
            title: "Being Interrupted in Briefing",
            narrative: "You try to share important info, but a senior interrupts you sharply.",
            emotions: ["Humiliation", "Anger", "Helplessness"],
            suggestions: [
                { text: "Ask after the meeting if you can clarify your point.", color: "success" },
                { text: "Stay quiet the rest of the briefing.", color: "warning" },
                { text: "Interrupt them back.", color: "danger" }
            ],
            feedback: "Maintain respect; seek space later to communicate critical information."
        }
    ],

    2: [ // Citizen Interaction
        {
            title: "Aggressive Civilian at Checkpoint",
            narrative: "A driver shouts at you and refuses to show ID, accusing you of harassment.",
            emotions: ["Fear", "Frustration", "Tension"],
            suggestions: [
                { text: "Repeat request calmly.", color: "success" },
                { text: "Raise your voice slightly.", color: "warning" },
                { text: "Threaten punishment in anger.", color: "danger" }
            ],
            feedback: "De-escalate verbal aggression; maintain neutral tone."
        },
        {
            title: "Delayed Domestic Dispute Call",
            narrative: "At a domestic call, a person blames you for arriving late.",
            emotions: ["Overwhelm", "Irritation", "Confusion"],
            suggestions: [
                { text: "Acknowledge their frustration gently.", color: "success" },
                { text: "Become quiet and avoid interaction.", color: "warning" },
                { text: "Respond defensively.", color: "danger" }
            ],
            feedback: "Acknowledge feelings, gather facts, avoid personal defensiveness."
        }
    ],

    3: [ // Team Dynamics
        {
            title: "Gossip About You",
            narrative: "You learn that false rumours are being spread about you.",
            emotions: ["Hurt", "Betrayal", "Anger"],
            suggestions: [
                { text: "Clarify privately with the person involved.", color: "success" },
                { text: "Ignore it completely.", color: "warning" },
                { text: "Confront them publicly.", color: "danger" }
            ],
            feedback: "Address privately, avoid escalation, aim for clarity not conflict."
        },
        {
            title: "Junior Disrespecting Instructions",
            narrative: "A junior dismisses your direction saying “I know better.”",
            emotions: ["Ego hurt", "Irritation"],
            suggestions: [
                { text: "Correct them calmly and explain.", color: "success" },
                { text: "Ignore them.", color: "warning" },
                { text: "Retaliate harshly.", color: "danger" }
            ],
            feedback: "Calm correction establishes boundaries without humiliation."
        }
    ],

    4: [ // Personal Stress & Home Life
        {
            title: "Family Argument After Shift",
            narrative: "After a 13-hour shift, a family member complains you “never have time for them.”",
            emotions: ["Exhaustion", "Guilt", "Irritation"],
            suggestions: [
                { text: "Explain calmly & plan time to talk.", color: "success" },
                { text: "Withdraw silently.", color: "warning" },
                { text: "Shout back.", color: "danger" }
            ],
            feedback: "Emotional regulation + honest communication improves family stability."
        },
        {
            title: "Struggling With Sleep",
            narrative: "Poor sleep for 4 days; you feel your patience slipping.",
            emotions: ["Fear", "Fatigue", "Irritability"],
            suggestions: [
                { text: "Follow small calming routine tonight.", color: "success" },
                { text: "Use phone until sleep.", color: "warning" },
                { text: "Ignore the issue.", color: "danger" }
            ],
            feedback: "Small immediate steps help; sleep issues need attention, not avoidance."
        }
    ],

    5: [ // Ethical & Emotional Dilemmas
        {
            title: "Witnessing Unfair Treatment",
            narrative: "You see a senior officer speaking harshly to a civilian unnecessarily.",
            emotions: ["Moral discomfort", "Hesitation", "Fear"],
            suggestions: [
                { text: "Speak privately later.", color: "success" },
                { text: "Tell yourself it’s not your place.", color: "warning" },
                { text: "Confront them immediately in anger.", color: "danger" }
            ],
            feedback: "Private feedback > public confrontation; supports ethical culture."
        }
    ],

    6: [ // Safety & High-Pressure
        {
            title: "Citizens Starting a Fight",
            narrative: "Two people start pushing each other; you’re alone at the scene.",
            emotions: ["Adrenaline", "Fear", "Urgency"],
            suggestions: [
                { text: "Give verbal commands + call backup.", color: "success" },
                { text: "Try to break it alone.", color: "warning" },
                { text: "Use force impulsively.", color: "danger" }
            ],
            feedback: "Prioritize safety; verbal control + backup is best practice."
        }
    ],

    7: [ // Self-Doubt & Motivation
        {
            title: "Feeling Undervalued",
            narrative: "You feel your hard work is unnoticed despite consistent effort.",
            emotions: ["Sadness", "Demotivation", "Frustration"],
            suggestions: [
                { text: "Share career goals with supervisor.", color: "success" },
                { text: "Stay silent and resentful.", color: "warning" },
                { text: "Withdraw from team.", color: "danger" }
            ],
            feedback: "Asking for support is healthy; withdrawal worsens isolation."
        }
    ]
};


const currentCategory = ref(null)
const currentScenario = reactive({ title: '', narrative: '', feedback: '' })

// Step 4: Pause
const emotions = ['Anger', 'Stress', 'Anxiety', 'Guilt', 'Frustration', 'Embarrassment']
const selectedEmotion = ref('')
const emotionIntensity = ref(1)
const breathProgress = ref(0)

onMounted(() => {
    const interval = setInterval(() => {
        if (step.value === 4 && breathProgress.value < 100) breathProgress.value += 1
    }, 300)
})

// Step 5: Plan
const planOptions = reactive([
    { text: '', color: '' },
    { text: '', color: '' },
    { text: '', color: '' }
])
const allPlanFilled = computed(() => planOptions.every(o => o.text && o.color))

// Step 6: Proceed
const selectedOption = ref(null)
const reflection = ref('')

// Functions
const selectCategory = (cat) => {
    currentCategory.value = cat.id
    const scens = scenarios[cat.id]
    if (scens && scens.length) {
        Object.assign(currentScenario, scens[0])
        step.value = 3
    }
}

const saveSession = () => {
    const saved = JSON.parse(localStorage.getItem('scenarioSessions') || '[]')
    saved.push({
        category: currentCategory.value,
        scenario: currentScenario.title,
        emotion: selectedEmotion.value,
        intensity: emotionIntensity.value,
        planOptions: JSON.parse(JSON.stringify(planOptions)),
        chosenOption: planOptions[selectedOption.value],
        reflection: reflection.value
    })
    localStorage.setItem('scenarioSessions', JSON.stringify(saved))
    alert('Session saved successfully!')
    step.value = 1
};
</script>

<style scoped>
h1, h5, p, label, button {
    text-align: center;
}
textarea {
    min-height: 80px;
};
</style>
