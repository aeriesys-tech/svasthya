<template>
    <div class="container py-4 blog-page">
        <h2 class="text-center mb-4">Anger Management</h2>

        <div class="video-container mt-3 rounded">
            <iframe width="560" height="200" 
                src="https://www.youtube.com/embed/a8t5SwmqDnk?si=NkGfev4jtFeBWf6X" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
            </iframe>
        </div>

        <section v-for="(section, index) in sections" :key="index" class="mb-4 mt-3">
            <h5 class="fw-bold mb-2">{{ index + 1 }}. {{ section.title }}</h5>

            <p v-if="typeof section.content === 'string'">
                {{ section.content }}
            </p>

            <ul v-else class="ps-3">
                <li v-for="(item, i) in section.content" :key="i">{{ item }}</li>
            </ul>
        </section>

        <div class="text-center mt-5">
            <button class="btn btn-success rounded-pill px-4" @click="">
                Complete
            </button>
            <button class="btn btn-success rounded-pill px-4 ms-2" @click="goToQuestionnaire">
                <i class="bi bi-arrow-right"></i>
                Post Test
            </button>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function goToQuestionnaire() {
    router.push({ path: '/anger/questionnaire', query: { fromInfo: '1' } })
}

const sections = [
    {
        title: 'Introduction',
        content: "Anger is a normal emotion, like spice in food. But too much, and it ruins the dish... or the day!"
    },
    {
        title: 'Common Causes',
        content: [
            'Feeling disrespected',
            'Unfair situations (common in police work!)',
            'Delayed justice',
            'Fatigue and stress'
        ]
    },
    {
        title: 'Triggers',
        content: [
            'Loud noise',
            'Traffic violations',
            'Being questioned unnecessarily',
            'Lack of recognition'
        ]
    },
    {
        title: 'Demerits of Uncontrolled Anger',
        content: [
            'Relationship breakdown',
            'Wrong decisions',
            'Health problems (BP, ulcers, headaches)',
            'Bad reputation at work',
            'Self-blame and guilt'
        ]
    },
    {
        title: 'Understanding Yourself',
        content: [
            'When do I get most angry?',
            'Do I attack, avoid, or address my anger?',
            'What’s beneath my anger and fear? stress? disappointment?'
        ]
    },
    {
        title: 'Strategies',
        content: [
            'Breathe deeply: 5 slow breaths calm the fight-or-flight response.',
            'Walk away (literally): Change your scene.',
            'Talk it out: Not shout it out.',
            'Use humour: Sometimes, imagining the situation in a cartoon version works!',
            'Use "I" statements: "I feel upset when…" instead of "You always…"'
        ]
    },
    {
        title: 'Conclusion',
        content: "You are not your anger. You can train your mind like you train your body. Anger is power - when managed, not exploded."
    }
]
</script>

<style scoped>
.blog-page {
    line-height: 1.7;
    font-size: 1rem;
}

ul li {
    margin-bottom: 0.5rem;
}

.video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    height: 0;
    overflow: hidden;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}
</style>
