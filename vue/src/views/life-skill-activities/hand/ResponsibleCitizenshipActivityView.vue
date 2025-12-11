<template>
    <div class="container py-5">

        <!-- STEP 1 — WELCOME SCREEN -->
        <div v-if="step === 1">
            <h1 class="text-center fw-bold mb-3">Civic Training</h1>
            <p class="text-center mb-4">
                Learn, Reflect, Serve better.
            </p>

            <div class="text-center">
                <button class="btn btn-primary btn-lg px-4" @click="goToStep(2)">
                    Start Learning
                </button>
            </div>
        </div>

        <!-- STEP 2 — DAILY CIVIC LEARNING MODULE -->
        <div v-if="step === 2">
            <h2 class="fw-bold mb-3">Today's Lesson</h2>
            <p class="mb-3">
                <strong>{{ todayLesson.title }}</strong>
            </p>

            <div class="mb-4">
                <button class="btn btn-outline-primary w-100 mb-3" @click="viewType = 'video'">
                    Watch Video (2–3 minutes)
                </button>
                <button class="btn btn-outline-secondary w-100" @click="viewType = 'visual'">
                    View Visual Lesson (Interactive Graphic)
                </button>
            </div>

            <div v-if="viewType === 'video'" class="mb-4">
                <div class="ratio ratio-16x9 rounded shadow-sm">
                    <iframe
                        :src="todayLesson.videoUrl"
                        title="Civic Training Video"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>

            <div v-if="viewType === 'visual'" class="mb-4 p-3 border rounded">
                <img :src="todayLesson.visualUrl" alt="Lesson Graphic" class="img-fluid rounded" />
                <p class="mt-3">{{ todayLesson.visualText }}</p>
            </div>

            <div class="text-end">
                <button
                    class="btn btn-success"
                    :disabled="!viewType"
                    @click="goToStep(3)"
                >
                    Start Lesson Quiz
                </button>
            </div>
        </div>

        <!-- STEP 3 — QUICK CIVIC QUIZ -->
        <div v-if="step === 3">
            <h3 class="fw-bold mb-3">Quick Civic Quiz</h3>

            <p class="mb-4">{{ todayLesson.quiz.question }}</p>

            <div v-for="(opt, idx) in todayLesson.quiz.options" :key="idx" class="form-check">
                <input
                    class="form-check-input"
                    type="radio"
                    name="quiz"
                    :id="'opt'+idx"
                    :value="opt"
                    v-model="quizAnswer"
                />
                <label class="form-check-label" :for="'opt'+idx">{{ opt }}</label>
            </div>

            <div class="text-end mt-4">
                <button
                    class="btn btn-primary"
                    :disabled="!quizAnswer"
                    @click="checkQuiz"
                >
                    Submit Answer
                </button>
            </div>
        </div>

        <!-- STEP 4 — IMMEDIATE FEEDBACK -->
        <div v-if="step === 4" class="text-center">
            <h3 class="fw-bold mb-3">Quiz Result</h3>

            <p class="fw-bold" :class="quizCorrect ? 'text-success' : 'text-danger'">
                {{ quizCorrect ? 'Correct!' : 'Incorrect.' }}
            </p>

            <p class="mb-4">
                {{ todayLesson.quiz.explanation }}
            </p>

            <button class="btn btn-success" @click="goToStep(5)">
                Next
            </button>
        </div>

        <!-- STEP 5 — REFLECTION PROMPT -->
        <div v-if="step === 5">
            <h3 class="fw-bold mb-3">Reflection</h3>

            <p class="mb-3">
                Why does this topic matter to your role or your community?
            </p>

            <textarea
                class="form-control mb-3"
                rows="3"
                v-model="reflectionText"
                maxlength="200"
                placeholder="Type your answer here (max 1 sentence)"
            ></textarea>

            <!--<button class="btn btn-secondary me-2" @click="useVoiceToText">
                🎤 Speak Reflection
            </button>-->

            <button
                class="btn btn-primary"
                :disabled="!reflectionText.trim()"
                @click="saveReflection"
            >
                Save & Continue
            </button>
        </div>

        <!-- STEP 6 — SAVE & PROGRESS -->
        <div v-if="step === 6" class="text-center">
            <h3 class="fw-bold mb-3">Great job!</h3>

            <p>You’ve completed today’s civic learning.</p>

            <div class="my-4 p-3 border rounded bg-light">
                <strong>Badge Earned:</strong>
                <div class="mt-2 badge bg-primary p-2">
                    Completed 1 Civic Lesson
                </div>
            </div>

            <button class="btn btn-primary me-2" @click="goToStep(7)">
                View Civic Journal
            </button>

            <button class="btn btn-success" @click="finishActivity">
                Done
            </button>
        </div>

        <!-- STEP 7 — CIVIC JOURNAL -->
        <div v-if="step === 7">
            <h3 class="fw-bold mb-3">Civic Journal</h3>

            <p class="text-muted">Review your completed lessons</p>

            <ul class="list-group mb-4">
                <li
                    class="list-group-item"
                    v-for="(entry, index) in journal"
                    :key="index"
                >
                    <div class="fw-bold">{{ entry.title }}</div>
                    <div>Quiz: {{ entry.correct ? 'Correct' : 'Incorrect' }}</div>
                    <div class="mt-1"><em>"{{ entry.reflection }}"</em></div>
                </li>
            </ul>

            <button class="btn btn-secondary" @click="goToStep(8)">
                Progress & Badges
            </button>
        </div>

        <!-- STEP 8 — PROGRESS & RECOGNITION -->
        <div v-if="step === 8">
            <h3 class="fw-bold mb-3">Progress & Recognition</h3>

            <p>You earned a new badge today!</p>

            <div class="my-4">
                <div class="badge bg-success p-2 me-2">1 Week of Civic Learning</div>
                <div class="badge bg-info p-2">Completed 5 Lessons</div>
            </div>

            <button class="btn btn-primary" @click="finishActivity">
                Done
            </button>
        </div>

    </div>
</template>

<script setup>
import { ref } from 'vue';

const step = ref(1);
const viewType = ref(null);
const quizAnswer = ref(null);
const quizCorrect = ref(false);
const reflectionText = ref('');
const journal = ref(JSON.parse(localStorage.getItem('civic_journal') || '[]'));

const todayLesson = {
    title: "Right to Equality in Policing (Article 14)",
    videoUrl: "https://www.youtube.com/embed/example",
    visualUrl: "/assets/civic-equality.png",
    visualText: "Equality before law means police must treat every person fairly, without bias.",
    quiz: {
        question: "What does Article 21 guarantee to every citizen?",
        options: [
            "Right to life and personal liberty",
            "Right to free speech",
            "Right to equality"
        ],
        answer: "Right to life and personal liberty",
        explanation:
            "Article 21 guarantees the right to life and personal liberty. It ensures fair treatment and due process for every citizen."
    }
};

function goToStep(n) {
    step.value = n;
}

function checkQuiz() {
    quizCorrect.value = quizAnswer.value === todayLesson.quiz.answer;
    step.value = 4;
}

function useVoiceToText() {
    alert("Voice-to-text functionality will be enabled here.");
}

function saveReflection() {
    journal.value.push({
        title: todayLesson.title,
        correct: quizCorrect.value,
        reflection: reflectionText.value
    });

    localStorage.setItem('civic_journal', JSON.stringify(journal.value));

    step.value = 6;
}

function finishActivity() {
    alert("Activity completed!");
    // You can navigate to home or another page here
}
</script>

<style scoped>
.badge {
    font-size: 0.9rem;
}
</style>
