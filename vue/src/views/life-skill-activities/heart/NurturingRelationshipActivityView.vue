<template>
    <div class="container py-4" style="max-width: 480px;">
        <!-- STEP 0 – INTRO -->
        <div v-if="step === 0" class="text-center">
            <h2 class="fw-bold mb-3">3-Minute Connect</h2>
            <p class="mb-4">
                Small acts can make a big difference.  
                Let's strengthen a relationship today.
            </p>

            <button class="btn btn-primary btn-lg rounded-pill px-4"
                    @click="step = 1">
                Start Connecting
            </button>
        </div>

        <!-- STEP 1 – CHOOSE WHO -->
        <div v-if="step === 1" class="text-center">
            <h4 class="fw-bold mb-3">Who do you want to connect with?</h4>

            <div class="d-grid gap-3 mt-4">
                <button class="btn btn-outline-primary rounded-pill py-3"
                        @click="chooseType('Family')">Family</button>

                <button class="btn btn-outline-primary rounded-pill py-3"
                        @click="chooseType('Friend')">Friend</button>

                <button class="btn btn-outline-primary rounded-pill py-3"
                        @click="chooseType('Colleague')">Colleague</button>
            </div>
        </div>

        <!-- STEP 2 – GET PROMPT -->
        <div v-if="step === 2" class="text-center">
            <h4 class="fw-bold mb-3">Connection Task</h4>

            <div class="border rounded p-3 bg-light mb-3">
                {{ currentPrompt }}
            </div>

            <button class="btn btn-secondary rounded-pill mb-4"
                    @click="getAnotherPrompt">
                Show Another
            </button>

            <button class="btn btn-primary rounded-pill mb-4 mx-2"
                    @click="step = 3">
                I Did This
            </button>
        </div>

        <!-- STEP 3 – LOG EXPERIENCE -->
        <div v-if="step === 3">
            <h4 class="fw-bold text-center mb-3">How did it go?</h4>

            <div class="d-flex justify-content-center gap-3 mb-3 fs-3">
                <span @click="mood = 'good'"
                      :class="moodClass('good')">😊</span>

                <span @click="mood = 'okay'"
                      :class="moodClass('okay')">😐</span>

                <span @click="mood = 'bad'"
                      :class="moodClass('bad')">☹️</span>
            </div>

            <textarea class="form-control mb-3 rounded"
                      rows="3"
                      placeholder="What happened?"
                      v-model="logText"></textarea>

            <textarea class="form-control mb-4 rounded"
                      rows="3"
                      placeholder="Optional: How did it make you feel?"
                      v-model="journalText"></textarea>

            <button class="btn btn-primary w-100 rounded-pill py-2"
                    @click="saveEntry">
                Save Entry
            </button>
        </div>

        <!-- STEP 4 – ENCOURAGEMENT SCREEN -->
        <div v-if="step === 4" class="text-center">
            <h3 class="fw-bold mb-3">Nice Work!</h3>
            <p class="mb-4">
                Small steps build strong relationships.  
                Keep the momentum!
            </p>

            <button class="btn btn-primary rounded-pill px-4"
                    @click="$router.back()">
                Done
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const step = ref(0);
const selection = ref(null);
const currentPrompt = ref("");

const mood = ref(null);
const logText = ref("");
const journalText = ref("");

// PROMPT LIBRARY
const promptLibrary = {
    Family: [
        "Ask how their day went.",
        "Tell them you're proud of them.",
        "Send a quick 'thinking of you' message.",
        "Share one nice memory from childhood."
    ],
    Friend: [
        "Send them a funny message.",
        "Ask what they’re watching lately.",
        "Invite them for a coffee or chat.",
        "Send them an old photo you found."
    ],
    Colleague: [
        "Thank them for their help today.",
        "Compliment their teamwork.",
        "Share a small win from work.",
        "Ask them how they're doing outside work."
    ]
};

function chooseType(type) {
    selection.value = type;
    getAnotherPrompt();
    step.value = 2;
}

function getAnotherPrompt() {
    const list = promptLibrary[selection.value];
    currentPrompt.value = list[Math.floor(Math.random() * list.length)];
}

function saveEntry() {
    const entry = {
        type: selection.value,
        prompt: currentPrompt.value,
        mood: mood.value,
        log: logText.value,
        journal: journalText.value,
        timestamp: new Date().toISOString()
    };

    // Save locally (you can replace with API)
    let stored = JSON.parse(localStorage.getItem("three_minute_connect") || "[]");
    stored.push(entry);
    localStorage.setItem("three_minute_connect", JSON.stringify(stored));

    step.value = 4;
}

// Emoji selection styles
function moodClass(key) {
    return key === mood.value ? "border border-primary rounded-circle p-1" : "opacity-50";
}
</script>

<style scoped>
span {
    cursor: pointer;
}
</style>
