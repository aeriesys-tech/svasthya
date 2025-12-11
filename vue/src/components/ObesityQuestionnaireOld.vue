<template>
    <div class="container p-2">
        <div v-if="fromInfoView">
            <h5 class="mt-2 mb-3 mx-2">Importence of Post Test</h5>
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <p class="mb-0">
                        A post-test helps reinforce learning, identify areas for improvement, and track your progress. It's a simple step that makes a big difference in your health journey.
                    </p>
                </div>
            </div>
        </div>
        <h5 class="text-center py-3"> Obesity Management</h5>
        <!-- <div v-if="!fromInfoView" class="video-container mt-3 rounded">
            <iframe width="100%" height="200" 
                src="https://www.youtube.com/embed/zyn8rGboNfU?si=gPMmSWmlZeBNt8k5" 
                title="YouTube video player" 
                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
            </iframe>
        </div> -->
        <form @submit.prevent="submitAnswers" class="p-3">
            <div
                v-for="(question, qIndex) in questions"
                :key="qIndex"
                class="mb-4"
            >
                <label class="form-label fw-semibold">{{ qIndex + 1 }}. {{ question.text }}</label>
    
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <button
                        v-for="(option, oIndex) in likertScale"
                        :key="oIndex"
                        type="button"
                        class="btn w-100 rounded-pill"
                        :class="{
                            'btn-outline-theme': question.answer !== option,
                            'btn-theme': question.answer === option
                        }"
                        @click="question.answer = option"
                    >
                        {{ option }}
                    </button>
                </div>
            </div>
    
            <div class="text-center">
                <button class="btn btn-success rounded-pill px-4" type="submit">
                    Submit
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter();
const route = useRoute();

const fromInfoView = computed(() => route.query.fromInfo === '1')

const likertScale = ['Never', 'Rarely', 'Sometimes', 'Often', 'Always']

const questions = ref([
    { text: "Do you feel tired easily while performing daily physical activities?", answer: "" },
    { text: "Have you gained weight steadily over the past 6 months?", answer: "" },
    { text: "Do you eat mostly because you're hungry or because you're stressed/bored?", answer: "" },
    { text: "How many days a week do you engage in physical activity (30 mins or more)?", answer: "" },
    { text: "Do you read food labels before eating packaged food?", answer: "" },
    { text: "How many sugary drinks (soda/juice/tea with sugar) do you consume daily?", answer: "" },
    { text: "How often do you eat late at night?", answer: "" },
    { text: "Are you aware of your BMI (Body Mass Index)?", answer: "" },
    { text: "Do you experience knee/back pain due to your weight?", answer: "" },
    { text: "Do you feel motivated to lose or manage your weight?", answer: "" }
])

function submitAnswers() {
    const unanswered = questions.value.filter(q => !q.answer)
    if (unanswered.length) {
        alert("Please answer all questions before submitting.")
        return
    }
    if (fromInfoView.value) {
        alert("Thanks again! \nOur WBO will update the progress.")
        router.push('/dashboard') // Change as per your actual dashboard route
    } else {
        alert("Thank you for submitting the questionnaire!\nOur WBO will get in touch with you.")
        router.push({ path: '/obesity/info' })
    }
}
</script>

<style scoped>
button.rounded-pill {
    font-size: 0.95rem;
    white-space: nowrap;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
}
</style>