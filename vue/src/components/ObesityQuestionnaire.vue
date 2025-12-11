<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Obesity Self-Assessment</h4>

        <div class="card shadow-sm">
            <div class="card-header bg-accent">
                <h5 class="mb-0">Answer all questions below</h5>
            </div>

            <div class="card-body">

                <!-- MAIN 12-QUESTION ASSESSMENT -->
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

                <!-- 5-QUESTION RISK SCREENING -->
                <h5 class="mt-4 mb-3">Obesity Risk Screening</h5>

                <div v-for="(rq, rIndex) in riskQuestions" :key="'risk-' + rIndex" class="mb-4">
                    <p class="fw-semibold">{{ `${rIndex + 1}. ${rq.text}` }}</p>

                    <div class="d-flex flex-column ms-3">
                        <label
                            v-for="(opt, oIndex) in rq.options"
                            :key="oIndex"
                            class="form-check mb-1 text-start"
                        >
                            <input
                                type="checkbox"
                                class="form-check-input me-2"
                                :value="opt"
                                v-model="rq.answer"
                                @change="handleSingleSelect(rq)"
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

/* ----------------------------------------------------
   12-QUESTION BASIC OBESITY SELF-ASSESSMENT
---------------------------------------------------- */
const questions = reactive([
    {
        text: "Do you know your current BMI?",
        type: "single",
        options: [
            "Under 25",
            "25–29.9",
            "30 or above",
            "I don’t know"
        ],
        answer: []
    },
    {
        text: "Waist circumference (approximate):",
        type: "single",
        options: [
            "Normal range",
            "Higher than recommended",
            "Unsure / Don’t know"
        ],
        answer: []
    },
    {
        text: "How often do you eat fast food or fried foods?",
        type: "single",
        options: [
            "Rarely",
            "1–2 times/week",
            "3+ times/week"
        ],
        answer: []
    },
    {
        text: "How often do you consume sugary drinks (soda, sweet tea, juice)?",
        type: "single",
        options: [
            "Rarely",
            "Weekly",
            "Daily"
        ],
        answer: []
    },
    {
        text: "How often do you overeat or eat past fullness?",
        type: "single",
        options: [
            "Rarely",
            "Sometimes",
            "Often"
        ],
        answer: []
    },
    {
        text: "How many fruits and vegetables do you eat daily?",
        type: "single",
        options: [
            "3 or more servings",
            "1–2 servings",
            "None / Rarely"
        ],
        answer: []
    },
    {
        text: "How often do you exercise (30+ minutes)?",
        type: "single",
        options: [
            "4–7 days/week",
            "1–3 days/week",
            "Rarely/never"
        ],
        answer: []
    },
    {
        text: "How many hours do you spend sitting daily?",
        type: "single",
        options: [
            "Less than 4",
            "4–7",
            "More than 7"
        ],
        answer: []
    },
    {
        text: "Do you have any medical conditions? (check all that apply)",
        type: "multi",
        options: [
            "High blood pressure",
            "High cholesterol",
            "Prediabetes / Diabetes",
            "None"
        ],
        answer: []
    },
    {
        text: "Do you have a family history of obesity or type 2 diabetes?",
        type: "single",
        options: [
            "No",
            "Yes"
        ],
        answer: []
    },
    {
        text: "How would you describe your stress eating or emotional eating?",
        type: "single",
        options: [
            "Rare",
            "Occasional",
            "Frequent"
        ],
        answer: []
    },
    {
        text: "How many hours of sleep do you get per night?",
        type: "single",
        options: [
            "7–9 hours",
            "5–6 hours",
            "Less than 5 or very irregular"
        ],
        answer: []
    }
]);

/* ----------------------------------------------------
   5-QUESTION OBESITY RISK SCREENING
---------------------------------------------------- */
const riskQuestions = reactive([
    {
        text: "What is your approximate BMI?",
        type: "single",
        options: [
            "Under 25",
            "25–29.9",
            "30 or above",
            "Not sure"
        ],
        answer: []
    },
    {
        text: "How often do you engage in physical activity (30 minutes or more)?",
        type: "single",
        options: [
            "Most days",
            "1–3 days per week",
            "Rarely/never"
        ],
        answer: []
    },
    {
        text: "How often do you eat high-calorie or processed foods (fried foods, sweets, fast food)?",
        type: "single",
        options: [
            "Rarely",
            "1–3 times per week",
            "4+ times per week"
        ],
        answer: []
    },
    {
        text: "How often do you drink sugary beverages (soda, sweet tea, juice)?",
        type: "single",
        options: [
            "Rarely",
            "Weekly",
            "Daily"
        ],
        answer: []
    },
    {
        text: "Do you have any of the following? (check all that apply)",
        type: "multi",
        options: [
            "High blood pressure",
            "High cholesterol",
            "Prediabetes / Diabetes",
            "None"
        ],
        answer: []
    }
]);

/* ----------------------------------------------------
   SINGLE-SELECT BEHAVIOR (same as your component)
---------------------------------------------------- */
function handleSingleSelect(question) {
    if (question.type === "single" && question.answer.length > 1) {
        const last = question.answer[question.answer.length - 1];
        question.answer = [last];
    }
}

/* ----------------------------------------------------
   SUBMIT
---------------------------------------------------- */
function submitAssessment() {
    result.value = {
        completed: true,
        timestamp: new Date().toISOString()
    };
}

/* ----------------------------------------------------
   CLOSE
---------------------------------------------------- */
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

.form-check,
.form-check-label {
    cursor: pointer;
}
</style>
