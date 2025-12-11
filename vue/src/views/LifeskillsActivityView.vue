<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Life Skills Activities</h1>

        <div class="row justify-content-center g-4">
            <div class="col-12 col-sm-6 col-md-3 text-center">
                <router-link to="/activity/life-skills/head" class="btn btn-secondary btn-lg w-100">
                    HEAD
                </router-link>
            </div>
            <div class="col-12 col-sm-6 col-md-3 text-center">
                <router-link to="/activity/life-skills/heart" class="btn btn-secondary btn-lg w-100">
                    HEART
                </router-link>
            </div>
            <div class="col-12 col-sm-6 col-md-3 text-center">
                <router-link to="/activity/life-skills/hand" class="btn btn-secondary btn-lg w-100">
                    HAND
                </router-link>
            </div>
            <div class="col-12 col-sm-6 col-md-3 text-center">
                <router-link to="/activity/life-skills/health" class="btn btn-secondary btn-lg w-100">
                    HEALTH
                </router-link>
            </div>
        </div>

        <div class="text-center mt-5">
            <button class="btn btn-secondary" @click="goBack">
                ← Back to Activities
            </button>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()

const goBack = () => {
    router.push('/activity')
}
</script>

<style scoped>
h1 {
    font-weight: 700;
    letter-spacing: 0.5px;
}
.btn-lg {
    font-weight: 600;
    padding: 1rem 0;
    font-size: 1.25rem;
    border-radius: 0.5rem;
    transition: transform 0.2s ease;
}
.btn-lg:hover {
    transform: scale(1.05);
}
</style>
