<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Resilience Activities</h1>

        <div class="accordion" id="resilienceAccordion">
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
                        <h6 class="fw-bold mb-2 text-primary">
                            Imagine the Situation:
                        </h6>
                        <p>{{ activity.situation }}</p>

                        <h6 class="fw-bold mb-2 text-primary">
                            Steps to Practice:
                        </h6>
                        <ul>
                            <li
                                v-for="(step, sIndex) in activity.steps"
                                :key="sIndex"
                            >
                                {{ step }}

                                <!-- Input prompt appears if step has 'write' or 'record' -->
                                <div
                                    v-if="containsPrompt(step)"
                                    class="mt-2"
                                >
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="activity.responses[sIndex]"
                                        placeholder="Write your response..."
                                        @input="saveLocal()"
                                    />
                                </div>
                            </li>
                        </ul>

                        <h6 class="fw-bold mb-2 text-primary">
                            Reflection Question:
                        </h6>
                        <p><em>{{ activity.reflection }}</em></p>

                        <!-- Reflection Star Rating (changed to critical-thinking style) -->
                        <div class="feedback-stars text-center mb-4">
                            <h6 class="text-dark mb-2">How do you feel?</h6>
                            <div class="stars">
                                <i
                                    v-for="n in 5"
                                    :key="'reflect-' + n"
                                    class="bi star-icon"
                                    :class="n <= activity.reflectionRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                    @click="setRating(activity, 'reflectionRating', 0)"
                                    role="button"
                                    aria-label="reflection star"
                                    style="font-size: 1.8rem; cursor: pointer;"
                                ></i>
                            </div>
                            <div class="small text-muted mt-1">
                                Your rating: {{ activity.reflectionRating }} / 5
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2 text-primary">Purpose:</h6>
                        <p>{{ activity.purpose }}</p>

                        <!-- Activity Star Rating (changed to critical-thinking style) -->
                        <div class="feedback-stars text-center mt-3">
                            <h6 class="text-dark mb-2">Rate this Activity:</h6>
                            <div class="stars">
                                <i
                                    v-for="n in 5"
                                    :key="'activity-' + n"
                                    class="bi star-icon"
                                    :class="n <= activity.activityRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                    @click="setRating(activity, 'activityRating', 0)"
                                    role="button"
                                    aria-label="activity star"
                                ></i>
                            </div>
                            <div class="small text-muted mt-1">
                                Your rating: {{ activity.activityRating }} / 5
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeIndex = ref(null)

const toggleActivity = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index
}

const setRating = (activity, key, n) => {
    activity[key] = n
    saveLocal()
}

// detect text that needs prompt
const containsPrompt = (text) => {
    const keywords = ['write', 'record', 'say it aloud']
    return keywords.some((word) => text.toLowerCase().includes(word))
}

const completeActivity = (activity, index) => {
    alert(
        `✅ You completed "${activity.title}"\nReflection Rating: ${activity.reflectionRating}/5\nActivity Rating: ${activity.activityRating}/5`
    )

    if (activeIndex.value === index) {
        activeIndex.value = null
    }
    saveLocal()
}

const goBack = () => {
    router.push('/self-enhancement')
}

const defaultActivities = [
    {
        title: 'Start Calm - Morning Grounding',
        situation:
            'You wake up late, your phone keeps ringing, and you feel tense even before the day begins.',
        steps: [
            'Sit comfortably.',
            'Take five slow, deep breaths.',
            'Notice 3 things you can see, 2 things you can hear, and 1 thing you can feel.',
            'Stay still for 30 seconds before getting up.',
        ],
        reflection: '“How calm do you feel now?” (Rate 1-5)',
        purpose:
            'To begin the day with clarity, balance, and reduced morning anxiety.',
    },
    {
        title: 'Pause Before Reacting',
        situation:
            'A civilian or colleague speaks rudely, and you feel anger rising.',
        steps: [
            'Take a deep breath and silently count 1… 2… 3.',
            'Relax your shoulders before replying.',
            'Think: “What response will help me, not harm me?”',
            'Then respond calmly.',
        ],
        reflection: '“Did pausing help you stay in control?” (Rate 1-5)',
        purpose:
            'To train your brain to respond wisely instead of reacting impulsively.',
    },
    {
        title: 'One Good Thing - Gratitude Practice',
        situation: 'You’ve had a busy, stressful day and feel drained.',
        steps: [
            'Think of one small thing that went well today - even something simple.',
            'Write it in the app journal or say it aloud.',
            'Smile for a moment and thank yourself for handling the day.',
        ],
        reflection:
            '“How much lighter do you feel after recalling that?” (Rate 1-5)',
        purpose:
            'To focus on positives and shift the mind from exhaustion to appreciation.',
    },
    {
        title: 'Walk to Recharge',
        situation:
            'You’ve been standing or sitting for hours - your body feels heavy, and your focus is fading.',
        steps: [
            'Take a 10-minute walk, even around your station or home.',
            'Avoid checking your phone.',
            'Focus on your steps and breathing.',
            'End by stretching your shoulders and neck.',
        ],
        reflection: '“How refreshed do you feel now?” (Rate 1-5)',
        purpose:
            'To renew energy and bring mental alertness through mindful movement.',
    },
    {
        title: 'What Went Well - Reflective Ending',
        situation:
            'Your shift is over. You’re replaying the day’s challenges in your mind.',
        steps: [
            'Think of two things that went well today.',
            'Identify one thing you’ll handle differently tomorrow.',
            'Note them in your reflection log.',
        ],
        reflection:
            '“How confident are you about managing tomorrow better?” (Rate 1-5)',
        purpose:
            'To build self-awareness and learn from each day without self-criticism.',
    },
    {
        title: 'Buddy Connect - Support Circle',
        situation: 'You feel lonely, tired, or emotionally disconnected.',
        steps: [
            'Call, message, or meet a friend, colleague, or family member.',
            'Share something positive or simply ask how they are.',
            'End with a few minutes of laughter or light conversation.',
        ],
        reflection:
            '“Did connecting with someone improve your mood?” (Rate 1-5)',
        purpose:
            'To build emotional resilience through meaningful human connection.',
    },
    {
        title: '3-3-3 Reset',
        situation:
            'You feel tense, anxious, or distracted while handling multiple tasks.',
        steps: [
            'Name 3 things you see around you.',
            'Name 3 sounds you can hear.',
            'Move 3 body parts (neck, shoulders, fingers).',
            'Take one deep breath.',
        ],
        reflection: '“How calm do you feel after this reset?” (Rate 1-5)',
        purpose:
            'To quickly ground yourself and bring your focus back to the present.',
    },
    {
        title: 'Humour Break – Laughter Dose',
        situation: 'You’re feeling irritated, serious, or mentally exhausted.',
        steps: [
            'Watch a short funny video or recall a humorous moment.',
            'Smile or laugh freely for at least 20 seconds.',
            'Take a deep breath and notice your mood.',
        ],
        reflection:
            '“How much better do you feel after laughing?” (Rate 1-5)',
        purpose:
            'To use laughter as a natural stress buster and mood lifter.',
    },
    {
        title: 'Small Help, Big Change - Kindness Act',
        situation:
            'You notice a colleague struggling or a junior officer upset.',
        steps: [
            'Offer a kind word, a small help, or simple encouragement.',
            'Do it silently - without expecting thanks.',
            'Notice how it makes you feel afterward.',
        ],
        reflection: '“How satisfying was that act of kindness?” (Rate 1-5)',
        purpose:
            'To strengthen empathy and sense of purpose through simple daily kindness.',
    },
    {
        title: 'Digital Detox - Sleep Well',
        situation:
            'It’s late night, and you’re scrolling through your phone instead of sleeping.',
        steps: [
            'Keep your phone aside 30 minutes before sleep.',
            'Do slow breathing or stretch lightly.',
            'Close your eyes and recall one peaceful memory.',
        ],
        reflection: '“How relaxed do you feel before sleep?” (Rate 1-5)',
        purpose: 'To improve sleep quality and promote mental recovery.',
    },
]

const activities = ref([])

const saveLocal = () => {
    localStorage.setItem('resilienceActivities', JSON.stringify(activities.value))
}

onMounted(() => {
    const saved = localStorage.getItem('resilienceActivities')
    activities.value = saved
        ? JSON.parse(saved)
        : defaultActivities.map((a) => ({
              ...a,
              reflectionRating: 0,
              activityRating: 0,
              responses: [],
          }))
})
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

/* STAR style matching Critical Thinking: outlined stars that fill yellow when selected */
.stars {
    display: flex;
    justify-content: center;
    /* gap: 0.4rem;*/
}

.star-icon {
    font-size: 1.8rem;
    cursor: pointer;
    transition: transform 0.15s ease, color 0.15s ease;
    color: #6c757d; 
}

.star-icon:hover {
    transform: scale(1.15);
}

/* filled star color */
.text-warning {
    color: #ffbf00 !important;
}

/* keep a subtle scale for selected stars */
.bi-star-fill {
    transform: scale(1.12);
}

/* small input width */
.form-control {
    max-width: 400px;
}
</style>
