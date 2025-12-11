<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Psychological Disorders</h4>

        <div class="row g-3">
            <div
                v-for="(disorder, index) in disorders"
                :key="index"
                class="col-12 col-lg-6"
            >
                <div class="text-center mb-3">
                    <button
                        class="btn btn-accent w-100"
                        @click="toggleDisorder(index)"
                    >
                        {{ disorder.name }}
                    </button>
                </div>

                <!-- Disorder Questions appear below the button -->
                <div v-if="disorder.show" class="card shadow-sm mb-4">
                    <div class="card-header bg-accent">
                        <h5 class="mb-0">{{ disorder.name }}</h5>
                    </div>

                    <div class="card-body">
                        <div
                            v-for="(question, qIndex) in disorder.questions"
                            :key="qIndex"
                            class="mb-3"
                        >
                            <p class="fw-semibold">{{ question.text }}</p>
                            <div class="d-flex flex-column ms-3">
                                <label
                                    v-for="(label, n) in ratingOptions"
                                    :key="n"
                                    class="form-check mb-1"
                                >
                                    <input
                                        type="radio"
                                        class="form-check-input me-2"
                                        :name="`q-${index}-${qIndex}`"
                                        :value="n + 1"
                                        v-model.number="question.answer"
                                    />
                                    {{ label }}
                                </label>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button
                                class="btn btn-success"
                                @click="calculateScore(disorder)"
                            >
                                Submit Assessment
                            </button>
                        </div>

                        <div
                            v-if="disorder.result"
                            class="alert alert-info mt-3"
                        >
                            <strong>Total Score:</strong> {{ disorder.result.total }} <br />
                            <strong>Severity:</strong> {{ disorder.result.interpretation }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'

const ratingOptions = [
    'Never',
    'Rarely',
    'Sometimes',
    'Often',
    'Almost Always',
]

const disorders = reactive([
	{
		name: 'Mood Disorders',
		show: false,
		questions: [
			// Depressive Episode (F32)
			{ text: 'I feel sad or hopeless most of the time.', answer: null },
			{ text: 'I have little or no interest in activities I used to enjoy.', answer: null },
			{ text: 'I feel worthless or guilty, even when there is no reason to feel this way.', answer: null },
			// Manic Episode (F30)
			{ text: 'I feel unusually energetic or overly enthusiastic.', answer: null },
			{ text: 'My thoughts race or I find it difficult to slow my thinking down.', answer: null },
			{ text: 'I often engage in impulsive or reckless behaviors (e.g., overspending, risky actions).', answer: null },
		],
		result: null,
	},
	{
		name: 'Anxiety Disorders',
		show: false,
		questions: [
			// Generalised Anxiety Disorder (F41.1)
			{ text: 'I find myself worrying excessively about a variety of everyday issues (work, health, relationships).', answer: null },
			{ text: 'I feel physically tense or restless due to worry (muscle tightness, difficulty relaxing).', answer: null },
			// Panic Disorder (F41.0)
			{ text: 'I experience sudden, intense episodes of fear or discomfort (e.g., rapid heartbeat, difficulty breathing).', answer: null },
			{ text: 'I avoid certain situations or places because I fear having a panic attack.', answer: null },
		],
		result: null,
	},
	{
		name: 'Psychotic Disorders',
		show: false,
		questions: [
			// Schizophrenia (F20)
			{ text: 'I hear voices or perceive things that others do not hear or see.', answer: null },
			{ text: 'I have unusual beliefs or thoughts (e.g., believing I have special powers or that others are plotting against me).', answer: null },
			{ text: 'My speech or thoughts often feel disorganized or difficult to follow.', answer: null },
		],
		result: null,
	},
	{
		name: 'Obsessive-Compulsive Disorder(OCD)',
		show: false,
		questions: [
			{ text: 'I experience intrusive, unwanted thoughts or worries that cause distress.', answer: null },
			{ text: 'I engage in repetitive behaviors (e.g., washing, checking, or counting) to reduce anxiety or prevent something bad from happening.', answer: null },
			{ text: 'I feel unable to control or stop my compulsive behaviors, even though I may not want to do them.', answer: null },
		],
		result: null,
	},
	{
		name: 'Eating Disorders',
		show: false,
		questions: [
			// Anorexia Nervosa (F50.0)
			{ text: 'I am extremely concerned about my weight and body shape.', answer: null },
			{ text: 'I restrict my food intake in order to lose or maintain weight.', answer: null },
			// Bulimia Nervosa (F50.2)
			{ text: 'I experience episodes of overeating, followed by behaviors such as vomiting or excessive exercise to prevent weight gain.', answer: null },
			{ text: 'I feel extremely distressed or guilty after overeating or engaging in compensatory behaviors.', answer: null },
		],
		result: null,
	},
	{
		name: 'Substance-Related (Alcohol/Drugs)',
		show: false,
		questions: [
			// Alcohol Use Disorder (F10)
			{ text: 'I often drink more alcohol than I intended to.', answer: null },
			{ text: 'I have tried and failed to cut down or stop drinking alcohol.', answer: null },
			// Drug Use Disorder (F11–F19)
			{ text: 'I regularly use drugs in a way that negatively impacts my life or responsibilities.', answer: null },
			{ text: 'I have tried to reduce or stop using drugs but have been unsuccessful.', answer: null },
		],
		result: null,
	},
	{
		name: 'Personality Disorders',
		show: false,
		questions: [
			// Borderline Personality Disorder (F60.3)
			{ text: 'I experience intense and unstable emotions (e.g., extreme anger, sadness, or anxiety).', answer: null },
			{ text: 'I often engage in impulsive behaviors (e.g., reckless spending, self-harm).', answer: null },
			{ text: 'I have difficulty maintaining stable relationships and often feel abandoned or empty.', answer: null },
		],
		result: null,
	},
])

function toggleDisorder(index) {
    disorders[index].show = !disorders[index].show
}

function calculateScore(disorder) {
    const total = disorder.questions.reduce((sum, q) => sum + (q.answer || 0), 0)
    let interpretation = ''

    if (total <= 8) interpretation = 'Low severity of symptoms; no significant disorder likely.'
    else if (total <= 15) interpretation = 'Moderate severity of symptoms; may indicate a mild form of the disorder. Consider seeking a professional evaluation.'
    else interpretation = 'High severity of symptoms; likely to meet diagnostic criteria for the disorder. A professional assessment is strongly recommended.'

    disorder.result = { total, interpretation }
}
</script>
