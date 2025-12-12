<template>
    <div class="container py-4">
        <h2 class="mb-3">Wellness Sessions</h2>

        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div v-for="session in combinedSessions" :key="session.id" class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ session.title }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ session.presenter }}</h6>

                        <div class="d-flex justify-content-between">
                            <p class="mb-0">📅 {{ formatDate(session.start_date) }}</p>
                            <p class="mb-0">🕒 {{ formatTime(session.start_time) }}</p>
                        </div>

                        <p class="mb-2">📍 {{ session.venue }}</p>

                        <div class="d-flex justify-content-between align-items-center mt-2">

                            <a :href="session.youtubeUrl" class="btn btn-outline-accent text-dark" target="_blank">
                                ▶ Watch Live
                            </a>

                            <div class="d-flex align-items-center gap-2">
                                <button
                                    v-if="session.status === 'Completed'"
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="openFeedback(session)"
                                >
                                    <i class="bi bi-chat-square-dots fs-5"></i>
                                </button>

                                <span class="badge bg-secondary p-2">{{ session.status }}</span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- FEEDBACK MODAL -->
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Workshop Feedback: {{ selectedSession ? selectedSession.title : '' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div v-for="(question, qIndex) in questions" :key="qIndex" class="mb-4">
                            <p class="fw-semibold">{{ qIndex + 1 }}. {{ question.text }}</p>

                            <div class="d-flex flex-wrap gap-2">
                                <button
                                    v-for="(opt, oIndex) in likert"
                                    :key="oIndex"
                                    type="button"
                                    class="btn btn-sm rounded-pill"
                                    :class="question.answer === opt ? 'bg-secondary text-white' : 'btn-outline-secondary'"
                                    @click="question.answer = opt"
                                >
                                    {{ opt }}
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                8. Any other suggestions you feel that the workshop should concentrate on or any topics you feel that you need to be trained or taught?
                            </label>
                            <textarea v-model="suggestions" class="form-control" rows="4" placeholder="Type your suggestions (optional)"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-dark" @click="submitFeedback">Submit Feedback</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import * as bootstrap from 'bootstrap'

const events = [
    { id: 1, title: 'Stress Management Seminar', start_date: '2025-07-24', start_time: '10:00:00', presenter: 'Dr. Smith', venue: 'City Wellness Center, Hall A', youtubeUrl: 'https://www.youtube.com/watch?v=ZToicYcHIOU', status: 'Completed' },
    { id: 2, title: 'Morning Yoga for Wellness', start_date: '2025-07-25', start_time: '07:30:00', presenter: 'Yoga Studio', venue: 'Riverside Yoga Park', youtubeUrl: 'https://www.youtube.com/watch?v=v7AYKMP6rOE', status: 'Live' },
    { id: 3, title: 'Sleep & Mental Health Talk', start_date: '2025-08-03', start_time: '16:00:00', presenter: 'Dr. Alka Sharma', venue: 'Community Health Auditorium', youtubeUrl: 'https://www.youtube.com/watch?v=8RsbvZz3SvQ', status: 'Yet to stream' },
    { id: 4, title: 'Walking Meditation Session', start_date: '2025-08-10', start_time: '06:30:00', presenter: 'MindfulSteps Org', venue: 'Botanical Garden Trail', youtubeUrl: 'https://www.youtube.com/watch?v=1vx8iUvfyCY', status: 'Yet to stream' },
    { id: 5, title: 'Healthy Habits for Office Workers', start_date: '2025-09-14', start_time: '11:00:00', presenter: 'Dr. Ramesh Patel', venue: 'Tech Park Conference Hall 2', youtubeUrl: 'https://www.youtube.com/watch?v=uzI9tZ7rTUM', status: 'Yet to stream' },
    { id: 6, title: 'Outdoor Tai Chi Experience', start_date: '2025-09-21', start_time: '07:00:00', presenter: 'TaiChi Club', venue: 'Lakeside Ground, Sector 5', youtubeUrl: 'https://www.youtube.com/watch?v=cEOS2zoyQw4', status: 'Yet to stream' }
]

const workshops = [
    { id: 101, title: 'Anger Management Techniques', start_date: '2025-07-25', start_time: '14:00:00', presenter: 'Dr. Ramchandra Prasad', venue: 'Wellness Hall, Block B', youtubeUrl: 'https://www.youtube.com/watch?v=gmwiJ6ghLIM', status: 'Completed' },
    { id: 102, title: 'Nutrition for Energy', start_date: '2025-07-30', start_time: '11:00:00', presenter: 'Dr. Gopi', venue: 'Diet & Nutrition Lab, Health Tower', youtubeUrl: 'https://www.youtube.com/watch?v=RkH_1R-SQ3c', status: 'Yet to stream' },
    { id: 103, title: 'Work-Life Balance for Professionals', start_date: '2025-08-06', start_time: '10:30:00', presenter: 'Dr. Aisha Khan', venue: 'Hotel GreenView, Banquet Room 1', youtubeUrl: 'https://www.youtube.com/watch?v=Tc9n_1Sow5s', status: 'Yet to stream' },
    { id: 104, title: 'Overcoming Burnout', start_date: '2025-08-23', start_time: '15:00:00', presenter: 'Prof. Sunil Desai', venue: 'Metro Wellness Centre', youtubeUrl: 'https://www.youtube.com/watch?v=iZIjDtVvTH8', status: 'Yet to stream' },
    { id: 105, title: 'Posture & Ergonomics for Desk Jobs', start_date: '2025-09-05', start_time: '09:00:00', presenter: 'Physio Expert Team', venue: 'Urban Health Club, Workshop Zone', youtubeUrl: 'https://www.youtube.com/watch?v=_gBJxUeJrH0', status: 'Yet to stream' },
    { id: 106, title: 'Healing Through Breathwork', start_date: '2025-09-25', start_time: '17:30:00', presenter: 'Nina Gupta', venue: 'Zen Studio, City Center', youtubeUrl: 'https://www.youtube.com/watch?v=4Lb5L-VEm34', status: 'Yet to stream' }
]

const combinedSessions = computed(() => [...events, ...workshops])

function formatDate(dateStr) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateStr).toLocaleDateString(undefined, options)
}

function formatTime(timeStr) {
    const [h, m] = timeStr.split(':')
    const hour = parseInt(h)
    const suffix = hour >= 12 ? 'PM' : 'AM'
    return `${((hour + 11) % 12 + 1)}:${m} ${suffix}`
}

const selectedSession = ref(null)
let modalInstance = null

const likert = [
    'Fully Disagree',
    'Disagree',
    'Agree or Disagree',
    'Agree',
    'Fully Agree'
]

const questions = ref([
    { text: 'Do you feel the facilitator effectively communicated the concepts of Mental health?', answer: null },
    { text: 'Do you feel that there was sufficient information on Mental Health?', answer: null },
    { text: 'Was the trainer able to address all your concerns related to mental health?', answer: null },
    { text: 'Were there sufficient activities related to concept for better understanding?', answer: null },
    { text: 'Were you able to learn any skills related to managing day-to-day issues?', answer: null },
    { text: 'Overall, were you satisfied with the workshop?', answer: null },
    { text: 'Overall, how did you think the workshop will benefit you?', answer: null }
])

const suggestions = ref('')

function openFeedback(session) {
    selectedSession.value = session
    // reset previous answers when opening new session feedback
    questions.value.forEach(q => q.answer = null)
    suggestions.value = ''
    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(document.getElementById('feedbackModal'))
    }
    modalInstance.show()
}

function submitFeedback() {
    const missing = questions.value.some(q => !q.answer)
    if (missing) {
        alert('Please answer all questions before submitting.')
        return
    }

    // Here you can send `selectedSession`, `questions` and `suggestions` to backend if needed.
    alert('Thank you for submitting your feedback!')
    if (modalInstance) modalInstance.hide()
}
</script>

<style scoped>
.btn-sm.rounded-pill {
    padding: 0.35rem 0.9rem;
    font-size: 0.85rem;
}
</style>
