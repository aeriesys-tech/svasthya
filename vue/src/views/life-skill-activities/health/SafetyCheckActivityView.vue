<template>
    <div class="container py-4">
        <!-- STEP 1: Launch -->
        <div v-if="step === 1" class="text-center fade-section">
            <h2 class="fw-bold mb-3">High Alert – Pre-Shift Safety Check</h2>
            <p class="lead">
                You’re heading into a flagged zone tonight. Take 4 minutes to prepare your gear,
                your strategy, and your mindset.
            </p>
            <button class="btn btn-primary btn-lg mt-3" @click="nextStep">
                Start
            </button>
        </div>

        <!-- STEP 2: Scenario Simulation -->
        <div v-if="step === 2" class="fade-section">
            <h3 class="fw-bold mb-3">Shift Scenario</h3>
            <div class="scenario-box p-3">
                <p>
                    It’s <strong>20:20 hours</strong>. Your patrol zone has seen
                    <strong>4 thefts in the past 3 days</strong>. Lighting is poor in alleyways
                    behind Market Lane. Locals are anxious, and backup arrival time is estimated
                    at <strong>10 minutes or more</strong>.  
                </p>
                <p>
                    This shift requires <strong>calm</strong>, <strong>readiness</strong>, 
                    and high visibility.
                </p>
            </div>
            <button class="btn btn-primary mt-4" @click="nextStep">
                Continue to Checklist
            </button>
        </div>

        <!-- STEP 3: Gear Checklist -->
        <div v-if="step === 3" class="fade-section">
            <h3 class="fw-bold mb-3">Gear & Safety Checklist</h3>

            <div
                v-for="(item, index) in checklist"
                :key="item.label"
                class="check-item d-flex align-items-start p-2 mb-2"
            >
                <input
                    class="form-check-input mt-2"
                    type="checkbox"
                    v-model="item.checked"
                />
                <div class="ms-3">
                    <p class="m-0 fw-semibold">{{ item.label }}</p>
                    <small class="text-muted">{{ item.tip }}</small>
                </div>
            </div>

            <button
                class="btn btn-primary mt-4"
                :disabled="!allChecked"
                @click="nextStep"
            >
                Continue
            </button>
        </div>

        <!-- STEP 4: Situational Awareness Quiz -->
        <div v-if="step === 4" class="fade-section">
            <h3 class="fw-bold mb-3">Situational Awareness Check</h3>

            <!-- Question 1 -->
            <div class="mb-4">
                <p class="fw-semibold">
                    Scenario 1: A suspicious car follows your unit through 2 alleys. What’s your first move?
                </p>
                <button
                    v-for="(opt, i) in question1Options"
                    :key="i"
                    class="btn w-100 text-start mb-2"
                    :class="answer1 === opt ? 'btn-success' : 'btn-outline-secondary'"
                    @click="selectAnswer1(opt)"
                >
                    {{ opt }}
                </button>

                <p v-if="answer1" class="text-info small mt-2">
                    {{ answer1Feedback }}
                </p>
            </div>

            <!-- Question 2 -->
            <div class="mb-3">
                <p class="fw-semibold">
                    Scenario 2: Choose the safest patrol style for this shift.
                </p>
                <button
                    v-for="(opt, i) in question2Options"
                    :key="i"
                    class="btn w-100 text-start mb-2"
                    :class="answer2 === opt ? 'btn-success' : 'btn-outline-secondary'"
                    @click="answer2 = opt"
                >
                    {{ opt }}
                </button>
            </div>

            <button
                class="btn btn-primary mt-4"
                :disabled="!answer1 || !answer2"
                @click="nextStep"
            >
                Continue
            </button>
        </div>

        <!-- STEP 5: Stress Slider -->
        <div v-if="step === 5" class="fade-section">
            <h3 class="fw-bold mb-3">Emotional Self-Check</h3>

            <label class="fw-semibold">How alert or stressed are you feeling?</label>
            <input
                type="range"
                class="form-range"
                min="0"
                max="100"
                v-model="stress"
            />

            <p class="mt-2">
                Current State:
                <strong>{{ stressLabel }}</strong>
            </p>

            <!-- If high stress → show breathing button -->
            <div v-if="stress >= 70" class="mt-3">
                <p class="text-danger fw-semibold">You seem highly stressed.</p>
                <button class="btn btn-outline-primary" @click="startBreathing">
                    Start Tactical Breathing
                </button>
            </div>

            <button class="btn btn-primary mt-4" @click="nextStep">
                Continue
            </button>
        </div>

        <!-- STEP 5B: Tactical Breathing -->
        <div v-if="step === 'breathing'" class="fade-section text-center">
            <h3 class="fw-bold mb-3">Tactical Breathing</h3>

            <div class="breathing-circle mx-auto" :class="breathingPhase"></div>

            <p class="fw-bold mt-3 fs-4">{{ breathingText }} ({{ countdown }})</p>

            <button class="btn btn-secondary mt-4" @click="finishBreathing">
                Done
            </button>
        </div>

        <!-- STEP 6: Final Affirmation -->
        <div v-if="step === 6" class="text-center fade-section">
            <h3 class="fw-bold mb-3">Final Affirmation</h3>

            <p class="affirmation-text">
                “I am prepared, focused, and alert.”
            </p>

            <button class="btn btn-primary btn-lg mt-3" @click="finish">
                Confirm & Start Shift
            </button>
        </div>

        <!-- COMPLETED -->
        <div v-if="step === 7" class="text-center fade-section">
            <h2 class="fw-bold">You're Ready.</h2>
            <p class="lead">Stay sharp. You’ve got training, tools, and backup.</p>
            <button class="btn btn-outline-secondary mt-3 ms-2"
                @click="goHome">
                Back to activities
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const step = ref(1);

const nextStep = () => {
    step.value++;
};

function goHome() {
    router.push("/activity/life-skills/health");
}

//////////////////////////////////////////////////////////////////
// STEP 3 — CHECKLIST
//////////////////////////////////////////////////////////////////
const checklist = ref([
    { label: "Bulletproof vest secure", checked: false, tip: "Adjust straps properly." },
    { label: "Flashlight working", checked: false, tip: "Check battery and brightness." },
    { label: "Body cam on + tested", checked: false, tip: "Ensure footage is recording correctly." },
    { label: "Comms device functioning", checked: false, tip: "Test signal and volume." },
    { label: "Patrol vehicle checked", checked: false, tip: "Fuel, lights, tools inspected." },
    { label: "Backup contact confirmed", checked: false, tip: "Share route and availability." },
    { label: "Route map reviewed", checked: false, tip: "Know exits and high-visibility zones." }
]);

const allChecked = computed(() => checklist.value.every(i => i.checked));

//////////////////////////////////////////////////////////////////
// STEP 4 — QUIZ
//////////////////////////////////////////////////////////////////
const question1Options = [
    "A. Confront immediately",
    "B. Note plate, alert control, shift route",
    "C. Ignore unless they act"
];

const answer1 = ref(null);

const selectAnswer1 = (opt) => {
    answer1.value = opt;
};

const answer1Feedback = computed(() => {
    if (answer1.value === "B. Note plate, alert control, shift route")
        return "Correct. Staying aware without escalating maintains control.";
    return "Not ideal. Escalation or inaction can increase risk.";
});

const question2Options = [
    "Well-lit & high-visibility route",
    "Shortcuts through alleys",
    "Isolated low-visibility areas"
];

const answer2 = ref(null);

//////////////////////////////////////////////////////////////////
// STEP 5 — STRESS SLIDER
//////////////////////////////////////////////////////////////////
const stress = ref(50);

const stressLabel = computed(() => {
    if (stress.value < 30) return "Low Alert";
    if (stress.value < 70) return "Balanced";
    return "High Stress";
});

//////////////////////////////////////////////////////////////////
// STEP 5B — Breathing Animation
//////////////////////////////////////////////////////////////////
const countdown = ref(4);
const breathingPhase = ref("");
const breathingText = ref("");

let interval = null;

const startBreathing = () => {
    step.value = "breathing";
    startCycle();
};

const startCycle = () => {
    runPhase("inhale", 4, () => {
        runPhase("hold", 4, () => {
            runPhase("exhale", 4, () => {
                startCycle(); // repeat loop
            });
        });
    });
};

const runPhase = (phase, duration, next) => {
    breathingPhase.value = phase;
    breathingText.value = phase.toUpperCase();
    countdown.value = duration;

    clearInterval(interval);

    interval = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
            clearInterval(interval);
            next();
        }
    }, 1000);
};

const finishBreathing = () => {
    clearInterval(interval);
    step.value = 6;
};

//////////////////////////////////////////////////////////////////
// END
//////////////////////////////////////////////////////////////////
const finish = () => {
    step.value = 7;
};
</script>

<style scoped>
.fade-section {
    animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

.scenario-box {
    background: #f5f5f5;
    border-left: 5px solid #0d6efd;
    border-radius: 6px;
}

.check-item {
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e2e2e2;
}

.affirmation-text {
    font-size: 1.4rem;
    font-style: italic;
}

/* Breathing Circle */
.breathing-circle {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: #b3d7ff;
    transition: all 1s ease;
}

.inhale {
    transform: scale(1.25);
    background: #70b8ff;
}

.hold {
    transform: scale(1.35);
    background: #4da6ff;
}

.exhale {
    transform: scale(0.9);
    background: #9ccfff;
}
</style>
