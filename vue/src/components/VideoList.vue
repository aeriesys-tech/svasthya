<template>
    <div>
        <!-- Section Header -->
        <h4 class="mb-1">Videos</h4>
        <p class="text-muted mb-3">
            Watch expert sessions and wellbeing tips for better physical and mental health.
        </p>

        <!-- Toggle Pills -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button
                class="toggle-pill"
                :class="{ active: selectedCategory === 'physical' }"
                @click="selectedCategory = 'physical'"
            >
                Physical Health
            </button>
            <button
                class="toggle-pill"
                :class="{ active: selectedCategory === 'mental' }"
                @click="selectedCategory = 'mental'"
            >
                Mental Health
            </button>
            <button
                class="toggle-pill"
                :class="{ active: selectedCategory === 'diet' }"
                @click="selectedCategory = 'diet'"
            >
                Diet & Nutrition
            </button>
        </div>

        <!-- Physical Health Section -->
        <!-- <div v-if="selectedCategory === 'physical'">
            <div class="d-flex flex-column gap-2 mb-3">
                <div v-for="category in physicalCategories" :key="category">
                    <button
                        class="phys-btn w-100"
                        :class="{ active: selectedPhysical === category }"
                        @click="togglePhysical(category)"
                    >
                        {{ formatPhysicalCategory(category) }}
                    </button>

                    
                    <div v-if="selectedPhysical === category" class="accordion physical-accordion mt-2 mb-3">
                        <div
                            v-for="sectionName in ['asanas','pranayama','mudras','kriyas','meditation','instructions']"
                            :key="sectionName"
                            v-if="showSection(category, sectionName)"
                            class="accordion-item mb-2"
                        >
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button"
                                    :class="{ collapsed: openPhysical !== sectionName }"
                                    @click="toggleAccordion(sectionName)"
                                    type="button"
                                >
                                    {{ formatSectionName(sectionName) }}
                                </button>
                            </h2>

                            <div 
                                class="accordion-collapse collapse"
                                :class="{ show: openPhysical === sectionName }"
                            >
                                <div class="accordion-body">
                                   
                                    <div v-if="sectionName === 'instructions'" v-html="newPhysical[category].instructions"></div>

                                   
                                    <div v-else>
                                        <div
                                            v-for="(video, vIndex) in newPhysical[category][sectionName]"
                                            :key="vIndex"
                                            class="mb-4"
                                        >
                                            <h6>{{ vIndex + 1 }}. {{ video.title }}</h6>
                                            <div class="ratio ratio-16x9 rounded shadow-sm">
                                                <iframe
                                                    :src="video.url"
                                                    frameborder="0"
                                                    allowfullscreen
                                                ></iframe>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div> -->
        <div v-if="selectedCategory === 'physical'">
            <div class="d-flex flex-column gap-2 mb-3">
                <div v-for="category in physicalCategories" :key="category">
                    <button
                        class="phys-btn w-100"
                        :class="{ active: selectedPhysical === category }"
                        @click="togglePhysical(category)"
                    >
                        {{ formatPhysicalCategory(category) }}
                    </button>

                    <!-- Accordion below selected category -->
                    <div v-if="selectedPhysical === category" class="accordion physical-accordion mt-2 mb-3">
                        <template v-for="section in ['asanas','pranayama','mudras','kriyas','meditation','instructions']" :key="section">
                            <div v-if="showSection(category, section)" class="accordion-item mb-2">
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button"
                                        :class="{ collapsed: openPhysical !== section }"
                                        @click="toggleAccordion(section)"
                                        type="button"
                                    >
                                        {{ formatSectionName(section) }}
                                    </button>
                                </h2>

                                <div 
                                    class="accordion-collapse collapse"
                                    :class="{ show: openPhysical === section }"
                                >
                                    <div class="accordion-body">
                                        <!-- Instructions -->
                                        <div v-if="section === 'instructions'" v-html="newPhysical[category].instructions"></div>

                                        <!-- Videos -->
                                        <div v-else>
                                            <div
                                                v-for="(video, vIndex) in newPhysical[category][section]"
                                                :key="vIndex"
                                                class="mb-4"
                                            >
                                                <h6>{{ vIndex + 1 }}. {{ video.title }}</h6>
                                                <div class="ratio ratio-16x9 rounded shadow-sm">
                                                    <iframe
                                                        :src="video.url"
                                                        frameborder="0"
                                                        allowfullscreen
                                                    ></iframe>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mental Section -->
        <div v-else-if="selectedCategory === 'mental'">
            <div class="accordion" id="mentalAccordion">
                <div
                    class="accordion-item mb-2"
                    v-for="(section, index) in mentalSections"
                    :key="`mental-${index}`"
                >
                    <h2 class="accordion-header" :id="`heading-mental-${index}`">
                        <button
                            type="button"
                            class="accordion-button"
                            :class="{ collapsed: open.mental !== index }"
                            :aria-expanded="open.mental === index"
                            @click="toggle('mental', index)"
                        >
                            {{ section.title }}
                        </button>
                    </h2>

                    <div
                        :class="['accordion-collapse', 'collapse', { show: open.mental === index }]"
                        :aria-labelledby="`heading-mental-${index}`"
                        :id="`collapse-mental-${index}`"
                    >
                        <div class="accordion-body">
                            <div
                                v-for="(video, vIndex) in section.videos"
                                :key="`mental-video-${index}-${vIndex}`"
                                class="mb-4"
                            >
                                <h6>{{ vIndex + 1 }}. {{ video.title }}</h6>
                                <div class="ratio ratio-16x9 rounded shadow-sm">
                                    <iframe
                                        :src="video.url"
                                        title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diet Section -->
        <div v-else>
            <div class="mb-3">
                <h5 class="fw-bold">{{ dietSections[0].title }}</h5>
                <p class="text-muted">Nutrition and wellbeing related expert videos</p>
            </div>

            <div v-for="(video, vIndex) in dietSections[0].videos" 
                :key="`diet-video-${vIndex}`" 
                class="mb-4"
            >
                <h6>{{ vIndex + 1 }}. {{ video.title }}</h6>
                <div class="ratio ratio-16x9 rounded shadow-sm">
                    <iframe
                        :src="video.url"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { mentalSections } from '@/utils/mentalVideos';
import { dietSections } from '@/utils/dietVideos';
import { newPhysical } from "@/utils/newPhysicalVideos";

const physicalCategories = ['general','hypertension','diabetes','obesity'];
const selectedPhysical = ref("");
const selectedCategory = ref('physical');

// Accordion states
const openPhysical = ref("");
function toggleAccordion(section) {
    openPhysical.value = openPhysical.value === section ? null : section;
}
function togglePhysical(category) {
    selectedPhysical.value = selectedPhysical.value === category ? "" : category;
    openPhysical.value = null;
}

// Formatting
function formatSectionName(name) {
    if (name === 'instructions') return 'Instructions';
    if (name === 'pranayama') return 'Pranayama';
    if (name === 'asanas') return 'Asanas';
    if (name === 'mudras') return 'Mudras';
    if (name === 'kriyas') return 'Kriyas';
    if (name === 'meditation') return 'Meditation & Relaxation';
    return name;
}
function formatPhysicalCategory(name) {
    if(name === 'general') return 'General Health';
    if(name === 'hypertension') return 'Hypertension';
    if(name === 'diabetes') return 'Diabetes';
    if(name === 'obesity') return 'Obesity';
    return name;
}

// Show section only if it has content
function showSection(category, sectionName) {
    if (!category || !newPhysical[category]) return false;
    if (sectionName === 'instructions') {
        const instr = newPhysical[category].instructions;
        return instr && instr.trim().length > 0;
    }
    const videos = newPhysical[category][sectionName];
    return Array.isArray(videos) && videos.length > 0;
}

// Mental & Diet accordions
const open = reactive({
    physical: null,
    mental: null,
    diet: null,
});
function toggle(category, index) {
    open[category] = open[category] === index ? null : index;
}
</script>

<style scoped>
.toggle-pill {
    border: 1px solid #dc3545;
    color: #6c757d;
    background-color: transparent;
    border-radius: 50rem;
    padding: 6px 16px;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}
.toggle-pill.active {
    background-color: #dc3545 !important;
    color: #fff !important;
    border-color: #dc3545 !important;
}

.phys-btn {
    border: 1px solid #6c757d;
    color: #495057;
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 10px;
    font-weight: 500;
    transition: 0.2s;
    text-align: center;
    display: block;
    width: 100%;
}
.phys-btn.active {
    background: #727f91;
    color: #fff;
    border-color: #6c757d;
}

.accordion-button {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #6c757d;
}
.accordion-button.collapsed {
    background-color: #f8f9fa;
    color: #495057;
}

.physical-accordion {
    margin-top: 0.5rem;
    margin-bottom: 1rem;
}
</style>
