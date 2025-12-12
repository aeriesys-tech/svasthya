<template>
    <div class="container px-3">
        <h5 class="text-center py-3" v-if="!activeComponent">Mental Health Assessment</h5>

        <!-- Buttons Grid -->
        <div class="row text-center g-3 mb-4" v-if="!activeComponent">
            <div class="col-12 col-lg-6">
                <button class="btn btn-accent w-75" @click="activeComponent = 'anger'">
                    Anger Management
                </button>
            </div>
            <!-- <div class="col-12 col-lg-6">
                <button class="btn btn-accent w-75" @click="activeComponent = 'psychologicalDisorder'">
                    Psychological Disorder
                </button>
            </div> -->
            <div class="col-12 col-lg-6">
                <button class="btn btn-accent w-75" @click="activeComponent = 'financialManagement'">
                    Financial Management
                </button>
            </div>
            <div class="col-12 col-lg-6">
                <button class="btn btn-accent w-75" >
                    Life Skills
                </button>
            </div>
        </div>

        <!-- Load Components Dynamically -->
        <AngerQuestionnaire v-if="activeComponent === 'anger'" />
        <!-- <PsychologicalDisorder v-if="activeComponent === 'psychologicalDisorder'" /> -->
        <FinancialManagement v-if="activeComponent === 'financialManagement'" />
        <!-- <MentalStatus v-if="activeComponent === 'mentalStatus'" /> -->
    </div>
</template>

<script setup>
    import { ref } from 'vue'
    import AngerQuestionnaire from '@/components/AngerQuestionnaire.vue'
    import PsychologicalDisorder from '@/components/PsychologicalDisorder.vue';
    import FinancialManagement from '@/components/FinancialManagement.vue';
    import MentalStatus from '@/components/MentalStatus.vue';
    const activeComponent = ref(null);

</script>
