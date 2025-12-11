<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Life Skill Activities - Head</h1>

        <div class="row g-3 justify-content-center">
            <div
                class="col-12 col-md-4"
                v-for="activity in activities"
                :key="activity.id"
            >
                <div>
                    <button
                        class="btn btn-accent w-100 d-flex justify-content-between align-items-center"
                        @click="activity.subheadings ? toggleDropdown(activity.id) : goToActivity(activity.route)"
                    >
                        <span>{{ activity.title }}</span>
                        <i
                            v-if="activity.subheadings"
                            :class="[
                                'bi',
                                openActivityId === activity.id
                                    ? 'bi-chevron-up'
                                    : 'bi-chevron-down'
                            ]"
                        ></i>
                    </button>

                    <!-- Subheadings dropdown -->
                    <transition name="fade">
                        <div
                            v-if="openActivityId === activity.id && activity.subheadings"
                            class="mt-2 ps-3"
                        >
                            <button
                                v-for="(sub, i) in activity.subheadings"
                                :key="i"
                                class="btn btn-outline-secondary w-100 mb-2"
                                @click="goToActivity(sub.route)"
                            >
                                {{ sub.title }}
                            </button>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const openActivityId = ref(null)

const toggleDropdown = (id) => {
    openActivityId.value = openActivityId.value === id ? null : id
}

const activities = [
    {
        id: 1,
        title: 'RESILIENCE',
        route: '',
        subheadings: [
            { title: 'Five Good Things', route: '/activity/life-skills/head/resilience/five-good-things' },
            { title: 'Resilience Mapping', route: '/activity/life-skills/head/resilience/resilience-mapping' },
        ],
    },
    { id: 2, title: 'DECISION MAKING', route: '/activity/life-skills/head/decision-making' },
    { id: 3, title: 'GOAL SETTING', route: '/activity/life-skills/head/goal-setting' },
    { id: 4, title: 'PLANNING/ORGANISING', route: '/activity/life-skills/head/planning' },
    { 
        id: 5, 
        title: 'PROBLEM SOLVING', 
        route: '' ,
        subheadings: [
            { title:'BREAK IT DOWN', route: '/activity/life-skills/head/problem-solving/break-it-down'},
            { title:'PAUSE-PALN-PROCEED', route: '/activity/life-skills/head/problem-solving/pause-plan-proceed'},
        ],
    },
    // { id: 6, title: 'CRITICAL THINKING', route: '' },
]

const goToActivity = (route) => {
    if (route) router.push(route)
}
</script>

<style scoped>
h1 {
    font-weight: 700;
    letter-spacing: 1px;
}

.btn {
    padding: 1rem;
    font-size: 1.1rem;
    border-radius: 0.5rem;
    transition: transform 0.2s;
    white-space: normal;
    word-wrap: break-word;
    text-align: center;
}

.btn:hover {
    transform: scale(1.05);
}

/* Smooth dropdown animation */
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>
