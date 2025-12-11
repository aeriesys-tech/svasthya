<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">General Health</h4>

        <div class="card shadow-sm">
            <div class="card-header bg-accent">
                <h5 class="mb-0">Answer all questions below</h5>
            </div>

            <div class="card-body">

                <!-- MAIN QUESTIONNAIRE -->
                <div v-for="(q, qIndex) in questions" :key="qIndex" class="mb-4">
                    <p class="fw-semibold">{{ `${qIndex + 1}. ${q.text}` }}</p>

                    <div class="d-flex flex-column ms-3">
                        <!-- OPTIONS -->
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
                                @change="handleSelect(q)"
                            />
                            {{ opt }}
                        </label>

                        <!-- TEXT INPUT WHEN REQUIRED -->
                        <div v-if="q.needsInput && q.answer.includes('Yes')" class="mt-2">
                            <input
                                type="text"
                                class="form-control"
                                v-model="q.additionalText"
                                :placeholder="q.placeholder"
                            />
                        </div>
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

/* -------------------------------------
   GENERAL HEALTH QUESTIONNAIRE DATA
-------------------------------------- */
const questions = reactive([
    {
        text: "How would you rate your overall health?",
        type: "single",
        options: ["Excellent", "Good", "Fair", "Poor"],
        answer: [],
        needsInput: false
    },
    {
        text: "Do you have any ongoing medical conditions?",
        type: "single",
        options: ["No", "Yes"],
        answer: [],
        needsInput: true,
        additionalText: "",
        placeholder: "Please list your medical conditions"
    },
    {
        text: "Do you currently take any medications or supplements?",
        type: "single",
        options: ["No", "Yes"],
        answer: [],
        needsInput: false
    },
    {
        text: "How often do you exercise (≥30 minutes per session)?",
        type: "single",
        options: [
            "Rarely",
            "1–2 times/week",
            "3–4 times/week",
            "5+ times/week"
        ],
        answer: [],
        needsInput: false
    },
    {
        text: "How would you describe your diet?",
        type: "single",
        options: [
            "Healthy and balanced",
            "Fair",
            "Poor"
        ],
        answer: [],
        needsInput: false
    },
    {
        text: "How many hours of sleep do you usually get per night?",
        type: "single",
        options: ["<5", "5–6", "7–8", ">8"],
        answer: [],
        needsInput: false
    },
    {
        text: "How often do you feel stressed, anxious, or low in mood?",
        type: "single",
        options: [
            "Rarely",
            "Sometimes",
            "Often",
            "Nearly every day"
        ],
        answer: [],
        needsInput: false
    },
    {
        text: "Do you smoke or use tobacco products?",
        type: "single",
        options: ["No", "Occasionally", "Daily"],
        answer: [],
        needsInput: false
    },
    {
        text: "How often do you consume alcohol?",
        type: "single",
        options: ["Never", "Occasionally", "Weekly", "Daily"],
        answer: [],
        needsInput: false
    },
    {
        text: "Do you currently have any physical symptoms or health concerns you’d like to discuss?",
        type: "single",
        options: ["No", "Yes"],
        answer: [],
        needsInput: true,
        additionalText: "",
        placeholder: "Please specify your health concerns"
    }
]);

/* --------------------------------------
    SINGLE SELECT LOGIC USING CHECKBOXES
----------------------------------------- */
function handleSelect(question) {
    if (question.type === "single" && question.answer.length > 1) {
        const last = question.answer[question.answer.length - 1];
        question.answer = [last];
    }

    // Clear text input when "No" is chosen
    if (question.needsInput && !question.answer.includes("Yes")) {
        question.additionalText = "";
    }
}

/* --------------------------------------
   SUBMIT ASSESSMENT
----------------------------------------- */
function submitAssessment() {
    result.value = {
        completed: true,
        timestamp: new Date().toISOString()
    };
}

/* --------------------------------------
   CLOSE COMPONENT
----------------------------------------- */
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
