<template>
    <div class="container py-4">
        <h2 class="text-center mb-4 fw-bold">Self Awareness Activities</h2>

        <div class="accordion" id="selfAwarenessAccordion">
            <div
                class="accordion-item mb-3"
                v-for="(activity, index) in activities"
                :key="index"
            >
                <h2 class="accordion-header" :id="`heading-${index}`">
                    <button
                        class="accordion-button fw-semibold collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        :data-bs-target="`#collapse-${index}`"
                        aria-expanded="false"
                        :aria-controls="`collapse-${index}`"
                    >
                        {{ activity.title }}
                    </button>
                </h2>
                <div
                    :id="`collapse-${index}`"
                    class="accordion-collapse collapse"
                    :aria-labelledby="`heading-${index}`"
                    data-bs-parent="#selfAwarenessAccordion"
                >
                    <div class="accordion-body">
                        <!-- Instruction -->
                        <p class="text-muted mb-3">{{ activity.instruction }}</p>

                        <!-- Dynamic Input Fields -->
                        <div
                            v-for="(field, fIndex) in activity.fields"
                            :key="fIndex"
                            class="mb-3"
                        >
                            <label class="form-label fw-semibold">{{
                                field.label
                            }}</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="field.value"
                                placeholder="Enter your response..."
                            />
                        </div>

                        <!-- ⭐ Reflection Question -->
                        <div class="mt-3">
                            <p class="fw-semibold mb-2">
                                ⭐ {{ activity.starQuestion }}
                            </p>
                        </div>

                        <!-- Star Rating -->
                        <div class="feedback-stars text-center mt-2">
                            <h6 class="text-dark mb-2">
                                Your Reflection Rating:
                            </h6>
                            <div class="stars">
                                <i
                                    v-for="star in 5"
                                    :key="star"
                                    class="bi"
                                    :class="
                                        star <= activity.clarityRating
                                            ? 'bi-star-fill text-warning'
                                            : 'bi-star text-secondary'
                                    "
                                    @click="activity.clarityRating = star"
                                    style="font-size: 1.8rem; cursor: pointer;"
                                ></i>
                            </div>
                            <div class="mt-2 small text-muted">
                                Rating: {{ activity.clarityRating }} / 5
                            </div>
                        </div>

                        <!-- 🎯 Purpose -->
                        <p class="mt-3 text-secondary">
                            🎯 <strong>Purpose:</strong> {{ activity.purpose }}
                        </p>

                        <!-- Activity Rating -->
                        <div class="feedback-stars text-center mt-4">
                            <h6 class="text-dark mb-2">Rate this Activity:</h6>
                            <div class="stars">
                                <i
                                    v-for="star in 5"
                                    :key="star"
                                    class="bi"
                                    :class="
                                        star <= activity.subRating
                                            ? 'bi-star-fill text-warning'
                                            : 'bi-star text-secondary'
                                    "
                                    @click="activity.subRating = star"
                                    style="font-size: 1.8rem; cursor: pointer;"
                                ></i>
                            </div>
                            <div class="mt-2 small text-muted">
                                Your rating: {{ activity.subRating }} / 5
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="text-center mt-4">
                            <button
                                class="btn btn-success px-4"
                                @click="submitActivity(activity, index)"
                            >
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <button class="btn btn-secondary" @click="goBack">
                ← Back to Activities
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick } from "vue";
import router from "../../router";

const activities = ref([
    {
        title: "Emotion Check-In - “What am I Feeling Right Now?”",
        instruction:
            "Imagine: You just completed a long shift or handled a stressful case. Pause and identify your current emotion and reflect why.",
        fields: [
            { label: "Current emotion (angry, proud, tired, calm, anxious)", value: "" },
            { label: "Why am I feeling this way?", value: "" },
            { label: "What triggered it?", value: "" },
        ],
        starQuestion: "Did I correctly name my emotion? (1–5)",
        purpose: "Builds daily emotional awareness and vocabulary.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Strength Spotting - “My 3 Best Qualities”",
        instruction:
            "Imagine: A junior officer seeks your guidance. Identify your three strongest qualities and recall where you used them.",
        fields: [
            { label: "My 3 strongest qualities", value: "" },
            { label: "Example situation for each quality", value: "" },
        ],
        starQuestion: "How confident am I about my strengths? (1–5)",
        purpose: "Increases self-confidence and clarity about personal assets.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Trigger Tracker - “When Do I Lose My Calm?”",
        instruction:
            "Imagine: A situation at work that usually frustrates you. Reflect on your patterns and awareness.",
        fields: [
            { label: "When was the last time I got upset?", value: "" },
            { label: "What pattern do I notice (disrespect, delay, argument)?", value: "" },
            { label: "Can I predict such moments next time? (Yes/No)", value: "" },
        ],
        starQuestion: "How aware am I of my triggers now? (1–5)",
        purpose: "Converts unconscious reactions into conscious awareness.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Mirror Talk - “Who Am I When No One’s Watching?”",
        instruction:
            "Imagine: You’re getting ready for duty. Reflect on your mindset and integrity.",
        fields: [
            { label: "How am I feeling today?", value: "" },
            { label: "What kind of officer do I want to be today?", value: "" },
            { label: "Positive line to say to myself", value: "" },
        ],
        starQuestion: "How aligned were my actions with my answer? (1–5)",
        purpose: "Promotes integrity and mindfulness.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Role Reflection - “The Uniform and Me”",
        instruction:
            "Imagine: You remove your uniform after duty. Reflect on your identity in both roles.",
        fields: [
            { label: "What’s different about me in and out of uniform?", value: "" },
            { label: "One learning from each role", value: "" },
        ],
        starQuestion: "Do I balance my personal and professional self well? (1–5)",
        purpose: "Encourages identity balance and mental separation of roles.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Thought Catcher - “What’s Going On in My Mind?”",
        instruction:
            "Imagine: You’re stuck in traffic or waiting for duty orders. Observe your thoughts.",
        fields: [
            { label: "What thoughts are running in my mind?", value: "" },
            { label: "Label them: Useful or Not useful", value: "" },
        ],
        starQuestion: "How often do I observe my thoughts without judgment? (1–5)",
        purpose: "Builds metacognitive awareness and reduces rumination.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Feedback Moment - “What Do Others See in Me?”",
        instruction:
            "Imagine: A trusted colleague gives you feedback. Reflect on openness and calmness.",
        fields: [
            { label: "What’s one thing I do well?", value: "" },
            { label: "What’s one thing I can improve?", value: "" },
        ],
        starQuestion: "Was I open and calm while receiving feedback? (1–5)",
        purpose: "Fosters humility and external awareness.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "My Energy Journal - “When Do I Feel at My Best?”",
        instruction:
            "Imagine: Some parts of the day you feel energetic, others drained. Identify your pattern.",
        fields: [
            { label: "When do I feel most alert?", value: "" },
            { label: "When do I feel most tired?", value: "" },
        ],
        starQuestion: "How aware am I of my energy highs and lows? (1–5)",
        purpose: "Helps optimize performance and rest cycles.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Values Compass - “What Matters Most to Me?”",
        instruction:
            "Imagine: You must make a quick ethical decision on duty. Reflect on your values.",
        fields: [
            { label: "My 3 guiding values (e.g., honesty, service, fairness)", value: "" },
            { label: "Did my actions today match my values?", value: "" },
        ],
        starQuestion: "Did I act in line with my values today? (1–5)",
        purpose: "Strengthens ethical clarity and personal alignment.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "My Calm Zone - “How Do I Return to Balance?”",
        instruction:
            "Imagine: You’re emotionally overwhelmed. Practice regaining calm.",
        fields: [
            { label: "What helps me calm down? (breathing, music, walk, prayer, silence)", value: "" },
            { label: "Did I regain calmness after using it?", value: "" },
        ],
        starQuestion: "Did I regain calmness? (1–5)",
        purpose: "Enhances emotional regulation through awareness of personal coping tools.",
        clarityRating: 0,
        subRating: 0,
    },
]);

const submitActivity = async (activity, index) => {
    alert(
        `✅ You completed "${activity.title}"\nReflection Rating: ${activity.clarityRating}/5\nActivity Rating: ${activity.subRating}/5`
    );

    await nextTick();
    const collapseEl = document.getElementById(`collapse-${index}`);
    if (collapseEl && window.bootstrap) {
        const bsCollapse =
            window.bootstrap.Collapse.getInstance(collapseEl) ||
            new window.bootstrap.Collapse(collapseEl);
        bsCollapse.hide();
    }
};

const goBack = () => {
    router.push('/self-enhancement')
}
</script>

<style scoped>
.accordion-button {
    background-color: #f8f9fa;
    color: #212529;
}

.accordion-button:not(.collapsed) {
    background-color: #e9ecef;
    color: #000;
    box-shadow: none;
}

.form-label {
    color: #333;
}

.stars i {
    transition: transform 0.2s;
}

.stars i:hover {
    transform: scale(1.2);
}
</style>
