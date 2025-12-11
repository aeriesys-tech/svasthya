<template>
    <div class="container py-5">
        <h1 class="text-center mb-4">My Role in the Team</h1>
        <p class="text-muted text-center mb-4">
            Strong teams are made of different strengths. Discover yours and set a goal to grow this week.
        </p>

        <!-- Step 1: Start -->
        <div v-if="step === 1" class="text-center">
            <button class="btn btn-primary btn-lg" @click="nextStep">Start Reflection</button>
        </div>

        <!-- Step 2: Quiz -->
        <div v-else-if="step === 2">
            <div v-if="currentQ">
                <h5 class="fw-bold mb-3">{{ currentQ.text }}</h5>
                <div class="d-grid gap-2 mb-3">
                    <button
                        v-for="(opt, i) in currentQ.options"
                        :key="i"
                        class="btn btn-outline-secondary"
                        @click="selectOption(opt)"
                    >
                        {{ opt }}
                    </button>
                </div>
                <div v-if="currentQ.allowText">
                    <textarea
                        v-model="customAnswer"
                        class="form-control"
                        rows="3"
                        placeholder="Your answer..."
                    ></textarea>
                </div>

                <div class="text-center mt-3">
                    <button class="btn btn-primary" @click="nextQuestion">Next</button>
                </div>
            </div>
        </div>

        <!-- Step 3: Role Summary -->
        <div v-else-if="step === 3" class="text-center">
            <h3 class="text-accent fw-bold mb-3">{{ roleLabel }}</h3>
            <p class="lead">{{ roleDescription }}</p>
            <div class="mt-4">
                <button class="btn btn-success" @click="step = 4">Set Weekly Goal</button>
            </div>
        </div>

        <!-- Step 4: Weekly Goal -->
        <div v-else-if="step === 4">
            <h4 class="text-center mb-3">What’s one way you’ll support your team better this week?</h4>
            <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                <button
                    v-for="(g, i) in goalSuggestions"
                    :key="i"
                    class="btn btn-outline-secondary btn-sm"
                    @click="goal = g"
                >
                    {{ g }}
                </button>
            </div>
            <textarea
                v-model="goal"
                class="form-control mb-3"
                rows="3"
                placeholder="Write your own goal..."
            ></textarea>

            <div class="text-center">
                <button class="btn btn-success" :disabled="!goal.trim()" @click="nextStep">
                    Save Goal
                </button>
            </div>
        </div>

        <!-- Step 5: Done -->
        <div v-else-if="step === 5" class="text-center">
            <h3 class="text-success mb-3">Goal Saved!</h3>
            <p>Every step counts. Keep showing up for your team.</p>
            <div class="d-grid gap-3" style="max-width: 300px; margin:auto;">
                <button class="btn btn-outline-primary">View Team Commitment Log</button>
                <button class="btn btn-outline-secondary" @click="restart">Try Again</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const step = ref(1)
const currentIndex = ref(0)
const customAnswer = ref('')
const answers = ref({})
const goal = ref('')

const questions = [
    {
        text: 'What role do you usually take in a team?',
        options: [
            'The leader who coordinates',
            'The doer who gets things done',
            'The planner who organizes',
            'The problem-solver under pressure',
            'The communicator who keeps everyone in sync',
            'The motivator who uplifts others',
        ],
    },
    {
        text: 'What team strength do you value most in others?',
        options: ['Reliability', 'Calmness', 'Clear communication', 'Initiative', 'Empathy', 'Planning'],
    },
    {
        text: 'How do you usually respond when team stress is high?',
        options: [
            'I try to stay calm and lead by example',
            'I focus on getting tasks done',
            'I check in on others',
            'I try to keep the mood light',
            'I step back and observe before acting',
        ],
    },
    {
        text: 'What makes you a good teammate?',
        options: [
            'I follow through on my tasks',
            'I stay calm under pressure',
            'I support others quietly',
            'I bring positive energy',
            'I speak up when it matters',
        ],
        allowText: true,
    },
    {
        text: 'One thing you’d like to improve as a teammate?',
        options: [
            'Being more open with feedback',
            'Checking in with others more',
            'Planning ahead',
            'Communicating clearly',
            'Staying patient under pressure',
        ],
        allowText: true,
    },
]

const currentQ = computed(() => questions[currentIndex.value] || null)

const selectOption = (opt) => {
    answers.value[currentQ.value.text] = opt
}

const nextQuestion = () => {
    if (customAnswer.value.trim()) {
        answers.value[currentQ.value.text] = customAnswer.value
        customAnswer.value = ''
    }
    if (currentIndex.value < questions.length - 1) {
        currentIndex.value++
    } else {
        step.value = 3
    }
}

const roleLabel = computed(() => {
    const first = Object.values(answers.value)[0] || ''
    if (first.includes('leader')) return 'Leadership Coordinator'
    if (first.includes('doer')) return 'Dependable Doer'
    if (first.includes('communicator')) return 'Team Communicator'
    if (first.includes('planner')) return 'Strategic Planner'
    return 'Calm Anchor'
})

const roleDescription = computed(() => {
    switch (roleLabel.value) {
        case 'Leadership Coordinator':
            return 'You take initiative and guide others clearly. Your steady hand helps the team stay focused.'
        case 'Dependable Doer':
            return 'You’re the steady hand who gets things done. Others trust you to carry the team forward.'
        case 'Team Communicator':
            return 'You keep things flowing with clarity and connection. Keep those lines open.'
        case 'Strategic Planner':
            return 'You bring structure and foresight, ensuring smooth coordination and readiness.'
        default:
            return 'Your calm presence keeps your team grounded, especially when pressure rises.'
    }
})

const goalSuggestions = [
    'Check in with someone I haven’t spoken to lately.',
    'Ask how I can help during briefing.',
    'Stay patient when things get tense.',
    'Communicate more clearly.',
]

const nextStep = () => step.value++
const restart = () => {
    step.value = 1
    currentIndex.value = 0
    answers.value = {}
    goal.value = ''
}
</script>

<style scoped>
.text-accent {
    color: #d80621;
}
</style>
