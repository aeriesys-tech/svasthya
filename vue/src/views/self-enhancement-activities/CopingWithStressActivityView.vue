<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center fw-bold">Coping With Stress Activities</h1>

        <div class="accordion" id="stressAccordion">
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
                            <div class="small text-muted mt-1">Your rating: {{ activity.reflectionRating }} / 5</div>
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
                            <div class="small text-muted mt-1">Your rating: {{ activity.activityRating }} / 5</div>
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
   COPING WITH STRESS ACTIVITIES
-----------------------------------------*/
const activities = ref([
    {
        title: "Pause Clock",
        description: "Set a reminder three times a day — just pause for 30 seconds.",
        fields: [
            { label: "What was I thinking right now?", value: "" },
            { label: "How did this pause feel?", value: "" },
        ],
        reflection: "⭐ 1–5: Did a pause reduce mental rush?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds micro-break awareness.",
    },
    {
        title: "The “Can I Control It?” Test",
        description: "Write your current worry.",
        fields: [
            { label: "Write your current worry:", value: "" },
            { label: "Can I control it? (Yes / No / Partly)", value: "" },
            { label: "If yes — one step I’ll take:", value: "" },
        ],
        reflection: "⭐ 1–5: Did this clarity reduce pressure?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Reduces helplessness through clarity.",
    },
    {
        title: "Stress Thermometer",
        description: "Rate your stress right now (1–10).",
        fields: [
            { label: "Rate your stress (1–10):", value: "" },
            { label: "What’s one small action to lower it by 1 point?", value: "" },
        ],
        reflection: "⭐ 1–5: Did this action work?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Encourages measurable stress control.",
    },
    {
        title: "Move a Little",
        description: "When feeling tense, move — walk, stretch, shake your hands.",
        fields: [
            { label: "What did I do?", value: "" },
            { label: "After movement, I felt:", value: "" },
        ],
        reflection: "⭐ 1–5: Did physical movement change my mood?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Connects mind–body stress relief.",
    },
    {
        title: "5–4–3–2–1 Grounding",
        description: "When anxious, name the following grounding elements.",
        fields: [
            { label: "5 things I see:", value: "" },
            { label: "4 things I touch:", value: "" },
            { label: "3 things I hear:", value: "" },
            { label: "2 things I smell:", value: "" },
            { label: "1 thing I taste:", value: "" },
        ],
        reflection: "⭐ 1–5: Did this bring me to the present moment?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Reduces overthinking through sensory focus.",
    },
    {
        title: "Tension Release Breath",
        description: "Inhale deeply for 4 seconds → Hold for 2 → Exhale slowly for 6. Repeat 3 times.",
        fields: [
            { label: "After breathing, I felt:", value: "" },
        ],
        reflection: "⭐ 1–5: Did my body relax after breathing?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Activates calm response.",
    },
    {
        title: "Think Shift",
        description: "Write the stressful thought.",
        fields: [
            { label: "Write the stressful thought:", value: "" },
            { label: "What’s another way to see this?", value: "" },
        ],
        reflection: "⭐ 1–5: Did reframing ease the worry?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Promotes cognitive flexibility.",
    },
    {
        title: "Nature Minute",
        description: "Look outside — the sky, trees, birds — for one minute.",
        fields: [
            { label: "What did I notice?", value: "" },
        ],
        reflection: "⭐ 1–5: Did nature help me reset?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Simple sensory grounding.",
    },
    {
        title: "Energy Refill List",
        description: "Write two activities that recharge you.",
        fields: [
            { label: "1️⃣ Activity that recharges me:", value: "" },
            { label: "2️⃣ Activity that recharges me:", value: "" },
            { label: "Did I do at least one today?", value: "" },
        ],
        reflection: "⭐ 1–5: Did doing it lift my energy?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Encourages restorative self-care.",
    },
    {
        title: "Night Check-out",
        description: "Before bed, reflect on your day.",
        fields: [
            { label: "One thing I did well today:", value: "" },
            { label: "One thing I’ll let go of:", value: "" },
        ],
        reflection: "⭐ 1–5: Did this help me end the day peacefully?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Helps close the day with calm.",
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
