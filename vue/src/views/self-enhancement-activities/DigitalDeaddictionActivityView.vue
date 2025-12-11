<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center fw-bold">Digital De-Addiction Module</h1>

        <div class="accordion" id="digitalAccordion">
            <div
                class="accordion-item mb-3"
                v-for="(activity, index) in activities"
                :key="index"
            >
                <h2 class="accordion-header">
                    <button
                        class="accordion-button fw-semibold"
                        type="button"
                        :class="{ collapsed: activeIndex !== index }"
                        @click="toggleActivity(index)"
                    >
                        {{ index + 1 }}. {{ activity.title }}
                    </button>
                </h2>

                <div
                    class="accordion-collapse collapse"
                    :class="{ show: activeIndex === index }"
                >
                    <div class="accordion-body">

                        <!-- Description -->
                        <p class="mb-3 text-muted">{{ activity.description }}</p>

                        <!-- Input Fields -->
                        <div v-for="(field, fIndex) in activity.fields" :key="fIndex" class="mb-3">
                            <label class="form-label fw-medium text-dark">{{ field.label }}</label>
                            <input
                                type="text"
                                v-model="field.value"
                                class="form-control"
                                placeholder="Write here..."
                            />
                        </div>

                        <!-- Reflection Question -->
                        <div class="mb-3">
                            <p class="mb-2 fw-semibold"><em>{{ activity.reflection }}</em></p>
                        </div>

                        <!-- Reflection Star Rating -->
                        <div class="feedback-stars text-center mb-3">
                            <h6 class="text-dark mb-2">⭐ Reflection Rating (1–5)</h6>
                            <div class="stars d-inline-block">
                                <i
                                    v-for="n in 5"
                                    :key="'reflect-' + index + '-' + n"
                                    class="bi star-icon"
                                    :class="n <= activity.reflectionRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                    @click="setRating(activity, 'reflectionRating', n)"
                                    role="button"
                                ></i>
                            </div>
                            <div class="small text-muted mt-1">
                                Your rating: {{ activity.reflectionRating }} / 5
                            </div>
                        </div>

                        <!-- Purpose -->
                        <h6 class="fw-bold mt-3">🎯 Purpose:</h6>
                        <p class="text-muted">{{ activity.purpose }}</p>

                        <!-- Activity Rating -->
                        <div class="feedback-stars text-center mt-3 mb-3">
                            <h6 class="text-dark mb-2">⭐ Activity Rating (1–5)</h6>
                            <div class="stars d-inline-block">
                                <i
                                    v-for="n in 5"
                                    :key="'activity-' + index + '-' + n"
                                    class="bi star-icon"
                                    :class="n <= activity.activityRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                    @click="setRating(activity, 'activityRating', n)"
                                    role="button"
                                ></i>
                            </div>
                            <div class="small text-muted mt-1">
                                Your rating: {{ activity.activityRating }} / 5
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-success px-4" @click="completeActivity(activity, index)">
                                Complete Activity
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <button class="btn btn-secondary px-4" @click="goBack">
                ← Back to Activities
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeIndex = ref(null)

const toggleActivity = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index
}

const setRating = (activity, key, n) => {
    activity[key] = n
}

const completeActivity = (activity, index) => {
    alert(`✅ Completed: ${activity.title}
Reflection Rating: ${activity.reflectionRating}/5
Activity Rating: ${activity.activityRating}/5`)
    if (activeIndex.value === index) activeIndex.value = null
}

const goBack = () => {
    router.push('/self-enhancement')
}

/* ----------------------------------------
   DIGITAL DE-ADDICTION ACTIVITIES
-----------------------------------------*/
const activities = ref([
    {
        title: "Digital Mirror (Self-Check Tracker)",
        description: "Every evening, check your digital mirror.",
        fields: [
            { label: "Screen time today (hours):", value: "" },
            { label: "Productive time (work/study) (hours):", value: "" },
            { label: "Scrolling without purpose (minutes):", value: "" },
            { label: "One thing I could do instead tomorrow:", value: "" },
        ],
        reflection: "⭐ 1–5: Did tracking help me feel more in control today?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds self-awareness about time spent vs. time used.",
    },
    {
        title: "The 20-Minute Off Rule",
        description: "Choose one slot today — 20 minutes without your phone.",
        fields: [
            { label: "When did I do it?", value: "" },
            { label: "What did I do instead?", value: "" },
            { label: "How did I feel after?", value: "" },
        ],
        reflection: "⭐ 1–5: Did this phone-free break refresh my mind?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds gradual digital detachment tolerance.",
    },
    {
        title: "Screen–to–Scene Swap",
        description: "Replace one scrolling session with a real-world act.",
        fields: [
            { label: "Instead of scrolling, I chose to:", value: "" },
            { label: "Mood before:", value: "" },
            { label: "Mood after:", value: "" },
        ],
        reflection: "⭐ 1–5: Did real-world activity lift my energy?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Replaces passive dopamine triggers with active satisfaction.",
    },
    {
        title: "Silent Start, Calm End",
        description: "Keep your phone away for the first 30 minutes after waking and last 30 minutes before sleep.",
        fields: [
            { label: "Morning: Did I manage it? (Yes / No)", value: "" },
            { label: "Night: Did I manage it? (Yes / No)", value: "" },
            { label: "Quality of focus/sleep (1–5):", value: "" },
        ],
        reflection: "⭐ 1–5: Did this boundary improve my calmness?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Restores mental space and circadian rhythm.",
    },
    {
        title: "Digital Detox Daylight",
        description: "Pick one half-day in a week — keep notifications off.",
        fields: [
            { label: "When did I do it?", value: "" },
            { label: "How did people react?", value: "" },
            { label: "What did I learn about myself?", value: "" },
        ],
        reflection: "⭐ 1–5: Did this mini-detox make me feel peaceful?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Reinforces voluntary control and real-world engagement.",
    },
    {
        title: "Scroll Swap Challenge",
        description: "Every time you feel like checking your phone for no reason, pause and ask:",
        fields: [
            { label: "What emotion am I feeling right now? (Bored / Lonely / Curious / Anxious)", value: "" },
            { label: "What else can I do for 2 minutes instead?", value: "" },
            { label: "Did that reduce my urge? (Yes / No)", value: "" },
        ],
        reflection: "⭐ 1–5: Did this small swap help me control the impulse?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds impulse awareness and emotional substitution.",
    },
    {
        title: "App Energy Audit",
        description: "Check the top 3 apps you used today.",
        fields: [
            { label: "1️⃣ App – Energized / Drained", value: "" },
            { label: "2️⃣ App – Energized / Drained", value: "" },
            { label: "3️⃣ App – Energized / Drained", value: "" },
            { label: "One app I’ll limit tomorrow:", value: "" },
        ],
        reflection: "⭐ 1–5: Did identifying draining apps motivate change?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds cognitive awareness of digital energy flow.",
    },
    {
        title: "Real Connection Time",
        description: "Call or meet someone face-to-face instead of texting today.",
        fields: [
            { label: "Who did I connect with?", value: "" },
            { label: "Duration (minutes):", value: "" },
            { label: "How did it feel compared to chatting online?", value: "" },
        ],
        reflection: "⭐ 1–5: Did real interaction feel more satisfying?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Reconnects social reward system with real-world bonding.",
    },
    {
        title: "One-Tab Focus Rule",
        description: "While working or reading online — keep only one tab or app open.",
        fields: [
            { label: "Did I manage single-tasking? (Yes / No)", value: "" },
            { label: "Focus level (1–5):", value: "" },
            { label: "Distraction attempts noticed:", value: "" },
        ],
        reflection: "⭐ 1–5: Did single-tasking improve my focus?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Reduces cognitive overload and improves concentration.",
    },
    {
        title: "Sunday Digital Sunset",
        description: "Pick one evening a week — switch off devices one hour before sunset.",
        fields: [
            { label: "What did I do instead?", value: "" },
            { label: "Did I miss the phone? (Yes / No)", value: "" },
            { label: "What did I notice in my surroundings?", value: "" },
        ],
        reflection: "⭐ 1–5: Did this sunset break make me feel peaceful?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Encourages tech-free relaxation ritual to reset dopamine cycle.",
    },
]);
</script>

<style scoped>
h1 {
    font-weight: 700;
}
.accordion-button {
    background-color: #f8f9fa;
}
.accordion-button:not(.collapsed) {
    background-color: #e9ecef;
    color: #000;
}
.stars {
    display: inline-block;
}
.star-icon {
    font-size: 1.8rem;
    cursor: pointer;
    transition: transform 0.15s ease;
    margin: 0 4px;
}
.star-icon:hover {
    transform: scale(1.15);
}
.bi-star-fill {
    transform: scale(1.12);
}
.feedback-stars {
    text-align: center;
}
.form-control {
    max-width: 700px;
}
</style>
