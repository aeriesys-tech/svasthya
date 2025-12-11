<template>
    <div class="container py-5">
        <h1 class="text-center fw-bold mb-4">Quick Exercise Break</h1>

        <!-- Step 1: Start -->
        <div v-if="step === 1" class="text-center fade-in">
            <p class="lead mb-4">
                Take a 5-minute break to reset your body and brain. Let's move.
            </p>
            <button class="btn btn-primary btn-lg px-5" @click="step = 2">
                Start Exercise Break
            </button>
        </div>

        <!-- Step 2: Select Routine -->
        <div v-if="step === 2" class="fade-in">
            <h3 class="fw-bold mb-3">Select a Routine</h3>

            <div class="routine-card"
                v-for="routine in routines"
                :key="routine.id"
                @click="selectRoutine(routine)"
            >
                <h5 class="fw-bold">{{ routine.title }}</h5>
                <p class="mb-1">{{ routine.description }}</p>
                <small class="text-muted">{{ routine.time }}</small>
            </div>
        </div>

        <!-- Step 3: Guided Video -->
        <div v-if="step === 3" class="fade-in">
            <h3 class="fw-bold mb-3">{{ selectedRoutine.title }}</h3>

            <div class="video-container mb-3">
                <iframe 
                    :src="selectedRoutine.video"
                    frameborder="0"
                    allowfullscreen
                ></iframe>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-3">
                <button class="btn btn-secondary" @click="toggleSound">
                    {{ soundOn ? 'Sound: On' : 'Sound: Off' }}
                </button>
                <button class="btn btn-warning" @click="toggleLock">
                    {{ phoneLocked ? 'Unlock Screen' : 'Lock Screen' }}
                </button>
            </div>

            <button class="btn btn-success w-100 py-2 fw-bold"
                @click="finishRoutine">
                Finish Session
            </button>
        </div>

        <!-- Step 4: Reflection -->
        <div v-if="step === 4" class="fade-in">
            <h3 class="fw-bold mb-3">How are you feeling now?</h3>

            <div class="mood-options">
                <button
                    v-for="mood in moods"
                    :key="mood"
                    class="btn btn-outline-primary w-100 mb-2"
                    :class="{ active: selectedMood === mood }"
                    @click="selectedMood = mood"
                >
                    {{ mood }}
                </button>
            </div>

            <textarea
                class="form-control mt-3"
                placeholder="Any notes about your session?"
                rows="3"
                v-model="reflectionText"
            ></textarea>

            <button 
                class="btn btn-success w-100 mt-3 py-2 fw-bold"
                @click="saveSession">
                Save Session
            </button>
        </div>

        <!-- Step 5: Saved -->
        <div v-if="step === 5" class="text-center fade-in">
            <h3 class="fw-bold mb-3">Session Saved!</h3>
            <p class="text-muted">Great work — keep moving!</p>

            <button class="btn btn-primary mt-3" @click="resetActivity">
                Do Another Routine
            </button>

            <button class="btn btn-outline-secondary mt-3 ms-2"
                @click="goHome">
                Back to activities
            </button>
        </div>

        <!-- Quick Move Floating Button -->
        <!-- <button class="quick-move-btn" @click="startQuickMove">
            Quick Move
        </button> -->

        <!-- Quick Move Modal -->
        <!-- <div 
            class="quickmove-overlay" 
            v-if="quickMoveActive"
        >
            <div class="quickmove-box">
                <h3 class="fw-bold mb-3 text-center">2-Minute Quick Move</h3>
                <p class="text-center">{{ quickMoveInstruction }}</p>

                <button
                    class="btn btn-danger w-100 mt-3"
                    @click="endQuickMove"
                >
                    End Quick Move
                </button>
            </div>
        </div> -->
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

/* ----------------------------------------------------------
   Activity State
----------------------------------------------------------- */
const step = ref(1);
const selectedRoutine = ref(null);
const selectedMood = ref(null);
const reflectionText = ref("");

/* ----------------------------------------------------------
   Routines
----------------------------------------------------------- */
const routines = [
    {
        id: 1,
        title: "Stretch & Mobility (5 min)",
        description: "Neck rolls, shoulder stretches, light movements.",
        time: "5 minutes • Easy",
        video: "https://www.youtube.com/embed/ippvY7yJ5uA"
    },
    {
        id: 2,
        title: "Cardio Burst (10 min)",
        description: "High-energy cardio burst for quick activation.",
        time: "10 minutes • Moderate",
        video: "https://www.youtube.com/embed/cZnsLVArIt8"
    },
    {
        id: 3,
        title: "Movement Reset (7 min)",
        description: "Full-body stretch & mobility routine.",
        time: "7 minutes • Easy",
        video: "https://www.youtube.com/embed/UItWltVZZmE"
    },
    {
        id: 4,
        title: "Chair-Friendly Routine",
        description: "Seated routine you can do anywhere.",
        time: "5 minutes • Very Easy",
        video: "https://www.youtube.com/embed/2ODW7drIol0"
    }
];

/* ----------------------------------------------------------
   Sound + Screen Lock
----------------------------------------------------------- */
const soundOn = ref(true);
const phoneLocked = ref(false);

function toggleSound() {
    soundOn.value = !soundOn.value;
}

function toggleLock() {
    phoneLocked.value = !phoneLocked.value;
}

/* ----------------------------------------------------------
   Routine Selection
----------------------------------------------------------- */
function selectRoutine(routine) {
    selectedRoutine.value = routine;
    step.value = 3;
}

/* ----------------------------------------------------------
   Finish Routine
----------------------------------------------------------- */
function finishRoutine() {
    step.value = 4;
}

/* ----------------------------------------------------------
   Reflection
----------------------------------------------------------- */
const moods = ["Energized", "Calmer", "No change", "Tired but glad I moved"];

function saveSession() {
    const log = JSON.parse(localStorage.getItem("exercise_log") || "[]");

    log.push({
        routine: selectedRoutine.value.title,
        time: new Date().toLocaleString(),
        mood: selectedMood.value,
        note: reflectionText.value
    });

    localStorage.setItem("exercise_log", JSON.stringify(log));

    step.value = 5;
}

function resetActivity() {
    step.value = 1;
    selectedMood.value = null;
    selectedRoutine.value = null;
    reflectionText.value = "";
}

/* ----------------------------------------------------------
   Quick Move (2-minute burst)
----------------------------------------------------------- */
const quickMoveActive = ref(false);
const quickMoveInstruction = ref("");

const quickMoves = [
    "Step in place for 30 seconds.",
    "Do 15 shoulder rolls.",
    "Do 10 slow neck rotations.",
    "Open chest stretch for 20 seconds.",
    "Seated knee lifts for 30 seconds."
];

function startQuickMove() {
    quickMoveActive.value = true;
    quickMoveInstruction.value = quickMoves[Math.floor(Math.random() * quickMoves.length)];
}

function endQuickMove() {
    quickMoveActive.value = false;
}

function goHome() {
    router.push("/activity/life-skills/health");
}
</script>

<style scoped>
.fade-in {
    animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.routine-card {
    padding: 15px;
    border: 1px solid #dadada;
    border-radius: 12px;
    margin-bottom: 15px;
    transition: 0.2s;
    cursor: pointer;
}
.routine-card:hover {
    background: #f5f9ff;
    transform: translateY(-2px);
}

.video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
}
.video-container iframe {
    position: absolute;
    width: 100%;
    height: 100%;
}

/* .quick-move-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    background: #d80621;
    color: white;
    border: none;
    padding: 12px 18px;
    border-radius: 50px;
    font-size: 16px;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.25);
    z-index: 999;
}

.quickmove-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.quickmove-box {
    background: white;
    padding: 25px;
    width: 90%;
    max-width: 380px;
    border-radius: 20px;
    animation: fadeIn 0.4s ease;
} */
</style>
