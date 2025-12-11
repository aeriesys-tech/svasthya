<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center fw-bold">Effective Communication Activities</h1>

        <div class="accordion" id="effectiveCommunicationAccordion">
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

/* ------------------------------------------------------
   EFFECTIVE COMMUNICATION ACTIVITIES
-------------------------------------------------------*/
const activities = ref([
    {
        title: "Pause Before Speak",
        description: "Think of your last conversation.",
        fields: [
            { label: "Did I pause before replying?", value: "" },
            { label: "Did it change how I spoke?", value: "" },
        ],
        reflection: "⭐ 1–5: Did pausing help me respond better?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds mindful speech control.",
    },
    {
        title: "Listen Without Plan",
        description: "During your next talk, just listen — don’t plan your reply.",
        fields: [
            { label: "What did I notice differently?", value: "" },
        ],
        reflection: "⭐ 1–5: Did listening fully make me understand better?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Encourages presence and active listening.",
    },
    {
        title: "One Positive Line",
        description: "Think of someone you spoke to today.",
        fields: [
            { label: "One kind or encouraging thing I could have said:", value: "" },
        ],
        reflection: "⭐ 1–5: Did thinking kindly improve my mood?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds positive communication habits.",
    },
    {
        title: "I-Message Practice",
        description: "Instead of “You make me angry,” say “I feel upset when…” Write your own example:",
        fields: [
            { label: "I feel ______ when ______.", value: "" },
        ],
        reflection: "⭐ 1–5: Did expressing feelings calmly make sense?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Teaches assertiveness without aggression.",
    },
    {
        title: "Silent Signal",
        description: "In one interaction, notice your tone, face, or gesture.",
        fields: [
            { label: "Did my body language match my words?", value: "" },
        ],
        reflection: "⭐ 1–5: Did awareness of signals help me connect better?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Improves non-verbal awareness.",
    },
    {
        title: "Repeat to Confirm",
        description: "When someone speaks, repeat what you understood: “So you mean…”",
        fields: [
            { label: "How did the other person react?", value: "" },
        ],
        reflection: "⭐ 1–5: Did repeating avoid confusion?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Enhances clarity and listening accuracy.",
    },
    {
        title: "Small Talk Moment",
        description: "Speak kindly to a colleague, guard, or vendor today.",
        fields: [
            { label: "What did I say?", value: "" },
            { label: "How did they respond?", value: "" },
        ],
        reflection: "⭐ 1–5: Did this small chat lift my day?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds confidence through micro-interactions.",
    },
    {
        title: "Respectful Disagree",
        description: "Think of a time you disagreed recently.",
        fields: [
            { label: "Did I raise my voice or explain calmly?", value: "" },
            { label: "How did it end?", value: "" },
        ],
        reflection: "⭐ 1–5: Did calm disagreement maintain respect?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Encourages emotional maturity in conflict.",
    },
    {
        title: "Mirror Moment",
        description: "Stand in front of a mirror and say a difficult sentence politely. “I need help with…” or “I disagree because…”",
        fields: [],
        reflection: "⭐ 1–5: Did saying it calmly make me confident?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds verbal composure and tone control.",
    },
    {
        title: "Appreciation Habit",
        description: "End your day by thanking one person (in message or thought).",
        fields: [
            { label: "Whom did I thank?", value: "" },
            { label: "How did I feel afterward?", value: "" },
        ],
        reflection: "⭐ 1–5: Did gratitude make me feel connected?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Strengthens empathy and social warmth.",
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
