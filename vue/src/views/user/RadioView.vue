<template>
    <div class="container py-4">
        <h4 class="mb-3">📻 Health & Wellness Radio</h4>

        <!-- Physical Health Section -->        
        <div
            v-for="(audio, index) in physicalAudios"
            :key="'physical-' + index"
            class="card mb-3 shadow-sm"
        >
            <div class="card-body">
                <h6 class="card-title mb-1">{{ audio.title }}</h6>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">by {{ audio.author }}</small>
                    <button class="btn btn-sm btn-outline-accent text-dark">{{ audio.type }}</button>
                </div>
                <audio class="w-100 mt-2" controls>
                    <source :src="audio.src" type="audio/mpeg" />
                    Your browser does not support the audio element.
                </audio>
            </div>
        </div>

        <!-- Mental Health Section -->
        <!-- <h5 class="mt-4 mb-3">🧠 Mental Health & Wellness</h5>
        <div
            v-for="(audio, index) in mentalAudios"
            :key="'mental-' + index"
            class="card mb-3 shadow-sm"
        >
            <div class="card-body">
                <h6 class="card-title mb-1">{{ audio.title }}</h6>
                <small class="text-muted">by {{ audio.author }}</small>
                <audio class="w-100 mt-2" controls>
                    <source :src="audio.src" type="audio/mpeg" />
                    Your browser does not support the audio element.
                </audio>
            </div>
        </div> -->
    </div>
</template>

<script setup>
const physicalAudios = [
    {
        title: 'Staying Active Indoors',
        author: 'Aetna Wellness',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
        type: 'Live'
    },
    {
        title: 'Healthy Sleep Habits',
        author: 'Wellness Central',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
        type: 'Live'
    },
    {
        title: 'Balanced Nutrition Basics',
        author: 'Health Foundation',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
        type: 'Recorded'
    },
    {
        title: 'Mindfulness for Beginners',
        author: 'Jigsaw Youth Support',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3',
        type: 'Recorded'
    },
    {
        title: 'Coping with Anxiety',
        author: 'Mental Health Alliance',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3',
        type: 'Recorded'
    },
    {
        title: 'Daily Gratitude Exercise',
        author: 'Calm Minds FM',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3',
        type: 'Recorded'
    }
]

const mentalAudios = [
    {
        title: 'Mindfulness for Beginners',
        author: 'Jigsaw Youth Support',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3'
    },
    {
        title: 'Coping with Anxiety',
        author: 'Mental Health Alliance',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3'
    },
    {
        title: 'Daily Gratitude Exercise',
        author: 'Calm Minds FM',
        src: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3'
    }
];
</script>
<style scoped>
.card {
    border-radius: 1rem;
}

.card-title {
    font-weight: 600;
}

</style>
