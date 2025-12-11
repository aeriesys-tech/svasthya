<template>
    <div class="container p-3">
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
        <h5 class="text-center py-3">Anger Management</h5>
        <!-- <div v-if="!fromInfoView" class="video-container mt-3 rounded">
            <iframe width="100%" height="200" 
                src="https://www.youtube.com/embed/a8t5SwmqDnk?si=NkGfev4jtFeBWf6X" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
            </iframe>
        </div> -->
        <form @submit.prevent="submitAnswers" class="p-3">
            <div v-for="(question, qIndex) in questions" :key="qIndex" class="mb-4">
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

const router = useRouter()
const route = useRoute()

const fromInfoView = computed(() => route.query.fromInfo === '1')

const likertScale = ['Never', 'Rarely', 'Sometimes', 'Often', 'Always']

const questions = ref([
    { text: "Do you get irritated over small things (like traffic or noise)?", answer: "" },
    { text: "Do you raise your voice when you're angry?", answer: "" },
    { text: "After anger outbursts, do you regret your reaction?", answer: "" },
    { text: "Can you stay calm when someone disagrees with you?", answer: "" },
    { text: "Do you tend to bottle up your feelings?", answer: "" },
    { text: "Do you ever feel physical symptoms when angry (sweating, racing heart)?", answer: "" },
    { text: "Do your colleagues find you \"short-tempered\"?", answer: "" },
    { text: "Have you ever broken something out of anger?", answer: "" },
    { text: "Are you aware of what triggers your anger?", answer: "" },
    { text: "Do you know any healthy ways to calm down?", answer: "" }
])

function submitAnswers() {
    const unanswered = questions.value.filter(q => !q.answer)
    if (unanswered.length) {
        alert("Please answer all questions before submitting.")
        return
    }    

    if (fromInfoView.value) {
        alert("Thanks again! \nOur WBO will update the progress.")
        router.push('/dashboard') // Change as needed
    } else {
        alert("Thank you for submitting the questionnaire!\nOur WBO will get in touch with you.")
        router.push('/anger/info')
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
