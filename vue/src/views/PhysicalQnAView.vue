<template>
    <div class="container px-3">
        <h5 class="text-center py-3" v-if="!showObesity">Physical Health Assessment</h5>

        <div class="row">
            <!-- Obesity Button -->
            <div class="col-12 col-lg-6">
                <div class="text-center mb-4" v-if="!showObesity">
                    <button class="btn btn-accent w-75" @click="showObesity = true">Obesity Management</button>
                </div>
            </div>
             <div class="col-12 col-lg-6">
                <div class="text-center mb-4" v-if="!showObesity">
                    <button class="btn btn-accent w-75">General Health</button>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="text-center mb-4" v-if="!showObesity">
                    <button class="btn btn-accent w-75">Hypertension</button>
                </div>
            </div>
             <div class="col-12 col-lg-6">
                <div class="text-center mb-4" v-if="!showObesity">
                    <button class="btn btn-accent w-75">DIabities</button>
                </div>
            </div>
            <!-- <div class="col-12  col-lg-6 text-center" v-if="!showObesity">
                <button class="btn btn-accent mb-4" @click="openYogaForm">Yoga Interventional</button>
            </div>
            <div class="col-12 col-lg-6 text-center" v-if="!showObesity">
                <button class="btn btn-accent mb-4" @click="openDietForm">Diet Interventional</button>
            </div> -->

        </div>

        <!-- Load Obesity Questionnaire Component -->
        <ObesityQuestionnaire v-if="showObesity" />
    </div>
</template>

<script setup>
    import { ref } from 'vue'
    import ObesityQuestionnaire from '@/components/ObesityQuestionnaire.vue' // Adjust path if needed

    const showObesity = ref(false);

    function openYogaForm() {
        window.open('https://docs.google.com/forms/d/e/1FAIpQLSd42M03HC04BCpC6QSTwnH9rVKv12AOxZ5I-p42fkAIETV7Zg/viewform', '_blank')
    }

    function openDietForm() {
        window.open('https://docs.google.com/forms/d/e/1FAIpQLSczE2kPapqiv7seFzji6ZZpRwdHGkYS4KYrsDpBsKbez3Gj5w/viewform', '_blank')
    }
</script>