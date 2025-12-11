<template>
    <div class="container py-5">
        <h1 class="text-center mb-4">Daily Target Practice – Micro Goal Tracking</h1>

        <p class="text-muted text-center mb-5">
            Build strong habits with small steps. Choose a daily micro-goal to complete for 7 days in a row.
        </p>

        <!-- Micro Goal Library -->
        <div v-if="!selectedGoal">
            <h4 class="mb-3 text-accent">Choose a Goal</h4>

            <div v-for="(category, index) in categories" :key="index" class="mb-4">
                <h5 class="fw-bold">{{ category.name }}</h5>
                <div class="list-group">
                    <button
                        v-for="goal in category.goals"
                        :key="goal.name"
                        class="list-group-item list-group-item-action"
                        @click="selectGoal(goal)"
                    >
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                <strong>{{ goal.name }}</strong><br />
                                <small class="text-muted">{{ goal.description }}</small>
                            </span>
                            <span class="badge bg-light text-dark">{{ goal.time }}</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Selected Goal Screen -->
        <div v-else>
            <h4 class="mb-3 text-accent">{{ selectedGoal.name }}</h4>
            <p class="text-muted">{{ selectedGoal.description }}</p>

            <div class="text-center my-4">
                <button
                    class="btn btn-success btn-lg w-100"
                    :class="{ disabled: goalCompleted }"
                    @click="markDone"
                >
                    {{ goalCompleted ? 'Done for Today ✅' : 'Mark as Done' }}
                </button>
            </div>

            <div class="streak-tracker text-center my-4">
                <h5>7-Day Streak</h5>
                <div class="d-flex justify-content-center gap-2">
                    <div
                        v-for="n in 7"
                        :key="n"
                        class="circle"
                        :class="{
                            'completed': n <= streak,
                            'pending': n > streak
                        }"
                    ></div>
                </div>
            </div>

            <div class="text-center">
                <button class="btn btn-outline-secondary mt-3" @click="resetGoal">
                    Choose another goal
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const selectedGoal = ref(null)
const goalCompleted = ref(false)
const streak = ref(3) // Example streak count

const categories = [
    {
        name: 'Physical Wellness',
        goals: [
            { name: '10-min walk', description: 'Walk outdoors or around the station.', time: '10 min' },
            { name: '5-min stretch', description: 'Follow a guided stretch.', time: '5 min' },
        ],
    },
    {
        name: 'Emotional Regulation',
        goals: [
            { name: '3-min breathing box', description: 'Inhale-hold-exhale-hold (4s each).', time: '3 min' },
            { name: 'Emotion journal', description: 'Write one word to describe your feeling.', time: '1 min' },
        ],
    },
]

const selectGoal = (goal) => {
    selectedGoal.value = goal
}

const markDone = () => {
    goalCompleted.value = true
    if (streak.value < 7) streak.value++
}

const resetGoal = () => {
    selectedGoal.value = null
    goalCompleted.value = false
}
</script>

<style scoped>
.circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid #ccc;
    transition: 0.3s;
}
.circle.completed {
    background-color: #28a745;
    border-color: #28a745;
}
.circle.pending {
    background-color: #e9ecef;
}
</style>
