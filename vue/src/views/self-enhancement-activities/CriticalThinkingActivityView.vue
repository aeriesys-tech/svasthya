<template>
    <div class="container py-4">
        <h2 class="text-center mb-4 fw-bold">Critical Thinking Activities</h2>

        <div class="accordion" id="criticalThinkingAccordion">
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
                    data-bs-parent="#criticalThinkingAccordion"
                >
                    <div class="accordion-body">
                        <!-- Instruction -->
                        <p class="text-muted mb-3">{{ activity.instruction }}</p>

                        <!-- Dynamic Input Fields -->
                        <div v-for="(field, fIndex) in activity.fields" :key="fIndex" class="mb-3">
                            <label class="form-label fw-semibold">{{ field.label }}</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="field.value"
                                placeholder="Enter your response..."
                            />
                        </div>

                        <!-- ⭐ Question -->
                        <div class="mt-3">
                            <p class="fw-semibold mb-2">⭐ {{ activity.starQuestion }}</p>
                        </div>

                        <!-- Star Rating (replaces slider) -->
                        <div class="feedback-stars text-center mt-2">
                            <h6 class="text-dark mb-2">Your Clarity Rating:</h6>
                            <div class="stars">
                                <i
                                    v-for="star in 5"
                                    :key="star"
                                    class="bi"
                                    :class="star <= activity.clarityRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
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

                        <!-- Subactivity Feedback Stars -->
                        <div class="feedback-stars text-center mt-4">
                            <h6 class="text-dark mb-2">Rate this Activity:</h6>
                            <div class="stars">
                                <i
                                    v-for="star in 5"
                                    :key="star"
                                    class="bi"
                                    :class="star <= activity.subRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                    @click="activity.subRating = star"
                                    style="font-size: 1.8rem; cursor: pointer;"
                                ></i>
                            </div>
                            <div class="mt-2 small text-muted">
                                Your rating: {{ activity.subRating }} / 5
                            </div>
                        </div>

                        <!-- Submit Button -->
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

const goBack = () => {
    router.push('/self-enhancement')
}

const activities = ref([
    {
        title: "Fact or Feeling?",
        instruction: "Recall one belief or statement you heard today.",
        fields: [
            { label: "What’s the statement?", value: "" },
            { label: "Is it a fact or a feeling/opinion?", value: "" },
            { label: "How sure am I about it?", value: "" },
        ],
        starQuestion: "Rate (1–5): How clearly did I separate fact from feeling?",
        purpose: "Encourages evidence-based thinking.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Before I React",
        instruction: "Something today made me upset or excited.",
        fields: [
            { label: "What happened?", value: "" },
            { label: "What did I assume immediately?", value: "" },
            { label: "What else could be true?", value: "" },
        ],
        starQuestion: "Rate (1–5): Did pausing before reacting help me see clearly?",
        purpose: "Reduces cognitive impulsivity.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Check the Source",
        instruction: "I heard or read something today (social media, friend, news).",
        fields: [
            { label: "Who said it?", value: "" },
            { label: "Is it verified or just repeated?", value: "" },
            { label: "Will believing it help or harm me?", value: "" },
        ],
        starQuestion: "Rate (1–5): Do I now trust information more carefully?",
        purpose: "Builds awareness of misinformation and bias.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Flip the View",
        instruction: "Pick one situation that bothered you.",
        fields: [
            { label: "My side of the story:", value: "" },
            { label: "The other side could be:", value: "" },
            { label: "What’s the neutral fact?", value: "" },
        ],
        starQuestion: "Rate (1–5): Did balancing both sides calm my mind?",
        purpose: "Enhances cognitive flexibility.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Ask Why, Twice",
        instruction: "Something annoyed or confused me today.",
        fields: [
            { label: "Why did it affect me?", value: "" },
            { label: "Why really? (deeper reason)", value: "" },
        ],
        starQuestion: "Rate (1–5): Did asking “why” twice bring more clarity?",
        purpose: "Promotes layered self-inquiry.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Evidence Table",
        instruction: "Choose one strong opinion you have today.",
        fields: [
            { label: "What supports it?", value: "" },
            { label: "What goes against it?", value: "" },
            { label: "What would a third person say?", value: "" },
        ],
        starQuestion: "Rate (1–5): Did this make my thinking more balanced?",
        purpose: "Structured logical evaluation.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Question the Obvious",
        instruction: "Something I accepted without question today was:",
        fields: [
            { label: "If I challenge it, what new angle appears?", value: "" },
        ],
        starQuestion: "Rate (1–5): Did questioning expand my understanding?",
        purpose: "Encourages independent thinking.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Connect the Dots",
        instruction: "I faced a small problem today.",
        fields: [
            { label: "What caused it?", value: "" },
            { label: "What pattern do I notice?", value: "" },
            { label: "What can I change next time?", value: "" },
        ],
        starQuestion: "Rate (1–5): Did identifying the pattern make me feel in control?",
        purpose: "Analytical reflection for everyday control.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Half Full or Half Empty",
        instruction: "Think of something that didn’t go as planned.",
        fields: [
            { label: "What went wrong?", value: "" },
            { label: "What still went right?", value: "" },
            { label: "What can I learn from both?", value: "" },
        ],
        starQuestion: "Rate (1–5): Did I view it more rationally after this?",
        purpose: "Balanced thinking and optimism.",
        clarityRating: 0,
        subRating: 0,
    },
    {
        title: "Pause Before Sharing",
        instruction: "Before forwarding or repeating something I saw or heard today:",
        fields: [
            { label: "Did I check if it’s true?", value: "" },
            { label: "Why do I want to share it?", value: "" },
            { label: "Will it help someone or just spread emotion?", value: "" },
        ],
        starQuestion: "Rate (1–5): Did I act more responsibly today?",
        purpose: "Builds social responsibility and cognitive control.",
        clarityRating: 0,
        subRating: 0,
    },
]);

const submitActivity = async (activity, index) => {
    alert(
        `✅ You completed "${activity.title}"\nClarity Rating: ${activity.clarityRating}/5\nActivity Rating: ${activity.subRating}/5`
    );

    // Collapse the accordion after submission
    await nextTick();
    const collapseEl = document.getElementById(`collapse-${index}`);
    if (collapseEl && window.bootstrap) {
        const bsCollapse = window.bootstrap.Collapse.getInstance(collapseEl)
            || new window.bootstrap.Collapse(collapseEl);
        bsCollapse.hide();
    }
};
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
