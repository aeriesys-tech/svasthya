<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center fw-bold">Decision-Making Activities</h1>

        <div class="accordion" id="decisionAccordion">
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

                        <!-- Description / prompt -->
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

                        <!-- Activity Star Rating -->
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
    router.push('/activity')
}

/* ----------------------------------------
   DECISION-MAKING ACTIVITIES (CLEAN DATA)
-----------------------------------------*/
const activities = ref([
    {
        title: "Weigh It Out",
        description: "Think of one choice you made today.",
        fields: [
            { label: "What were my options?", value: "" },
            { label: "What did I choose?", value: "" },
            { label: "Why did I choose that option?", value: "" },
        ],
        reflection: "⭐ Did I choose thoughtfully today?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds conscious decision-awareness.",
    },
    {
        title: "Peace or Pressure?",
        description: "Think of a recent decision.",
        fields: [
            { label: "Did I make it calmly or under pressure?", value: "" },
            { label: "How did I feel afterward?", value: "" },
        ],
        reflection: "⭐ Did calmness lead to a better result?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Differentiates between reactive and mindful choices.",
    },
    {
        title: "Ask the 3Qs",
        description: "Before deciding, pause and ask: Is it right? Is it kind? Is it useful?",
        fields: [
            { label: "Write what came to mind:", value: "" },
        ],
        reflection: "⭐ Did asking 3Qs make my choice wiser?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Encourages ethical and balanced decisions.",
    },
    {
        title: "Small Step First",
        description: "When facing a big decision:",
        fields: [
            { label: "What's one small safe step I can try first?", value: "" },
            { label: "What might I learn from that?", value: "" },
        ],
        reflection: "⭐ Did breaking it into steps make it easier?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Reduces decision paralysis.",
    },
    {
        title: "Regret or Relief",
        description: "Recall a past decision.",
        fields: [
            { label: "What did I feel after making it — regret or relief?", value: "" },
            { label: "What does that teach me about my values?", value: "" },
        ],
        reflection: "⭐ Did this reflection help me know myself better?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Uses past experience for better future choices.",
    },
    {
        title: "Head vs. Heart",
        description: "Before deciding, write:",
        fields: [
            { label: "What my mind says:", value: "" },
            { label: "What my heart says:", value: "" },
            { label: "Can both agree on a middle path?", value: "" },
        ],
        reflection: "⭐ Did balancing logic and feeling help?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Teaches emotional-rational balance.",
    },
    {
        title: "The Ripple Effect",
        description: "Before deciding, imagine:",
        fields: [
            { label: "How will this affect me?", value: "" },
            { label: "How will this affect others?", value: "" },
            { label: "Will it create peace or tension?", value: "" },
        ],
        reflection: "⭐ Did I consider the bigger picture?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Enhances social and ethical awareness.",
    },
    {
        title: "Deadline Decisions",
        description: "If you had to decide in 10 seconds:",
        fields: [
            { label: "What would you choose?", value: "" },
            { label: "If you had one day — would it be the same choice?", value: "" },
        ],
        reflection: "⭐ Did this show me the power of timing in decisions?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Builds time-awareness in decision quality.",
    },
    {
        title: "My Inner Yes / No",
        description: "Close your eyes and think of a decision.",
        fields: [
            { label: "When I imagine saying 'Yes', my body feels:", value: "" },
            { label: "When I imagine saying 'No', my body feels:", value: "" },
        ],
        reflection: "⭐ Did my body signal help clarity?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Trains intuitive awareness.",
    },
    {
        title: "The Lesson of the Day",
        description: "Recall one small decision you made today.",
        fields: [
            { label: "What happened?", value: "" },
            { label: "What did I learn from that choice?", value: "" },
        ],
        reflection: "⭐ Did today’s choice teach me something useful?",
        reflectionRating: 0,
        activityRating: 0,
        purpose: "Makes decision-learning continuous and natural.",
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
