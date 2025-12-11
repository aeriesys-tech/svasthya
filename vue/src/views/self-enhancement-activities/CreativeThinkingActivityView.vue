<template>
    <div class="container py-4">
        <h2 class="text-center mb-4 fw-bold">Creative Thinking Activities</h2>

        <div class="accordion" id="creativeThinkingAccordion">
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
                    data-bs-parent="#creativeThinkingAccordion"
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

                        <!-- ⭐ Reflective Question -->
                        <div class="mt-3">
                            <p class="fw-semibold mb-2">⭐ {{ activity.starQuestion }}</p>
                        </div>

                        <!-- 🌟 Star Rating (replaces slider) -->
                        <div class="feedback-stars text-center mt-2">
                            <h6 class="text-dark mb-2">Your Reflection Rating:</h6>
                            <div class="stars">
                                <i
                                    v-for="star in 5"
                                    :key="star"
                                    class="bi"
                                    :class="star <= activity.reflectionRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                    @click="activity.reflectionRating = star"
                                    style="font-size: 1.8rem; cursor: pointer;"
                                ></i>
                            </div>
                            <div class="mt-2 small text-muted">
                                Rating: {{ activity.reflectionRating }} / 5
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
                                @click="submitActivity(activity)"
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
import { ref } from "vue";
import router from "../../router";

const goBack = () => {
    router.push('/self-enhancement')
}

const activities = ref([
    {
        title: "Three New Ways",
        instruction: "Think of one small daily problem (time, traffic, communication).",
        fields: [
            { label: "My usual way to handle it:", value: "" },
            { label: "Three different or funny ways to handle it:", value: "" },
        ],
        starQuestion: "1–5: Did new ideas come when I stopped judging them?",
        purpose: "Encourage open thinking and humor-based idea flow.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "What If...",
        instruction: "Pick any routine activity (brushing, meeting, driving).",
        fields: [
            { label: "What if it was done completely differently?", value: "" },
            { label: "What would happen then?", value: "" },
        ],
        starQuestion: "1–5: Did this make me smile or think differently?",
        purpose: "Stimulates curiosity and playfulness.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "Reverse the Problem",
        instruction: "Choose a challenge you’re facing.",
        fields: [
            { label: "What if the opposite of my usual belief was true?", value: "" },
            { label: "What solution would appear then?", value: "" },
        ],
        starQuestion: "1–5: Did flipping the view spark new insight?",
        purpose: "Builds divergent thinking.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "Ask a Child’s Question",
        instruction: "Imagine a curious 6-year-old asking “Why?” about something normal.",
        fields: [
            { label: "The question they’d ask:", value: "" },
            { label: "My honest, creative answer:", value: "" },
        ],
        starQuestion: "1–5: Did this question make me think fresh?",
        purpose: "Promotes simplicity and creative inquiry.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "Combine the Unrelated",
        instruction: "Pick two random objects around you.",
        fields: [
            { label: "Object 1:", value: "" },
            { label: "Object 2:", value: "" },
            { label: "What new use or idea can I make by combining them?", value: "" },
        ],
        starQuestion: "1–5: Did I enjoy imagining new possibilities?",
        purpose: "Enhances cognitive association and imagination.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "Dream Fixer",
        instruction: "Think of something you wish existed to make life easier.",
        fields: [
            { label: "What would that invention or idea be?", value: "" },
            { label: "How would it help?", value: "" },
        ],
        starQuestion: "1–5: Did this idea excite me?",
        purpose: "Fosters vision and proactive thought.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "Everyday Artist",
        instruction: "Find creativity in something routine.",
        fields: [
            { label: "What did I do today that could be done more creatively?", value: "" },
            { label: "How can I add a small creative twist?", value: "" },
        ],
        starQuestion: "1–5: Did creativity make my task more enjoyable?",
        purpose: "Brings creativity into ordinary moments.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "Story in 3 Lines",
        instruction: "Write a short, funny, or thoughtful story in just 3 lines about your day.",
        fields: [
            { label: "Line 1: Start", value: "" },
            { label: "Line 2: Twist", value: "" },
            { label: "Line 3: End", value: "" },
        ],
        starQuestion: "1–5: Did this make me see my day differently?",
        purpose: "Builds narrative flexibility and humor.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "Imagine Without Fear",
        instruction: "Write one idea that you’ve been scared to try.",
        fields: [
            { label: "Idea:", value: "" },
            { label: "What’s stopping me?", value: "" },
            { label: "What if I tried it just once?", value: "" },
        ],
        starQuestion: "1–5: Did imagining this make me feel braver?",
        purpose: "Reduces mental rigidity through safe imagination.",
        reflectionRating: 0,
        subRating: 0,
    },
    {
        title: "New Path Challenge",
        instruction: "Choose one small thing to do differently tomorrow (route, order, routine).",
        fields: [
            { label: "My usual way:", value: "" },
            { label: "My new way:", value: "" },
            { label: "How did it feel?", value: "" },
        ],
        starQuestion: "1–5: Did change feel refreshing?",
        purpose: "Encourages openness to experimentation.",
        reflectionRating: 0,
        subRating: 0,
    },
]);

const submitActivity = (activity) => {
    alert(
        `✅ You completed "${activity.title}"\nReflection Rating: ${activity.reflectionRating}/5\nActivity Rating: ${activity.subRating}/5`
    );
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
