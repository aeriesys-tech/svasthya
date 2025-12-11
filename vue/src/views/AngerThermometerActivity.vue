<template>
    <div class="container py-4">
        <h4 class="text-center mb-3 fw-bold">Anger Thermometer & Punch-the-Pillow (In Your Mind!)</h4>

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Step 1 -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">1. Think of a moment that made you angry last week.</label>
                    <textarea class="form-control" rows="3" v-model="angryMoment" placeholder="E.g. Someone cut me off in traffic..."></textarea>
                </div>

                <!-- Step 2 -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        2. How angry were you? (1 = Mild, 10 = Explosive)
                    </label>
                    <input type="range" min="1" max="10" class="form-range" v-model="angerLevel" />
                    <div class="text-center fw-bold">Anger Level: {{ angerLevel }}</div>

                    <ul class="list-unstyled mt-2 small text-muted ps-2">
                        <li>1 – Mild irritation (someone chewing loudly)</li>
                        <li>5 – Moderate (colleague took credit for your work)</li>
                        <li>10 – Explosive (someone hit your parked bike)</li>
                    </ul>
                </div>

                <!-- Step 3 – Only if anger >= 7 -->
                <div v-if="angerLevel >= 7" class="mb-4">
                    <label class="form-label fw-semibold">
                        3. If your anger reached 7 or more, what did you do?
                    </label>
                    <textarea class="form-control" rows="2" v-model="reaction" placeholder="I shouted... I walked away... I stayed quiet..."></textarea>
                </div>

                <!-- Step 4 -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        4. Imagine there’s a soft pillow in front of you with a name tag of your problem.<br>
                        What would you shout into that pillow? 🤬 (Say it aloud or type it.)
                    </label>
                     <div class="input-group mb-3">
                        <input type="text" class="form-control" v-model="pillowShout" placeholder="Type your scream here..." />
                        <span class="input-group-text" id="basic-addon12"><i class="bi bi-mic"></i></span>
                    </div>
                </div>

                <!-- Submission -->
                <div class="text-center">
                    <button class="btn btn-success rounded-pill px-4" @click="submitForm">
                        Submit
                    </button>
                </div>

                <!-- Result message -->
                <div v-if="showMessage" class="alert alert-success mt-4 text-center">
                    😄 Well done! It's okay to feel anger. Releasing it safely makes you stronger!
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const angryMoment = ref('')
const angerLevel = ref(5)
const reaction = ref('')
const pillowShout = ref('')
const showMessage = ref(false)

function submitForm() {
    if (!angryMoment.value || !pillowShout.value) {
        alert('Please fill at least the first and last steps.')
        return
    }
    // Optional: You can log this data or send to backend
   

    showMessage.value = true
}
</script>

<style scoped>
.card {
    max-width: 700px;
    margin: 0 auto;
}

input[type="range"] {
    accent-color: #28a745;
}
</style>
