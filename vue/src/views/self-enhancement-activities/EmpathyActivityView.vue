<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center fw-bold">Empathy Activities</h1>

        <div class="accordion" id="empathyAccordion">
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

                        <!-- Input Fields (exact labels preserved) -->
                        <div v-for="(field, fIndex) in activity.fields" :key="fIndex" class="mb-3">
                            <label class="form-label fw-medium text-dark">{{ field.label }}</label>
                            <input
                                type="text"
                                v-model="field.value"
                                class="form-control"
                                placeholder="Write here..."
                            />
                        </div>

                        <!-- Reflection Question (explicit) -->
                        <div class="mb-3">
                            <!-- <h6 class="fw-bold mb-2 text-primary">Reflection Question:</h6> -->
                            <p class="mb-2 fw-semibold"><em>{{ activity.reflection }}</em></p>
                        </div>

                        <!-- Reflection Star Rating (centered) -->
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
                                    aria-label="reflection star"
                                ></i>
                            </div>
                            <div class="small text-muted mt-1">Your rating: {{ activity.reflectionRating }} / 5</div>
                        </div>

                        <!-- Purpose / Focus -->
                        <h6 class="fw-bold mt-3">🎯 Focus:</h6>
                        <p class="text-muted">{{ activity.focus }}</p>

                        <!-- Activity Star Rating (centered) -->
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
                                    aria-label="activity star"
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeIndex = ref(null)

const toggleActivity = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index
}

const setRating = (activity, key, n) => {
    activity[key] = n
    saveLocal()
}

const completeActivity = (activity, index) => {
    alert(`✅ Completed: ${activity.title}
Reflection Rating: ${activity.reflectionRating}/5
Activity Rating: ${activity.activityRating}/5`)
    if (activeIndex.value === index) activeIndex.value = null
    saveLocal()
}

const goBack = () => {
    router.push('/activity')
}

// localStorage save/load (optional but helpful)
const saveLocal = () => {
    // localStorage.setItem('empathyModuleActivities', JSON.stringify(activities.value))
}

onMounted(() => {
    const saved = localStorage.getItem('empathyModuleActivities')
    if (saved) {
        activities.value = JSON.parse(saved)
    }
})
// ----------------------------
// ACTIVITIES DATA (reflection text included)
// ----------------------------
const activities = ref([
    {
        title: "One-Minute Perspective",
        description: "Think of someone you interacted with today.",
        fields: [
            { label: "What did they say or do?", value: "" },
            { label: "If you were in their place, how might you feel?", value: "" },
            { label: "What could they have needed at that moment?", value: "" },
        ],
        reflection: "⭐ How connected do you feel to them now?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Perspective-taking in daily interactions.",
    },
    {
        title: "The Waiting Line",
        description: "Imagine waiting in a long queue and someone cuts in front. Before reacting, ask: “What could be their reason?”",
        fields: [
            { label: "My first thought was:", value: "" },
            { label: "A possible reason they did that:", value: "" },
            { label: "I choose to feel instead.", value: "" },
        ],
        reflection: "⭐ How much calmer do I feel after this view?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Regulating judgment and practising understanding.",
    },
    {
        title: "Listen Without Reply",
        description: "Spend 5 minutes listening to someone without interrupting or advising.",
        fields: [
            { label: "What did I learn about them?", value: "" },
            { label: "What did I feel while listening?", value: "" },
        ],
        reflection: "⭐ How well did I understand them?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Active listening and non-defensive presence.",
    },
    {
        title: "Small Kindness",
        description: "Do one simple act of kindness today — hold a door, share a smile, or check on someone.",
        fields: [
            { label: "What did I do?", value: "" },
            { label: "How did they respond?", value: "" },
            { label: "What did I feel seeing that?", value: "" },
        ],
        reflection: "⭐ Did this small act make me feel connected?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Empathy through small behavioral gestures.",
    },
    {
        title: "The Opposite View",
        description: "Think of a recent disagreement.",
        fields: [
            { label: "My view was:", value: "" },
            { label: "Their view might be:", value: "" },
            { label: "One valid point in their view is:", value: "" },
        ],
        reflection: "⭐ Do I understand their side better now?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Cognitive empathy — acknowledging another’s logic.",
    },
    {
        title: "Unspoken Story",
        description: "Notice someone (colleague, friend, vendor).",
        fields: [
            { label: "What emotion might they be feeling today?", value: "" },
            { label: "What could have led to that?", value: "" },
            { label: "If I could say one kind line to them, it would be:", value: "" },
        ],
        reflection: "⭐ How compassionate did I feel just imagining that?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Emotional imagination, quiet empathy.",
    },
    {
        title: "Empathy through Memory",
        description: "Recall a time when you felt misunderstood.",
        fields: [
            { label: "How did that feel?", value: "" },
            { label: "What did you need then?", value: "" },
            { label: "Can you offer that understanding to someone today?", value: "" },
        ],
        reflection: "⭐ Did this reflection open my heart?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Using past experience to generate current empathy.",
    },
    {
        title: "See the Helper",
        description: "When you see someone doing their job (driver, cleaner, staff), pause and notice:",
        fields: [
            { label: "What effort do they put in daily?", value: "" },
            { label: "What might make their day easier?", value: "" },
        ],
        reflection: "⭐ How much respect did I feel for their work?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Empathy through gratitude and social awareness.",
    },
    {
        title: "Emotion Mirror",
        description: "When you talk to someone, observe their face or tone.",
        fields: [
            { label: "What emotion did I notice?", value: "" },
            { label: "Did my body react (smile, frown, soften)?", value: "" },
        ],
        reflection: "⭐ How in tune did I feel with their mood?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Non-verbal emotional attunement.",
    },
    {
        title: "Shared Humanity",
        description: "Read or hear a story of someone facing difficulty.",
        fields: [
            { label: "What did they go through?", value: "" },
            { label: "What part of their story felt familiar to me?", value: "" },
            { label: "What would I want if I were in their place?", value: "" },
        ],
        reflection: "⭐ Did I feel emotionally closer to others after this?",
        reflectionRating: 0,
        activityRating: 0,
        focus: "Empathy for unseen lives and human connection.",
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

/* reuse resilience star styles (Bootstrap Icons) */
.stars {
    display: inline-block;
}

.star-icon {
    font-size: 1.8rem;
    cursor: pointer;
    transition: transform 0.15s ease, color 0.15s ease;
    color: #6c757d;
    margin: 0 4px;
}

.star-icon:hover {
    transform: scale(1.15);
}

/* filled star color */
.text-warning {
    color: #ffbf00 !important;
}

.bi-star-fill {
    transform: scale(1.12);
}

/* center alignment helpers */
.feedback-stars {
    text-align: center;
}

/* small inputs */
.form-control {
    max-width: 700px;
}
</style>
