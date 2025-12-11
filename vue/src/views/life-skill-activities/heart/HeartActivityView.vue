<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Life Skill Activities - Heart</h1>

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
        title: 'COMMUNICATION',
        subheadings: [
            {
                title: 'LANGUAGE MATTERS',
                route: '/activity/life-skills/heart/communication/language-matters',
            },
            {
                title: 'SILENCE SIGNALS',
                route: '/activity/life-skills/heart/communication/silence-signals',
            },
        ],
    },
    { id: 2, title: 'EMPATHY', route: '/activity/life-skills/heart/empathy' },
    { id: 3, title: 'CONFLICT RESOLUTION', route: '/activity/life-skills/heart/conflict-resolution' },
    { id: 4, title: 'SOCIAL SKILLS', route: '/activity/life-skills/heart/social-skills' },
    { id: 5, 
        title: 'SHARING', 
        route: '' ,
        subheadings: [
            {
                title: 'GRATITUDE GALLERY',
                route: '/activity/life-skills/heart/sharing/gratitude-gallery',
            },
            {
                title: 'GRATITUDE WALL',
                route: '/activity/life-skills/heart/sharing/gratitude-wall',
            }
        ],
    },
    { id: 6, title: 'NURTURING RELATIONSHIPS', route: '/activity/life-skills/heart/nurturing-relationship' },
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
