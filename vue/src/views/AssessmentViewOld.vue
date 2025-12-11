<template>
    <div class="container py-4">
        <div class="row g-4 align-items-start">
            <!-- Physical Interventions -->
            <div class="col-6 text-center">
                <div class="border rounded p-4 hover-box mb-3" @click="router.push('/physical/qna')">
                    <div>Physical Health</div>
                </div>
<!-- <h6 class="fw-semibold text-start mb-2">{{ index + 1 }}. {{ video.title }}</h6> -->
                <!-- <div class="video-section">
                    <div
                        v-for="(video, index) in physicalVideos"
                        :key="index"
                        class="mb-4"
                    >
                        
                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                            <iframe
                                :src="video.url"
                                frameborder="0"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Mental Interventions -->
            <div class="col-6 text-center">
                <div class="border rounded p-4 hover-box mb-3" @click="router.push('/mental/qna')">
                    <div>Mental &nbsp;&nbsp;Health</div>
                </div>
<!-- <h6 class="fw-semibold text-start mb-2">{{ index + 1 }}. {{ video.title }}</h6> -->
                <!-- <div class="video-section ">
                    <div
                        v-for="(video, index) in mentalVideos"
                        :key="index"
                        class="mb-4"
                    >
                        
                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                            <iframe
                                :src="video.url"
                                frameborder="0"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import { useRouter } from 'vue-router'

    const router = useRouter();

    const physicalVideos = [
        { title: 'Surya Anulom Vilom', url: 'https://www.youtube.com/embed/5Be62_oZjyo' },
    ]

    const mentalVideos = [
        { title: 'Understanding Mind and Mental Health', url: 'https://www.youtube.com/embed/IyqLDC4US6Y?si=JxfAOoLMtt8YPezP' },
    ];
</script>

<style scoped>
    .hover-box {
        cursor: pointer;
        transition: 0.3s ease;
    }
    .hover-box:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
    }

    .video-section {
        max-height: 75vh;
        overflow-y: auto;
        padding-right: 6px;
    }

    .video-section::-webkit-scrollbar {
        width: 6px;
    }
    .video-section::-webkit-scrollbar-thumb {
        background-color: #bbb;
        border-radius: 3px;
    }
   
</style>
