<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Life Skill Activities</h1>

        <div class="row g-3 justify-content-center">
            <div class="col-12 col-md-4" v-for="activity in activities" :key="activity.id">
                <button 
                    class="btn btn-accent w-100"
                    @click="goToActivity(activity.route)"
                >
                    {{ activity.title }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const activities = [    
    { id: 1, title: 'LIFE SKILLS', route: '/activity/life-skills' },
]

const goToActivity = (route) => {
    router.push(route)
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
}

.btn:hover {
    transform: scale(1.05);
}
</style>
