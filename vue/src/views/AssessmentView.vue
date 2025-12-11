<template>
    <div class="container px-3">
        <div class="row">
            <div class="col-12">

                <!-- Loop through all modules -->
                <div 
                    class="text-center mb-4"
                    v-for="(item, index) in modules" 
                    :key="index"
                >
                    <!-- Module Button -->
                    <button 
                        class="btn btn-accent w-75"
                        @click="toggleModule(item.key)"
                    >
                        {{ item.label }}
                    </button>

                    <!-- Render Component When Active -->
                    <component 
                        v-if="active === item.key"
                        :is="item.component"
                        @close="toggleModule(item.key)" 
                    />
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

// Components
import ObesityQuestionnaire from '@/components/ObesityQuestionnaire.vue'
import AngerQuestionnaire from '@/components/AngerQuestionnaire.vue'
import FinancialManagement from '@/components/FinancialManagement.vue'
import PsychologicalDisorder from '@/components/PsychologicalDisorder.vue'
import MentalStatus from '@/components/MentalStatus.vue'
import BriefResilienceQuestionnaire from '@/components/BriefResilienceQuestionnaire.vue'
import AnxietyQuestionnaire from '@/components/AnxietyQuestionnaire.vue'
import SatisfactionWithLifeQuestionnaire from '../components/SatisfactionWithLifeQuestionnaire.vue'
import WellbeingQuestionnaire from '../components/WellbeingQuestionnaire.vue'
import WorkLifeBalanceQuestionnaire from '../components/WorkLifeBalanceQuestionnaire.vue'
import PerceivedStressQuestionnaire from '../components/PerceivedStressQuestionnaire.vue'
import SleepQualityQuestionnaire from '../components/SleepQualityQuestionnaire.vue'
import HypertensionQuestionnaire from '../components/HypertensionQuestionnaire.vue'
import GeneralHealthQuestionnaire from '../components/GeneralHealthQuestionnaire.vue'

// Track the currently open module
const active = ref(null)

// Toggle module: only one open at a time
function toggleModule(key) {
    active.value = (active.value === key) ? null : key
}

const modules = [
    { key: 'obesity', label: 'Obesity Management', component: ObesityQuestionnaire },
    { key: 'general', label: 'General Health', component: GeneralHealthQuestionnaire },
    { key: 'hypertension', label: 'Hypertension', component: HypertensionQuestionnaire },
    { key: 'diabetes', label: 'Diabetes', component: null },
    { key: 'anger', label: 'Anger Management', component: AngerQuestionnaire },
    { key: 'financial', label: 'Financial Management', component: FinancialManagement },
    { key: 'resilience', label: 'Brief Resilience', component: BriefResilienceQuestionnaire },
    { key: 'anxiety', label: 'Anxiety', component: AnxietyQuestionnaire },
    { key: 'sleep', label: 'Sleep Quality Index', component: SleepQualityQuestionnaire },
    { key: 'life', label: 'Satisfaction with Life', component: SatisfactionWithLifeQuestionnaire },
    { key: 'wellbeing', label: 'WHO - Well-being', component: WellbeingQuestionnaire },
    { key: 'worklife', label: 'Work-Life Balance', component: WorkLifeBalanceQuestionnaire },
    { key: 'stress', label: 'Perceived Stress Scale', component: PerceivedStressQuestionnaire }
];
</script>
