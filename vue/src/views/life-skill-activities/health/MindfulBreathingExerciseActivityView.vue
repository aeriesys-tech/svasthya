<template>
    <div class="breathing-wrapper">

        <!-- WELCOME SCREEN -->
        <div v-if="step === 1" class="screen">
            <h1 class="mb-3 text-center">Mindful Breathing</h1>
            <h2 class="title">Feeling tense?</h2>
            <p class="subtitle">
                Let’s take 5 minutes to reset your nervous system.
            </p>
            <button class="primary-btn" @click="step = 2">Start Breathing Exercise</button>
        </div>

        <!-- SELECT TECHNIQUE -->
        <div v-if="step === 2" class="screen">
            <h2 class="title">Choose Your Breathing Pattern</h2>

            <div 
                class="tech-option" 
                :class="{active: technique === 'box'}"
                @click="selectTechnique('box')"
            >
                <h3>Box Breathing (4-4-4-4)</h3>
                <p>Used by tactical teams to stay calm under pressure.</p>
            </div>

            <div 
                class="tech-option" 
                :class="{active: technique === '478'}"
                @click="selectTechnique('478')"
            >
                <h3>4-7-8 Breathing</h3>
                <p>Clinically tested to reduce anxiety & improve sleep.</p>
            </div>

            <button 
                class="primary-btn mt-3" 
                :disabled="!technique"
                @click="step = 3"
            >
                Continue
            </button>
        </div>

        <!-- GUIDED BREATHING SESSION -->
        <div v-if="step === 3" class="screen breathing-session">

            <h2 class="title">Follow the Rhythm</h2>
            <p class="subtitle">Focus on your breath.</p>

            <!-- PRE-START COUNTDOWN -->
            <div v-if="showCountdown" class="countdown-box">
                <h1 class="countdown-number">{{ countdown }}</h1>
            </div>

            <!-- BREATHING BUBBLE -->
            <div class="circle-container mb-5" v-else>
                <div 
                    class="breath-circle" 
                    :class="breathPhase"
                    :style="{ background: phaseColor }"
                ></div>
            </div>

            <!-- PHASE TEXT WITH COUNTDOWN -->
            <p class="phase-text mt-3">{{ phaseText }} – {{ phaseCount }}</p>

            <button 
                class="primary-btn mt-3"
                @click="toggleBreathing"
            >
                {{ isBreathing ? 'Pause' : 'Start' }}
            </button>

            <button 
                class="secondary-btn mt-2"
                @click="finishSession"
            >
                Done
            </button>
        </div>

        <!-- STRESS RATING -->
        <div v-if="step === 4" class="screen">
            <h2 class="title">How do you feel now?</h2>

            <label class="slider-label">Stress Level</label>
            <input 
                type="range"
                min="1"
                max="5"
                v-model="afterRating"
                class="slider"
            />

            <p class="rating-display">{{ afterRatingText }}</p>

            <label class="slider-label mt-3">Quick Reflection</label>

            <select v-model="reflection" class="select">
                <option disabled value="">Choose one</option>
                <option>More grounded</option>
                <option>Still tense, but better</option>
                <option>Feel lighter</option>
                <option>Breathing helped a little</option>
            </select>

            <button 
                class="primary-btn mt-4"
                @click="saveSession"
            >
                Save Session
            </button>
        </div>

        <!-- SESSION SAVED -->
        <div v-if="step === 5" class="screen">
            <h2 class="title">Session Saved</h2>
            <p class="subtitle">You can repeat this anytime.</p>
            <button class="primary-btn" @click="resetAll">
                Back to Home
            </button>
        </div>

        <!-- QUICK CALM BUTTON -->
        <button class="quick-calm-btn" @click="startQuickCalm">
            Quick Calm
        </button>

        <!-- QUICK CALM OVERLAY -->
        <div v-if="quickCalm" class="quick-calm-overlay">
            <div class="quick-modal">
                <h2 class="title center">2-Minute Quick Calm</h2>

                <div class="circle-container small">
                    <div 
                        class="breath-circle" 
                        :class="breathPhase"
                        :style="{ background: phaseColor }"
                    ></div>
                </div>

                <p class="phase-text">{{ phaseText }} – {{ phaseCount }}</p>

                <button 
                    class="primary-btn mt-3"
                    @click="stopQuickCalm"
                >
                    Stop
                </button>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onBeforeUnmount, computed } from "vue";

/* STEP CONTROL */
const step = ref(1);
const technique = ref(null);

/* BREATHING */
const isBreathing = ref(false);
const breathPhase = ref("");
const phaseText = ref("");
const phaseCount = ref(0);
const phaseColor = ref("#4e8cff");

let timer = null;
let secondTimer = null;
let phases = [];
let index = 0;

/* COUNTDOWN BEFORE START */
const showCountdown = ref(false);
const countdown = ref(3);

/* STRESS RATING */
const afterRating = ref(3);
const reflection = ref("");

/* QUICK CALM */
const quickCalm = ref(false);

/* TECHNIQUE SELECT */
function selectTechnique(type) {
    technique.value = type;
}

/* PRE-SESSION COUNTDOWN */
function startCountdown(startAfter) {
    showCountdown.value = true;
    countdown.value = 3;

    const interval = setInterval(() => {
        countdown.value--;

        if (countdown.value === 0) {
            clearInterval(interval);
            showCountdown.value = false;
            startAfter();
        }
    }, 1000);
}

/* DEFINE PHASES */
function preparePhases() {
    if (technique.value === "box") {
        phases = [
            { phase: "inhale", seconds: 4, label: "Inhale", color: "#4CAF50" },
            { phase: "hold", seconds: 4, label: "Hold", color: "#FFC107" },
            { phase: "exhale", seconds: 4, label: "Exhale", color: "#F44336" },
            { phase: "hold", seconds: 4, label: "Hold", color: "#03A9F4" }
        ];
    } else {
        phases = [
            { phase: "inhale", seconds: 4, label: "Inhale", color: "#4CAF50" },
            { phase: "hold", seconds: 7, label: "Hold", color: "#FFC107" },
            { phase: "exhale", seconds: 8, label: "Exhale", color: "#F44336" }
        ];
    }
}

/* START FULL BREATHING LOOP */
function startBreathingCycle() {
    clearTimeout(timer);
    clearInterval(secondTimer);

    preparePhases();
    index = 0;
    runPhase();
}

/* RUN SINGLE PHASE WITH COUNTDOWN */
function runPhase() {
    const p = phases[index];

    if (!p) {
        index = 0;
        return runPhase();
    }

    breathPhase.value = p.phase;
    phaseText.value = p.label;
    phaseColor.value = p.color;

    phaseCount.value = p.seconds;

    clearInterval(secondTimer);

    secondTimer = setInterval(() => {
        phaseCount.value--;
        if (phaseCount.value <= 0) {
            clearInterval(secondTimer);
        }
    }, 1000);

    timer = setTimeout(() => {
        index = (index + 1) % phases.length;
        runPhase();
    }, p.seconds * 1000);
}

/* START / PAUSE */
function toggleBreathing() {
    if (!isBreathing.value) {
        isBreathing.value = true;
        startCountdown(startBreathingCycle);
    } else {
        isBreathing.value = false;
        clearTimeout(timer);
        clearInterval(secondTimer);
    }
}

function finishSession() {
    clearTimeout(timer);
    clearInterval(secondTimer);
    isBreathing.value = false;
    step.value = 4;
}

/* STRESS TEXT */
const afterRatingText = computed(() => {
    return {
        1: "Very stressed",
        2: "Tense",
        3: "Neutral",
        4: "Calmer",
        5: "Very calm"
    }[afterRating.value];
});

/* SAVE SESSION */
function saveSession() {
    const log = JSON.parse(localStorage.getItem("mindfulnessLog") || "[]");

    log.push({
        technique: technique.value,
        date: new Date().toLocaleString(),
        after: afterRating.value,
        reflection: reflection.value
    });

    localStorage.setItem("mindfulnessLog", JSON.stringify(log));
    step.value = 5;
}

function resetAll() {
    step.value = 1;
    technique.value = null;
}

/* QUICK CALM */
function startQuickCalm() {
    quickCalm.value = true;
    technique.value = "box";
    startCountdown(() => {
        index = 0;
        preparePhases();
        runPhase();
    });
}

function stopQuickCalm() {
    quickCalm.value = false;
    clearTimeout(timer);
    clearInterval(secondTimer);
}

/* CLEANUP */
onBeforeUnmount(() => {
    clearTimeout(timer);
    clearInterval(secondTimer);
});
</script>

<style scoped>
.countdown-box {
    text-align: center;
    margin-top: 20px;
}
.countdown-number {
    font-size: 70px;
    font-weight: 700;
    color: #4e8cff;
}

.breathing-wrapper {
    padding: 20px;
    position: relative;
}

.screen {
    animation: fadeIn 0.4s ease;
}

.title {
    font-weight: 700;
    font-size: 1.4rem;
    margin-bottom: 6px;
}

.subtitle {
    color: #6b7a88;
    font-size: 0.95rem;
    margin-bottom: 18px;
}

.tech-option {
    padding: 15px;
    border: 2px solid #ccc;
    border-radius: 14px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: 0.25s;
}
.tech-option.active {
    border-color: #4e8cff;
    background: #e8f0ff;
}

.primary-btn {
    background: #4e8cff;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 12px;
    width: 100%;
    font-size: 1rem;
}
.primary-btn:disabled {
    background: #9dbfff;
}

.secondary-btn {
    background: #ddd;
    border: none;
    padding: 10px;
    border-radius: 12px;
    width: 100%;
    color: #333;
}

.slider {
    width: 100%;
    margin-top: 10px;
}
.slider-label {
    font-weight: 600;
    margin-top: 10px;
    display: block;
}
.rating-display {
    margin-top: 10px;
    font-weight: 600;
}

.select {
    width: 100%;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #bbb;
    font-size: 1rem;
}

.circle-container {
    width: 240px;
    height: 240px;
    margin: 40px auto 10px auto;
    display: flex;
    justify-content: center;
    align-items: center;
}

.circle-container.small {
    width: 160px;
    height: 160px;
}

.breath-circle {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    transition: transform 1s ease-in-out, background 0.5s ease-in-out;
}

/* LUNG BEHAVIOR */
.inhale { transform: scale(1.35); }
.exhale { transform: scale(0.7); }
.hold  { transform: scale(1.05); }

.phase-text {
    font-size: 1.3rem;
    margin-top: 35px;
    text-align: center;
    font-weight: 700;
}

.quick-calm-btn {
    position: fixed;
    right: 14px;
    bottom: 14px;
    padding: 12px 14px;
    background: #222;
    color: #fff;
    border-radius: 50px;
    border: none;
    font-size: 0.95rem;
}

.quick-calm-overlay {
    position: fixed;
    top: 0; left: 0; right:0; bottom:0;
    background: rgba(0,0,0,0.6);
    display: flex;
    justify-content:center;
    align-items:center;
}

.quick-modal {
    background: white;
    padding: 20px;
    width: 90%;
    max-width: 350px;
    border-radius: 20px;
}

.center {
    text-align: center;
}

@keyframes fadeIn {
    from { opacity:0; }
    to { opacity:1; }
}
</style>
