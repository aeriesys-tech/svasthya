<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Hypertension</h4>

        <div class="card shadow-sm">
            <div class="card-header bg-accent">
                <h5 class="mb-0">Answer all questions below</h5>
            </div>

            <div class="card-body">

                <!-- MAIN 10-QUESTION ASSESSMENT -->
                <div v-for="(q, qIndex) in questions" :key="qIndex" class="mb-4">
                    <p class="fw-semibold">{{ `${qIndex + 1}. ${q.text}` }}</p>

                    <div class="d-flex flex-column ms-3">

                        <label
                            v-for="(opt, oIndex) in q.options"
                            :key="oIndex"
                            class="form-check mb-1 text-start"
                        >
                            <input
                                type="checkbox"
                                class="form-check-input me-2"
                                :value="opt"
                                v-model="q.answer"
                                @change="handleSingleSelect(q)"
                            />
                            {{ opt }}
                        </label>

                    </div>
                </div>

                <hr />

                <!-- 5-QUESTION HYPERTENSION RISK SCREEN -->
                <h5 class="mt-4 mb-3">Hypertension Risk Screening</h5>

                <div v-for="(q, qIndex) in riskQuestions" :key="'risk-' + qIndex" class="mb-4">
                    <p class="fw-semibold">{{ `${qIndex + 1}. ${q.text}` }}</p>

                    <div class="d-flex flex-column ms-3">

                        <label
                            v-for="(opt, oIndex) in q.options"
                            :key="oIndex"
                            class="form-check mb-1 text-start"
                        >
                            <input
                                type="checkbox"
                                class="form-check-input me-2"
                                :value="opt"
                                v-model="q.answer"
                                @change="handleSingleSelect(q)"
                            />
                            {{ opt }}
                        </label>

                    </div>
                </div>

                <!-- SUBMIT -->
                <div class="text-center mt-3" v-if="!result">
                    <button class="btn btn-success" @click="submitAssessment">
                        Submit
                    </button>
                </div>

                <!-- RESULT -->
                <div v-if="result" class="alert alert-info mt-4">
                    <h5 class="fw-bold">Assessment Submitted</h5>
                    <p>Your responses have been saved.</p>
                </div>

                <div v-if="result" class="text-center mt-3">
                    <button class="btn btn-secondary" @click="closeComponent">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from "vue";

const emit = defineEmits(["close"]);
const result = ref(null);

/* -------------------------
   10-QUESTION ASSESSMENT
------------------------- */
const questions = reactive([
    {
        text: "When was the last time you checked your blood pressure?",
        type: "single",
        options: [
            "Within the last 3 months",
            "Within the last year",
            "More than a year ago",
            "I don’t remember"
        ],
        answer: []
    },
    {
        text: "What is your usual blood pressure reading (if known)?",
        type: "single",
        options: [
            "Under 120/80",
            "120–129/<80",
            "130–139/80–89",
            "140+/90+",
            "Not sure"
        ],
        answer: []
    },
    {
        text: "Do you experience any of the following symptoms? (check all that apply)",
        type: "multi",
        options: [
            "Frequent headaches",
            "Dizziness",
            "Blurred vision",
            "Chest discomfort",
            "None"
        ],
        answer: []
    },
    {
        text: "How often do you eat high-salt foods?",
        type: "single",
        options: [
            "Rarely",
            "1–3 times per week",
            "4+ times per week"
        ],
        answer: []
    },
    {
        text: "How often do you exercise for at least 30 minutes?",
        type: "single",
        options: [
            "4–7 days per week",
            "1–3 days per week",
            "Rarely/never"
        ],
        answer: []
    },
    {
        text: "How would you describe your stress level?",
        type: "single",
        options: ["Low", "Moderate", "High", "Very high"],
        answer: []
    },
    {
        text: "How many servings of fruits and vegetables do you eat daily?",
        type: "single",
        options: ["3 or more", "1–2", "None or rarely"],
        answer: []
    },
    {
        text: "Do you smoke or use tobacco?",
        type: "single",
        options: ["No", "Occasionally", "Daily"],
        answer: []
    },
    {
        text: "Do you drink alcohol?",
        type: "single",
        options: ["Rarely or never", "Socially (1–3 drinks/week)", "Frequently (4+ drinks/week)"],
        answer: []
    },
    {
        text: "Do you have any of the following conditions? (check all that apply)",
        type: "multi",
        options: [
            "Family history of hypertension",
            "High cholesterol",
            "Diabetes / Prediabetes",
            "Kidney disease",
            "Overweight/obesity",
            "None"
        ],
        answer: []
    }
]);

/* -------------------------
   5-QUESTION RISK SCREEN
------------------------- */
const riskQuestions = reactive([
    {
        text: "Have you had your blood pressure checked recently?",
        type: "single",
        options: [
            "Normal (<120/80)",
            "Elevated (120–129/<80)",
            "High (130/80 or above)",
            "Not sure / Haven’t checked"
        ],
        answer: []
    },
    {
        text: "Do you have any of these symptoms related to high blood pressure? (check all that apply)",
        type: "multi",
        options: ["Frequent headaches", "Vision changes", "Shortness of breath", "None"],
        answer: []
    },
    {
        text: "How often do you consume high-salt foods?",
        type: "single",
        options: ["Rarely", "1–3 times per week", "4+ times per week"],
        answer: []
    },
    {
        text: "How often do you exercise?",
        type: "single",
        options: ["Most days", "1–3 days per week", "Rarely/never"],
        answer: []
    },
    {
        text: "Do you have any of the following risk factors? (check all that apply)",
        type: "multi",
        options: [
            "Family history of hypertension",
            "Overweight/obesity",
            "High stress levels",
            "Diabetes or kidney problems",
            "None"
        ],
        answer: []
    }
]);

/* -------------------------
   HELPER — single select
------------------------- */
function handleSingleSelect(question) {
    if (question.type === "single" && question.answer.length > 1) {
        const last = question.answer[question.answer.length - 1];
        question.answer = [last];
    }
}

/* -------------------------
   SUBMIT
------------------------- */
function submitAssessment() {
    result.value = {
        completed: true,
        timestamp: new Date().toISOString()
    };
}

/* -------------------------
   CLOSE COMPONENT
------------------------- */
function closeComponent() {
    emit("close");
}
</script>

<style scoped>
.bg-accent {
    background: #efefef;
}

.form-check-input {
    cursor: pointer;
    border: 2px solid #333 !important;
}

.form-check-input:checked {
    background-color: #333 !important;
    border-color: #333 !important;
}

.form-check-label,
.form-check {
    cursor: pointer;
}
</style>
