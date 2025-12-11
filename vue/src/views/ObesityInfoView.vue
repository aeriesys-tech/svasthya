<template>
    <div class="container py-4 blog-page">
        <h2 class="text-center mb-4">Obesity Management</h2>

        <div class="video-container mt-3 rounded">
            <iframe width="560" height="200" 
                src="https://www.youtube.com/embed/zyn8rGboNfU?si=gPMmSWmlZeBNt8k5" 
                title="YouTube video player" 
                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
            </iframe>
        </div>

        <section v-for="(section, index) in sections" :key="index" class="mb-4 mt-3">
            <h5 class="fw-bold mb-2">{{ index + 1 }}. {{ section.title }}</h5>

            <!-- Paragraph Content -->
            <p v-if="typeof section.content === 'string'">
                {{ section.content }}
            </p>

            <!-- List Content -->
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
    router.push({ path: '/obesity/questionnaire', query: { fromInfo: '1' } }) // Adjust path as per your route file
}

// Content sections
const sections = [
    {
        title: 'Introduction',
        content: "Obesity is when your body has too much fat, more than it needs to function well. It can sneak in quietly, like that second samosa at tea time!"
    },
    {
        title: 'Common Causes',
        content: [
            'Overeating junk and sugary foods',
            'Sedentary lifestyle (too much sitting!)',
            'Stress-eating (comfort food is real!)',
            'Lack of exercise',
            "Genetics (but it's not an excuse for everything!)"
        ]
    },
    {
        title: 'Triggers',
        content: [
            'Boredom or stress (hello, emotional eating)',
            'Late-night snacking',
            'Skipping breakfast',
            'Long desk duties with little movement'
        ]
    },
    {
        title: 'Demerits of Obesity',
        content: [
            'High blood pressure',
            'Diabetes',
            'Back and knee pain (common in police work)',
            'Fatigue, sleep issues',
            'Lower confidence'
        ]
    },
    {
        title: 'Understanding Yourself',
        content: [
            'Am I eating to live, or living to eat?',
            'Is my job affecting my health habits?',
            'Am I fueling my body or just feeding my cravings?'
        ]
    },
    {
        title: 'Strategies',
        content: [
            'Follow the 3-2-1 rule: 3 meals, 2 snacks, 1 active hour a day.',
            'Hydrate before you overeat – your brain confuses thirst with hunger.',
            'Find a "buddy cop" to walk with!',
            'Switch samosas with sprouts (okay, maybe once a week!).'
        ]
    },
    {
        title: 'Conclusion',
        content: "You don’t need six-pack abs. You need energy, agility, and long-term health. Small steps. Consistent habits. Big results."
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
