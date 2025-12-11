<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Mental Status Examination (MSE)</h4>

        <div class="row g-3">
            <div
                v-for="(section, index) in sections"
                :key="index"
                class="col-12 col-lg-6"
            >
                <!-- Section Button -->
                <div class="text-center mb-3">
                    <button
                        class="btn btn-accent w-100"
                        @click="toggleSection(index)"
                    >
                        {{ section.name }}
                    </button>
                </div>

                <!-- Questions appear below the button -->
                <div v-if="section.show" class="card shadow-sm mb-4">
                    <div class="card-header bg-accent">
                        <h5 class="mb-0">{{ section.name }}</h5>
                    </div>

                    <div class="card-body">
                        <!-- <p class="small text-muted mb-3">
                            Instructions: Select the option that best describes your current state.
                        </p> -->

                        <div
                            v-for="(question, qIndex) in section.questions"
                            :key="qIndex"
                            class="mb-3"
                        >
                            <p class="fw-semibold">{{ question.text }}</p>
                            <div class="d-flex flex-column ms-3">
                                <label
                                    v-for="(option, oIndex) in question.options"
                                    :key="oIndex"
                                    class="form-check mb-1"
                                >
                                    <input
                                        type="radio"
                                        class="form-check-input me-2"
                                        :name="`q-${index}-${qIndex}`"
                                        :value="option.value"
                                        v-model="question.answer"
                                    />
                                    {{ option.text }}
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-3">
                            <button
                                class="btn btn-success"
                                @click="calculateScore(section)"
                            >
                                Submit Assessment
                            </button>
                        </div>

                        <!-- Score Display -->
                        <div v-if="section.result" class="alert alert-info mt-3">
                            <strong>Total C answers:</strong> {{ section.result.total }} <br />
                            <strong>Interpretation:</strong> {{ section.result.interpretation }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'

const sections = reactive([
    {
        name: 'Appearance',
        show: false,
        questions: [
            {
                text: 'How would you describe your grooming and hygiene?',
                answer: null,
                options: [
                    { text: 'I feel I am not well-groomed.', value: 'A' },
                    { text: 'My grooming is somewhat appropriate, but I could improve it.', value: 'B' },
                    { text: 'I am well-groomed and dressed appropriately for the situation.', value: 'C' },
                    { text: 'I feel like I am overly dressed or overly casual for the situation.', value: 'D' },
                ],
            },
            {
                text: 'How do you feel about your clothing?',
                answer: null,
                options: [
                    { text: 'My clothes are dirty or inappropriate.', value: 'A' },
                    { text: 'My clothes are somewhat suitable but could be better.', value: 'B' },
                    { text: 'I am wearing clean and appropriate clothing for the situation.', value: 'C' },
                    { text: 'I feel uncomfortable or overdressed.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Behavior',
        show: false,
        questions: [
            {
                text: 'How would you describe your overall behaviour today?',
                answer: null,
                options: [
                    { text: 'I feel very restless or agitated.', value: 'A' },
                    { text: 'I feel a little nervous or uneasy.', value: 'B' },
                    { text: 'I feel calm and relaxed.', value: 'C' },
                    { text: 'I feel unusually calm or overly controlled.', value: 'D' },
                ],
            },
            {
                text: 'How aware are you of unusual movements or restlessness?',
                answer: null,
                options: [
                    { text: 'I feel a lot of involuntary movements (e.g., twitching, shaking).', value: 'A' },
                    { text: 'I occasionally feel some restlessness or fidgeting.', value: 'B' },
                    { text: 'I feel at ease and do not notice any unusual movements.', value: 'C' },
                    { text: 'I feel very still or rigid in my movements.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Speech',
        show: false,
        questions: [
            {
                text: 'How would you describe your speech?',
                answer: null,
                options: [
                    { text: 'I find it hard to speak or stutter a lot.', value: 'A' },
                    { text: 'My speech is somewhat unclear, and I struggle with my words.', value: 'B' },
                    { text: 'I speak clearly and at a normal pace.', value: 'C' },
                    { text: 'I speak too fast or too slowly for the situation.', value: 'D' },
                ],
            },
            {
                text: 'How would you rate the tone and volume of your speech?',
                answer: null,
                options: [
                    { text: 'My voice is very quiet or too loud.', value: 'A' },
                    { text: 'My voice is somewhat inappropriate in terms of volume or tone.', value: 'B' },
                    { text: 'My tone and volume are normal.', value: 'C' },
                    { text: 'I feel that my tone is too harsh or monotone.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Mood and Affect',
        show: false,
        questions: [
            {
                text: 'How would you describe your mood?',
                answer: null,
                options: [
                    { text: 'I feel very sad, anxious, or irritable.', value: 'A' },
                    { text: 'I feel a little down or stressed.', value: 'B' },
                    { text: 'I feel stable, happy, and in control.', value: 'C' },
                    { text: 'I feel unusually euphoric or overly happy.', value: 'D' },
                ],
            },
            {
                text: 'How would you describe your emotional expression?',
                answer: null,
                options: [
                    { text: 'I feel like my emotions are very mismatched with my expressions.', value: 'A' },
                    { text: 'My expressions are slightly different from how I feel inside.', value: 'B' },
                    { text: 'My emotions and expressions match well.', value: 'C' },
                    { text: 'My expressions are more intense than how I feel inside.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Thought Process',
        show: false,
        questions: [
            {
                text: 'How would you describe your thought process?',
                answer: null,
                options: [
                    { text: 'My thoughts feel disorganised or jumbled.', value: 'A' },
                    { text: 'My thoughts are somewhat clear but still scattered.', value: 'B' },
                    { text: 'My thoughts are logical and easy to follow.', value: 'C' },
                    { text: 'My thoughts are highly focused and precise.', value: 'D' },
                ],
            },
            {
                text: 'How often do you experience distracting or intrusive thoughts?',
                answer: null,
                options: [
                    { text: 'I frequently experience intrusive thoughts that interrupt my focus.', value: 'A' },
                    { text: 'I occasionally find myself distracted by unrelated thoughts.', value: 'B' },
                    { text: 'I am able to focus well without distractions.', value: 'C' },
                    { text: 'I am extremely focused and rarely have any distractions.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Thought Content',
        show: false,
        questions: [
            {
                text: 'How often do you experience unusual or obsessive thoughts?',
                answer: null,
                options: [
                    { text: 'I have frequent, unwanted, or obsessive thoughts.', value: 'A' },
                    { text: 'I sometimes experience unusual thoughts but they don’t worry me.', value: 'B' },
                    { text: 'I don’t experience any unusual or obsessive thoughts.', value: 'C' },
                    { text: 'I am preoccupied with unrealistic or bizarre thoughts.', value: 'D' },
                ],
            },
            {
                text: 'How safe do you feel from harm?',
                answer: null,
                options: [
                    { text: 'I feel unsafe or have thoughts of harming myself or others.', value: 'A' },
                    { text: 'I feel uneasy but not unsafe.', value: 'B' },
                    { text: 'I feel completely safe.', value: 'C' },
                    { text: 'I feel overly cautious or paranoid about safety.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Perception',
        show: false,
        questions: [
            {
                text: 'Do you experience any sensory perceptions that others do not?',
                answer: null,
                options: [
                    { text: 'I hear voices or see things others do not.', value: 'A' },
                    { text: 'I sometimes hear noises or see things that aren’t real.', value: 'B' },
                    { text: 'I do not experience any hallucinations.', value: 'C' },
                    { text: 'I feel hyper-aware of my senses, but they seem normal.', value: 'D' },
                ],
            },
            {
                text: 'How accurate are your perceptions of reality?',
                answer: null,
                options: [
                    { text: 'I often misinterpret what I see or hear.', value: 'A' },
                    { text: 'I occasionally misinterpret things, but it doesn’t worry me.', value: 'B' },
                    { text: 'I perceive things accurately, and my senses are reliable.', value: 'C' },
                    { text: 'I feel overly sensitive or detached from reality at times.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Cognition',
        show: false,
        questions: [
            {
                text: 'How alert do you feel during the day?',
                answer: null,
                options: [
                    { text: 'I feel very tired or drowsy and can’t stay focused.', value: 'A' },
                    { text: 'I sometimes feel sleepy or inattentive.', value: 'B' },
                    { text: 'I feel alert and engaged.', value: 'C' },
                    { text: 'I feel unusually energised or hyper-alert.', value: 'D' },
                ],
            },
            {
                text: 'How well can you concentrate on tasks?',
                answer: null,
                options: [
                    { text: 'I have difficulty concentrating and staying focused.', value: 'A' },
                    { text: 'I sometimes struggle with concentration.', value: 'B' },
                    { text: 'I can easily focus on tasks and conversations.', value: 'C' },
                    { text: 'I can focus with exceptional clarity for long periods.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Insight and Judgment',
        show: false,
        questions: [
            {
                text: 'How well do you understand the challenges you’re currently facing?',
                answer: null,
                options: [
                    { text: 'I have little understanding of my challenges.', value: 'A' },
                    { text: 'I have some understanding but feel unsure about them.', value: 'B' },
                    { text: 'I understand my challenges and am actively addressing them.', value: 'C' },
                    { text: 'I have deep insight and clarity about my situation.', value: 'D' },
                ],
            },
            {
                text: 'How confident are you in making decisions?',
                answer: null,
                options: [
                    { text: 'I feel unsure or confused when making decisions.', value: 'A' },
                    { text: 'I sometimes struggle with decisions.', value: 'B' },
                    { text: 'I feel confident in making everyday decisions.', value: 'C' },
                    { text: 'I make decisions quickly and confidently.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
    {
        name: 'Memory',
        show: false,
        questions: [
            {
                text: 'How well can you recall recent events or conversations?',
                answer: null,
                options: [
                    { text: 'I struggle to remember recent events.', value: 'A' },
                    { text: 'I remember some recent events, but not clearly.', value: 'B' },
                    { text: 'I can recall recent events with good clarity.', value: 'C' },
                    { text: 'I remember recent events with remarkable detail.', value: 'D' },
                ],
            },
            {
                text: 'How well do you remember past events or important details from your life?',
                answer: null,
                options: [
                    { text: 'I have trouble recalling past events.', value: 'A' },
                    { text: 'I can recall some past events, but details are hazy.', value: 'B' },
                    { text: 'I remember my past clearly and accurately.', value: 'C' },
                    { text: 'I remember my past in vivid detail, almost like it happened yesterday.', value: 'D' },
                ],
            },
        ],
        result: null,
    },
])

function toggleSection(index) {
    sections[index].show = !sections[index].show
}

function calculateScore(section) {
    const total = section.questions.reduce(
        (sum, q) => sum + (q.answer === 'C' ? 1 : 0),
        0
    )
    let interpretation = ''
    if (total >= section.questions.length) {
        interpretation = 'High Scores: Suggest healthy mental functioning (more C answers).'
    } else if (total >= section.questions.length / 2) {
        interpretation = 'Moderate Scores: Areas for reflection or mild concern (mix of B and C answers).'
    } else {
        interpretation = 'Low Scores: Areas needing attention or professional evaluation (more A answers).'
    }
    section.result = { total, interpretation }
}
</script>
