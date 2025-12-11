<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center fw-bold">Problem Solving</h1>

        <div class="accordion" id="problemAccordion">
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
   PROBLEM-SOLVING ACTIVITIES
-----------------------------------------*/
const activities = ref([
    {
        title: "Name It, Don’t Blame It",
        description: "Think of one small issue from today.",
        fields: [
            { label: "What exactly happened?", value: "" },
            { label: "Who or what is involved?", value: "" },
            { label: "What part of it can I control?", value: "" },
        ],
        reflection: "⭐ Did identifying it clearly make me feel lighter?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Clarity reduces emotional noise.",
    },
    {
        title: "Pause–Plan–Proceed",
        description: "Before reacting, follow these 3 steps.",
        fields: [
            { label: "Pause – one deep breath taken?", value: "" },
            { label: "Plan – two options I thought of:", value: "" },
            { label: "Proceed – what did I choose calmly?", value: "" },
        ],
        reflection: "⭐ Did this three-step plan make the situation easier?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds impulse control and structured thinking.",
    },
    {
        title: "Break It Down",
        description: "Big problem? Split it.",
        fields: [
            { label: "What’s the big issue?", value: "" },
            { label: "What’s the first small thing I can fix today?", value: "" },
        ],
        reflection: "⭐ Did solving a small part make me feel progress?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Reduces overwhelm.",
    },
    {
        title: "The 3 Doors",
        description: "Imagine three doors for your challenge.",
        fields: [
            { label: "Which door would I choose today? (Quick Fix / Long-term / Let It Go)", value: "" },
        ],
        reflection: "⭐ Did this choice bring clarity?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Encourages flexible problem-solving.",
    },
    {
        title: "Ask for One Help",
        description: "When stuck, think:",
        fields: [
            { label: "Who can give me one small advice?", value: "" },
            { label: "What’s the best way to ask for it?", value: "" },
        ],
        reflection: "⭐ Did reaching out make me feel supported?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Normalizes seeking help and builds teamwork.",
    },
    {
        title: "Flip the Frame",
        description: "Take one problem and reframe it.",
        fields: [
            { label: "One small good thing about this situation:", value: "" },
            { label: "How could this challenge be teaching me something?", value: "" },
        ],
        reflection: "⭐ Did I feel calmer after reframing it?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Teaches positive cognitive reappraisal.",
    },
    {
        title: "Try–Test–Tweak",
        description: "Experimentation helps.",
        fields: [
            { label: "The step I tried:", value: "" },
            { label: "What worked:", value: "" },
            { label: "What I’ll tweak next time:", value: "" },
        ],
        reflection: "⭐ Did experimentation reduce stress?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds adaptive problem-solving.",
    },
    {
        title: "The 10-Minute Rule",
        description: "When overwhelmed, give yourself 10 minutes.",
        fields: [
            { label: "What can I do in 10 minutes to make this better?", value: "" },
        ],
        reflection: "⭐ Did this small action reduce my worry?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Converts thinking into immediate action.",
    },
    {
        title: "Past Wins Reminder",
        description: "Recall a problem you solved earlier.",
        fields: [
            { label: "What helped me then?", value: "" },
            { label: "Can I apply that now?", value: "" },
        ],
        reflection: "⭐ Did recalling past strength increase confidence?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Reinforces resilience and self-trust.",
    },
    {
        title: "The Ripple Review",
        description: "After solving something today:",
        fields: [
            { label: "What changed for me?", value: "" },
            { label: "How did it affect others?", value: "" },
            { label: "What will I remember next time?", value: "" },
        ],
        reflection: "⭐ Did reflection make me feel more capable?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Encourages learning from outcomes.",
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
