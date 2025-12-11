<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Conflict Resolution - Choose Your Fighters</h1>

        <div v-if="step === 1" class="text-center">
            <h4 class="mb-3">Step 1: Pick Your Battle</h4>
            <p>Select a conflict scenario that you want to resolve.</p>

            <div class="mb-3">
                <select v-model="selectedScenario" class="form-select w-auto mx-auto">
                    <option disabled value="">-- Select a Scenario --</option>
                    <option v-for="(s, index) in scenarios" :key="index" :value="s">
                        {{ s }}
                    </option>
                </select>
            </div>

            <button class="btn btn-accent" :disabled="!selectedScenario" @click="nextStep">
                Choose Your Fighter
            </button>
        </div>

        <div v-else-if="step === 2" class="text-center">
            <h4 class="mb-3">Step 2: Choose Your Fighter</h4>
            <p>Pick a character to handle your conflict!</p>

            <div class="row justify-content-center g-3">
                <div
                    class="col-12 col-md-4"
                    v-for="fighter in fighters"
                    :key="fighter.name"
                >
                    <div class="card h-100 text-center p-3 border-0 shadow-sm fighter-card"
                         @click="selectFighter(fighter)">
                        <h5 class="fw-bold mb-2">{{ fighter.name }}</h5>
                        <p class="fst-italic mb-1">“{{ fighter.catchphrase }}”</p>
                        <p class="small">{{ fighter.style }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="step === 3" class="text-center">
            <h4 class="mb-3">Step 3: Simulate the Conflict Scene</h4>
            <div class="card mx-auto p-4 shadow-sm w-75">
                <p class="fs-5"><strong>{{ fighter.name }}</strong> enters the scene!</p>
                <p class="text-muted fst-italic">“{{ fighter.actionLine }}”</p>
                <hr>
                <p>{{ fighter.outcome }}</p>
            </div>

            <div class="mt-4">
                <button class="btn btn-outline-secondary me-2" @click="step = 2">
                    Try Another Fighter
                </button>
                <button class="btn btn-accent" @click="step = 4">
                    Build Your Custom Fighter
                </button>
            </div>
        </div>

        <div v-else-if="step === 4" class="text-center">
            <h4 class="mb-3">Step 4: Build Your Custom Fighter</h4>

            <div class="row justify-content-center mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Thinking Strategy</label>
                    <select v-model="custom.thinking" class="form-select">
                        <option disabled value="">-- Select --</option>
                        <option>Stay calm</option>
                        <option>Ask questions</option>
                        <option>Pause before reacting</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Talking Style</label>
                    <select v-model="custom.talking" class="form-select">
                        <option disabled value="">-- Select --</option>
                        <option>Assertive</option>
                        <option>Gentle</option>
                        <option>Practical</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Action Move</label>
                    <select v-model="custom.action" class="form-select">
                        <option disabled value="">-- Select --</option>
                        <option>Compromise</option>
                        <option>Escalate</option>
                        <option>Step back</option>
                    </select>
                </div>
            </div>

            <button class="btn btn-accent" :disabled="!isCustomComplete" @click="finish">
                Save My Fighter
            </button>
        </div>

        <div v-else-if="step === 5" class="text-center">
            <h4 class="mb-3">Your Custom Conflict Avatar</h4>
            <div class="card w-50 mx-auto shadow-sm p-4">
                <p><strong>Thinking Strategy:</strong> {{ custom.thinking }}</p>
                <p><strong>Talking Style:</strong> {{ custom.talking }}</p>
                <p><strong>Action Move:</strong> {{ custom.action }}</p>
            </div>

            <div class="mt-4">
                <p class="fw-bold">Reflection:</p>
                <p>Who handled it best? What would YOU do in real life?</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const step = ref(1)
const selectedScenario = ref('')

const scenarios = [
    'My partner keeps dismissing my suggestions.',
    'A team member is always late and it’s affecting our shift.',
    'I’m frustrated with how resources are assigned.',
]

const fighters = [
    {
        name: 'The Shark',
        catchphrase: "Let’s end this, now!",
        style: 'Direct, dominating, no-nonsense.',
        actionLine: '“Hey, cut it out. We need to talk now!”',
        outcome: 'Tension eases, but the other person feels cornered.',
    },
    {
        name: 'The Owl',
        catchphrase: 'We rise when we reason.',
        style: 'Calm, collaborative, values logic and fairness.',
        actionLine: '“Let’s sit down. I want to understand where you’re coming from.”',
        outcome: 'The other person feels heard and respected — progress is made!',
    },
    {
        name: 'The Teddy',
        catchphrase: 'Hugs, not drama.',
        style: 'Kind-hearted, avoids fights to preserve relationships.',
        actionLine: '“It’s okay, maybe I was wrong anyway…”',
        outcome: 'Peace is maintained, but the problem remains unsolved.',
    },
    {
        name: 'The Fox',
        catchphrase: '50/50 is better than zero.',
        style: 'Practical, solution-focused, street-smart.',
        actionLine: '“Let’s find middle ground — what works for both of us?”',
        outcome: 'A fair compromise is reached and both parties feel okay.',
    },
    {
        name: 'The Turtle',
        catchphrase: 'Not my circus today.',
        style: 'Low-energy, backs off until the storm passes.',
        actionLine: '“I’ll deal with this later.”',
        outcome: 'Tension dies down, but the issue is still unresolved.',
    },
]

const fighter = ref(null)

const selectFighter = (f) => {
    fighter.value = f
    step.value = 3
}

const nextStep = () => {
    step.value = 2
}

const custom = ref({
    thinking: '',
    talking: '',
    action: '',
})

const isCustomComplete = computed(() =>
    custom.value.thinking && custom.value.talking && custom.value.action
)

const finish = () => {
    step.value = 5
}
</script>

<style scoped>
h1 {
    font-weight: 700;
    letter-spacing: 1px;
}

.fighter-card {
    cursor: pointer;
    border: 2px solid transparent;
}

.fighter-card:hover {
    transform: scale(1.05);
    border-color: var(--bs-primary);
}
</style>
