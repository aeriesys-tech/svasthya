<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Anger Management Activities</h1>

        <div class="accordion" id="angerAccordion">
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
                        <h6 class="fw-bold mb-2 text-dark">Imagine the Situation:</h6>
                        <p>{{ activity.imagine }}</p>

                        <h6 class="fw-bold mb-2 text-dark">Practice:</h6>
                        <ul>
                            <li v-for="(step, sIndex) in activity.practice" :key="sIndex">
                                {{ step }}
                            </li>
                        </ul>

                        <h6 class="fw-bold mb-2 text-dark">Reflection Question:</h6>
                        <p><em>{{ activity.reflection }}</em></p>

                        <!-- Reflection Star Rating -->
                        <div class="feedback-stars text-center mb-4">
                            <h6 class="text-dark mb-2">Rate your reflection:</h6>
                            <div class="stars">
                                <i
                                    v-for="n in 5"
                                    :key="'reflect-' + n"
                                    class="bi star-icon"
                                    :class="n <= activity.reflectionRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                    @click="setRating(activity, 'reflectionRating', n)"
                                    role="button"
                                ></i>
                            </div>
                            <div class="small text-muted mt-1">
                                Your rating: {{ activity.reflectionRating }} / 5
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2 text-dark mt-3">Purpose:</h6>
                        <p>{{ activity.purpose }}</p>

                        <!-- Activity Star Rating -->
                        <div class="feedback-stars text-center mt-3">
                            <h6 class="text-dark mb-2">Rate this Activity:</h6>
                            <div class="stars">
                                <i
                                    v-for="n in 5"
                                    :key="'activity-' + n"
                                    class="bi star-icon"
                                    :class="n <= activity.activityRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                    @click="setRating(activity, 'activityRating', n)"
                                    role="button"
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
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeIndex = ref(null)

const toggleActivity = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index
}

const setRating = (activity, key, value) => {
    activity[key] = value
}

const completeActivity = (activity, index) => {
    alert(
        `✅ You completed "${activity.title}"
Reflection Rating: ${activity.reflectionRating}/5
Activity Rating: ${activity.activityRating}/5`
    )

    if (activeIndex.value === index) {
        activeIndex.value = null
    }
}

const goBack = () => {
    router.push('/activity')
}

const activities = ref([
    {
        title: '3-Breath Pause - Calm Before Response',
        imagine: 'A citizen argues loudly during traffic inspection.',
        practice: [
            'Take 3 deep breaths — inhale through the nose, exhale through the mouth slowly.',
        ],
        reflection: '“Did I pause before reacting?” (Rate 1-5)',
        purpose: 'Train immediate emotional control under provocation.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Name the Feeling - Know Your Anger',
        imagine: 'You feel irritated but don’t know why — maybe hunger, fatigue, or workload.',
        practice: [
            'Identify what emotion is strongest: anger, frustration, helplessness, or tiredness.',
            'Say it mentally: “I’m feeling frustrated because…”',
        ],
        reflection: '“Could I identify my feelings before reacting?” (Rate 1-5)',
        purpose: 'Increase emotional awareness and insight.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Step Away - The Cool Down Move',
        imagine: 'A heated argument with a subordinate or colleague.',
        practice: [
            'Politely excuse yourself: “Give me a minute.”',
            'Step aside, drink water, stretch shoulders.',
            'Return after 2 minutes.',
        ],
        reflection: '“Did a short break help calm me down?” (Rate 1-5)',
        purpose: 'Reinforce physical and emotional reset.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Write, Don’t Fight - Journal the Trigger',
        imagine: 'After a stressful day, you replay the argument in your mind.',
        practice: [
            'Write a few lines: What triggered me? What helped me calm down?',
            'Note one thing you’ll try next time.',
        ],
        reflection: '“Did writing reduce the intensity of anger?” (Rate 1-5)',
        purpose: 'Convert emotion into reflection and learning.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Perspective Shift - The Other Side View',
        imagine: 'A citizen refuses to cooperate or blames you unfairly.',
        practice: [
            'Ask yourself: “What might they be going through?”',
            'Reframe: “Maybe they’re scared or stressed.”',
        ],
        reflection: '“Was I able to empathize instead of react?” (Rate 1-5)',
        purpose: 'Develop empathy and reduce personalization of conflict.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Humor Break - Lighten the Moment',
        imagine: 'A tense office situation with rising tempers.',
        practice: [
            'Share a light, harmless joke or make a friendly comment.',
            'Smile genuinely.',
        ],
        reflection: '“Did humor diffuse tension?” (Rate 1-5)',
        purpose: 'Use positive emotions to restore balance.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Mirror Check - Body Language Awareness',
        imagine: 'You are giving instructions sternly and notice your voice is harsh.',
        practice: [
            'Observe your tone, face, and posture in the mirror or mentally visualize it.',
            'Relax your shoulders and soften your tone.',
        ],
        reflection: '“Was my body language calm and respectful?” (Rate 1-5)',
        purpose: 'Promote mindful communication and composure.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Grounding Reset - 5-4-3-2-1 Technique',
        imagine: 'You are angry during a meeting or patrol.',
        practice: [
            'Name 5 things you can see, 4 you can touch, 3 you can hear, 2 you can smell, 1 you can taste.',
        ],
        reflection: '“How grounded did I feel after this?” (Rate 1-5)',
        purpose: 'Break the emotional surge and anchor in the present moment.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Replace the Word - Self-Talk Control',
        imagine: 'You’re thinking, “This always happens to me!”',
        practice: [
            'Replace that thought with: “I can handle this calmly this time.”',
        ],
        reflection: '“Did positive self-talk change how I felt?” (Rate 1-5)',
        purpose: 'Rewire negative thoughts into calming affirmations.',
        reflectionRating: 0,
        activityRating: 0,
    },
    {
        title: 'Gratitude Reset – End the Day Lightly',
        imagine: 'You’re replaying frustrating incidents after shift.',
        practice: [
            'Think of 2 things that went right today.',
            'Say to yourself: “Despite the challenges, I handled myself better.”',
        ],
        reflection: '“How relieved do I feel after thinking of positives?” (Rate 1-5)',
        purpose: 'Release daily tension and maintain emotional balance.',
        reflectionRating: 0,
        activityRating: 0,
    },
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

/* STAR styles (same as resilience activities) */
.stars {
    display: flex;
    justify-content: center;
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

.text-warning {
    color: #ffbf00 !important;
}

.bi-star-fill {
    transform: scale(1.12);
}
</style>
