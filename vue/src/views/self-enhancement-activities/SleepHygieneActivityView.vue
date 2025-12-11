<template>
    <div class="container py-4">
        <h2 class="mb-4 text-center fw-bold text-dark">
            Sleep Hygiene - “Give Rest to Your Body and Mind”
        </h2>

        <!-- PART 1 -->
        <div v-if="step === 1">
            <!-- Imagine the Situation -->
            <section class="mb-4">
                <h5 class="fw-semibold text-dark mb-2">Imagine the Situation:</h5>
                <p class="text-muted">
                    You’ve just returned home or to your quarters after a long day or night duty. Your body feels exhausted,
                    but your mind is still alert — replaying work incidents, checking your phone, or watching TV. You try to
                    sleep, but rest doesn’t come easily. This activity is designed to help you prepare your body and mind for
                    quality sleep, regardless of your shift hours.
                </p>
            </section>

            <!-- Steps -->
            <section class="mb-4">
                <h5 class="fw-semibold text-dark mb-2">Steps to Practice:</h5>
                <ol class="text-muted">
                    <li><b>Fix Your Rest Time:</b> Set a consistent rest schedule based on your shift.</li>
                    <li><b>Wind-Down Routine:</b> Switch off devices 30 mins before sleep, wash face, take deep breaths, and stretch lightly.</li>
                    <li><b>Control Light and Noise:</b> Darken your room or play soft sounds like rain or waves.</li>
                    <li><b>Calm the Mind:</b> Recall a peaceful memory or think of something you’re grateful for.</li>
                    <li><b>Keep Bed for Rest Only:</b> Avoid using your bed for scrolling, eating, or working.</li>
                </ol>
            </section>

            <!-- Next Button -->
            <div class="text-center mt-4">
                <button class="btn btn-primary px-4" @click="goNext">
                    Next →
                </button>
            </div>
        </div>

        <!-- PART 2 -->
        <div v-else>
            <!-- Reflection Questions -->
            <section class="mb-4">
                <h5 class="fw-semibold text-dark mb-2">Reflection Questions:</h5>

                <!-- STAR RATING 1 -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">
                        1. How calm did you feel before falling asleep? (1-5)
                    </label>

                    <div class="star-group">
                        <i v-for="n in 5"
                           :key="'calm-' + n"
                           class="bi"
                           :class="n <= calmness ? 'bi-star-fill text-warning' : 'bi-star text-warning'"
                           @click="calmness = n"
                           style="font-size: 1.6rem; cursor: pointer; margin-right: 4px;">
                        </i>
                    </div>
                </div>

                <!-- STAR RATING 2 -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">
                        2. How refreshed did you feel upon waking up? (1-5)
                    </label>

                    <div class="star-group">
                        <i v-for="n in 5"
                           :key="'fresh-' + n"
                           class="bi"
                           :class="n <= freshness ? 'bi-star-fill text-warning' : 'bi-star text-warning'"
                           @click="freshness = n"
                           style="font-size: 1.6rem; cursor: pointer; margin-right: 4px;">
                        </i>
                    </div>
                </div>

                <!-- YES / NO -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">
                        3. Did you avoid screen time before bed? (Yes/No)
                    </label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="screenYes" value="Yes" v-model="screenTimeAvoided" />
                            <label class="form-check-label" for="screenYes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="screenNo" value="No" v-model="screenTimeAvoided" />
                            <label class="form-check-label" for="screenNo">No</label>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Purpose -->
            <section class="mb-4">
                <h5 class="fw-semibold text-dark mb-2">Purpose:</h5>
                <p class="text-muted">
                    To help police officers manage irregular sleep cycles due to shift duties and promote restorative rest,
                    thereby improving alertness, emotional balance, and physical recovery.
                </p>
            </section>

            <!-- Tip -->
            <div class="alert alert-info small">
                <strong>Tip:</strong> “Your strength comes from your rest. Shifts may change — but rest is your right.”
            </div>

            <!-- Motivational Quote -->
            <div class="text-center fst-italic mt-3 text-secondary">
                “Sleep is a skill — the more you practice it, the stronger you become.”
            </div>

            <!-- Buttons -->
            <div class="text-center mt-4">
                <button class="btn btn-secondary me-2 px-4" @click="goBack">
                    ← Back
                </button>
                <button class="btn btn-success px-4" @click="completeActivity">
                    Complete Activity
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { scrollToTop } from '@/utils/scrollHelper'

const router = useRouter()
const step = ref(1)

const calmness = ref(0)
const freshness = ref(0)
const screenTimeAvoided = ref('Yes')

onMounted(() => {
    scrollToTop()
})

const goNext = () => {
    step.value = 2
    scrollToTop()
}

const goBack = () => {
    step.value = 1
    scrollToTop()
}

const completeActivity = () => {
    alert(
`✅ Sleep Hygiene activity completed!

Calmness: ${calmness.value}/5
Freshness: ${freshness.value}/5
Screen time avoided: ${screenTimeAvoided.value}`
    )
    router.push('/self-enhancement')
}
</script>

<style scoped>
.star-group i:hover {
    transform: scale(1.1);
}
</style>
