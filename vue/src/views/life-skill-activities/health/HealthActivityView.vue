<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Life Skill Activities - Health</h1>

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
        title: 'STRESS MANAGEMENT', 
        route: '' ,
        subheadings: [
            { title: 'RIDING THE WAVE', route: '/activity/life-skills/health/stress-management/riding-the-wave' },
            { title: 'MINDFULL BREATHING', route: '/activity/life-skills/health/stress-management/mind-ful-breathing' },
        ],
    },
    { 
        id: 2, 
        title: 'SELF-DISCIPLINE',
        route: '',
        subheadings: [
            { title: 'DISCIPLINE TRACKER', route: '/activity/life-skills/health/discipline/discipline-tracker' },
            { title: 'WILLPOWER WALLET', route: '/activity/life-skills/health/discipline/willpower-wallet' },
        ],
    },
    { 
        id: 3, 
        title: 'PERSONAL SAFETY', 
        route: '',
        subheadings: [
            { title: 'SAFETY CHECK', route: '/activity/life-skills/health/personal-safety/safety-check' },
            { title: 'CALM RESPONSE', route: '/activity/life-skills/health/personal-safety/calm-response' },
        ],
    },
    { 
        id: 4, 
        title: 'SELF ESTEEM', 
        route: '', 
        subheadings: [
            { title: 'MIRROR MOVEMENTS', route: '/activity/life-skills/health/self-esteem/mirror-movements' },
            { title: 'REFRAME THOUGHT', route: '/activity/life-skills/health/self-esteem/reframe-thought' },
        ],
    },
    { id: 5, title: 'MORAL REASONING', route: '/activity/life-skills/health/moral-reasoning' },
    { 
        id: 6, 
        title: 'HEALTHY LIFESTYLE ', 
        route: '' ,
        subheadings: [
            { title: 'QUICK EXERCISE BREAK', route: '/activity/life-skills/health/lifestyle/quick-exercise' },
            { title: 'NUTRITION LOGGING', route: '/activity/life-skills/health/lifestyle/nutrition-logging' },
        ],
    },
];

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
