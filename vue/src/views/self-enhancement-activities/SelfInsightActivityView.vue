<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Self-Insight & Thought Awareness Activities</h1>

        <div class="accordion" id="selfInsightAccordion">
            <div
                class="accordion-item mb-3"
                v-for="(activity, index) in activities"
                :key="index"
            >
                <h2 class="accordion-header">
                    <button
                        class="accordion-button fw-semibold"
                        type="button"
                        :class="{ collapsed: activeIndex !== index }"
                        @click="toggleActivity(index)"
                    >
                        {{ index + 1 }}. {{ activity.title }}
                    </button>
                </h2>

                <div
                    class="accordion-collapse collapse"
                    :class="{ show: activeIndex === index }"
                >
                    <div class="accordion-body">
                        <h6 class="fw-bold mb-2 text-dark">Imagine:</h6>
                        <p>{{ activity.imagine }}</p>

                        <h6 class="fw-bold mb-2 text-dark">Do / Practice:</h6>
                        <ul>
                            <li
                                v-for="(step, sIndex) in activity.steps"
                                :key="sIndex"
                                class="mb-2"
                            >
                                {{ step }}
                                <div
                                    v-if="needsInput(step)"
                                    class="mt-2 ms-3"
                                >
                                    <textarea
                                        v-model="activity.inputs[sIndex]"
                                        rows="2"
                                        class="form-control"
                                        placeholder="Write your response here..."
                                    ></textarea>
                                </div>
                            </li>
                        </ul>

                        <h6 class="fw-bold mb-2 text-dark">Reflection:</h6>
                        <p><em>{{ activity.reflection }}</em></p>

                        <!-- ⭐ Reflection Rating -->
                        <div class="rating-section text-center mt-3">
                            <div class="stars">
                                <i
                                    v-for="star in 5"
                                    :key="star"
                                    class="bi"
                                    :class="[
                                        activity.reflectionRating >= star
                                            ? 'bi-star-fill text-warning'
                                            : 'bi-star text-secondary',
                                        'fs-4',
                                        'mx-1',
                                        'cursor-pointer'
                                    ]"
                                    @click="activity.reflectionRating = star"
                                ></i>
                            </div>
                            <div class="mt-2 fw-bold">
                                Rating: 
                                <span class="text-success">{{ activity.reflectionRating }}</span> / 5
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2 text-dark mt-3">Purpose:</h6>
                        <p>{{ activity.purpose }}</p>

                        <!-- ⭐ Activity Rating -->
                        <div class="rating-section text-center mt-3">
                            <label class="form-label fw-semibold d-block">
                                Rate this activity:
                            </label>
                            <div class="stars">
                                <i
                                    v-for="star in 5"
                                    :key="star"
                                    class="bi"
                                    :class="[
                                        activity.activityRating >= star
                                            ? 'bi-star-fill text-warning'
                                            : 'bi-star text-secondary',
                                        'fs-4',
                                        'mx-1',
                                        'cursor-pointer'
                                    ]"
                                    @click="activity.activityRating = star"
                                ></i>
                            </div>
                            <div class="mt-2 fw-bold">
                                Current Rating:
                                <span class="text-success">{{ activity.activityRating }}</span> / 5
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button
                                class="btn btn-success"
                                @click="completeActivity(activity, index)"
                            >
                                Complete Activity
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
import { ref, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { scrollToTop } from '@/utils/scrollHelper'

const router = useRouter()
const activeIndex = ref(null)

const toggleActivity = async (index) => {
    activeIndex.value = activeIndex.value === index ? null : index
    await nextTick()
}

const completeActivity = async (activity, index) => {
    alert(
        `✅ You rated "${activity.title}" — Reflection: ${activity.reflectionRating}/5, Activity: ${activity.activityRating}/5`
    )
    if (activeIndex.value === index) activeIndex.value = null
    await nextTick()
    scrollToTop()
}

const goBack = () => router.push('/self-enhancement')

// Helper to detect steps that require user input
const needsInput = (text) => {
    const keywords = ['write', 'note', 'record', 'list', 'jot', 'describe']
    return keywords.some((word) => text.toLowerCase().includes(word))
}

// All 10 activities included
const activities = ref([
    {
        title: 'The Traffic Jam Test',
        imagine: 'You’re stuck in traffic and feel irritated.',
        steps: [
            'Notice your first thought - “This always happens to me!”',
            'Say to yourself: “It’s not the road; it’s my thought reacting.”',
            'Take one slow breath, look around, and name one neutral thing you see.'
        ],
        reflection: '“How much did noticing the thought reduce my irritation?” (1-5)',
        purpose: 'Separates event from reaction; teaches perception control.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Empty Cup Moment',
        imagine: 'You pour tea or coffee and it spills.',
        steps: [
            'Before blaming yourself, pause and smile. Say, “It’s just a spill, not proof I’m clumsy.”',
            'Wipe it slowly while breathing evenly.',
            'Note what thought came up first.'
        ],
        reflection: '“Did I stay calm after the spill?” (1-5)',
        purpose: 'Shows how thoughts label accidents as “personal failure.”',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Phone Message Pause',
        imagine: 'Someone leaves your message on ‘read’.',
        steps: [
            'Notice the instant story your mind tells - “They’re ignoring me.”',
            'Say instead: “That’s one possible story, not the only one.”',
            'Write a calmer alternative story.'
        ],
        reflection: '“Did postponing judgment change how I felt?” (1-5)',
        purpose: 'Loosens quick negative interpretations.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Mirror Check',
        imagine: 'Before work or bed, look in the mirror for 20 seconds.',
        steps: [
            'Ask: “What’s one thought running through my head right now?”',
            'Label it: useful / not useful.',
            'Write it down briefly.'
        ],
        reflection: '“How clear am I about my thoughts right now?” (1-5)',
        purpose: 'Builds metacognitive awareness in a neutral daily habit.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Missed Bus Thought',
        imagine: 'You miss your transport and feel upset.',
        steps: [
            'Ask, “What am I actually reacting to — delay or my fear of being late?”',
            'Breathe once, plan your next action calmly.'
        ],
        reflection: '“Was the emotion stronger than the event?” (1-5)',
        purpose: 'Highlights over-thinking vs. real issue.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Three-Minute Window',
        imagine: 'Anytime you feel pressure.',
        steps: [
            'Sit still, close eyes, and notice your thoughts like passing headlines.',
            'Don’t fix or judge them.',
            'After 3 minutes ask: “Which one really needs my attention?”',
            'Note your main observation.'
        ],
        reflection: '“Did my mind quiet down?” (1-5)',
        purpose: 'Teaches detached observation — key to self-regulation.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Two-Angle View',
        imagine: 'You had an argument.',
        steps: [
            'Replay the scene once from your view, once from the other person’s.',
            'Notice what changes.',
            'Write one insight that felt different.'
        ],
        reflection: '“Did seeing both sides soften my feeling?” (1-5)',
        purpose: 'Converts rigid thought to flexible understanding.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Gratitude Reframe',
        imagine: 'When you complain about a routine task.',
        steps: [
            'Pause and find one small benefit in doing it.',
            'Say it aloud: “This keeps my space clean / keeps me fit.”',
            'Note how your emotion changed.'
        ],
        reflection: '“Did I feel lighter after reframing?” (1-5)',
        purpose: 'Shifts focus from burden → value.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Label Swap',
        imagine: 'You catch yourself thinking “I’m lazy / unlucky / bad.”',
        steps: [
            'Replace the label with a descriptive fact: “I postponed it today.”',
            'Facts are changeable; labels aren’t.',
            'Write that new fact below.'
        ],
        reflection: '“Did the new description feel kinder?” (1-5)',
        purpose: 'Builds self-compassion and realistic self-talk.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    },
    {
        title: 'The Night-End Replay',
        imagine: 'Before sleep.',
        steps: [
            'Recall one tense moment of the day.',
            'Ask: What happened? What did I think? What was another way to think?',
            'Note a one-line learning.'
        ],
        reflection: '“Do I understand myself better now?” (1-5)',
        purpose: 'Daily insight loop reinforcing flexible thinking.',
        reflectionRating: 0,
        activityRating: 0,
        inputs: []
    }
])
</script>

<style scoped>
h1 {
    font-weight: 700;
    letter-spacing: 0.5px;
}
.accordion-button {
    background-color: #f8f9fa;
}
.accordion-button:not(.collapsed) {
    background-color: #e9ecef;
    color: #000;
}
.accordion-body ul {
    list-style-type: disc;
    margin-left: 1.5rem;
}
.btn-success {
    font-weight: 600;
    padding: 0.6rem 1.5rem;
}
.rating-section .stars .bi {
    transition: transform 0.2s ease;
}
.rating-section .stars .bi:hover {
    transform: scale(1.2);
}
.cursor-pointer {
    cursor: pointer;
}
textarea.form-control {
    resize: none;
}
</style>
