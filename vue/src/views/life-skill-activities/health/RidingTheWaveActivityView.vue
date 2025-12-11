<template>
  <div class="container py-5">
    <!-- Step 1: Guided Intro -->
    <div v-if="step === 'intro'" class="text-center">
      <h1 class="mb-3">Riding the Wave</h1>
      <h5 class="text-muted mb-4">Building Distress Tolerance</h5>
      <p class="lead">
        Emotions are like waves — they rise, peak, and fall. The goal is not to avoid distress,
        but to ride it safely until it passes.
      </p>
      <p class="fw-semibold text-accent mt-3">
        “You’ve faced worse. This is just another wave.”
      </p>
      <button class="btn btn-accent mt-4" @click="step = 'recall'">Start Activity</button>
    </div>

    <!-- Step 2: Recall a Distressing Moment -->
    <div v-else-if="step === 'recall'">
      <h3 class="mb-3 text-center">Recall a Distressing Moment</h3>
      <p class="text-center text-muted mb-4">
        Think of a recent time when you felt overwhelmed — at work or elsewhere.
      </p>

      <div class="mb-3">
        <label class="form-label fw-semibold">What happened?</label>
        <textarea class="form-control" rows="3" v-model="recall.whatHappened"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">What emotion did you feel?</label>
        <select class="form-select mb-2" v-model="recall.emotion">
          <option disabled value="">Select emotion</option>
          <option>Anger</option>
          <option>Fear</option>
          <option>Sadness</option>
          <option>Frustration</option>
          <option>Guilt</option>
          <option>Other</option>
        </select>
        <input type="text" class="form-control" placeholder="Describe in your own words"
          v-model="recall.emotionDetail" />
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">How strong was it? (0–10)</label>
        <input type="range" class="form-range" min="0" max="10" v-model="recall.intensity" />
        <div class="text-center text-muted">Intensity: {{ recall.intensity }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">What did you feel like doing?</label>
        <input type="text" class="form-control" placeholder="e.g. yell, shut down" v-model="recall.urge" />
      </div>

      <div class="text-center mt-4">
        <button class="btn btn-accent" @click="step = 'track'">Next: Track the Urge</button>
      </div>
    </div>

    <!-- Step 3: Track the Urge -->
    <div v-else-if="step === 'track'">
      <h3 class="mb-3 text-center">Track the Urge</h3>
      <p class="text-center text-muted">What was your physical or emotional urge?</p>

      <input type="range" min="0" max="10" v-model="urge.intensity" class="form-range" />
      <div class="text-center text-muted mb-3">Urge Level: {{ urge.intensity }}</div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Choose what fits best:</label>
        <div v-for="option in urgeOptions" :key="option" class="form-check">
          <input type="radio" class="form-check-input" :id="option" v-model="urge.choice" :value="option" />
          <label class="form-check-label" :for="option">{{ option }}</label>
        </div>
        <input
          v-if="urge.choice === 'Other (type)'"
          type="text"
          class="form-control mt-2"
          placeholder="Describe your urge"
          v-model="urge.other"
        />
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Do you think acting on that urge would have helped?</label>
        <div class="d-flex justify-content-center gap-3 mt-2">
          <button class="btn btn-outline-success" @click="urge.response = 'Yes'">Yes</button>
          <button class="btn btn-outline-danger" @click="urge.response = 'No'">No</button>
          <button class="btn btn-outline-secondary" @click="urge.response = 'Not sure'">Not sure</button>
        </div>
      </div>

      <div class="text-center mt-4">
        <button class="btn btn-accent" @click="step = 'strategy'">
          Next: Choose Strategy
        </button>
      </div>
    </div>

    <!-- Step 4: Choose a Distress Tolerance Strategy -->
    <div v-else-if="step === 'strategy'">
      <h3 class="mb-3 text-center">Choose a Distress Tolerance Strategy</h3>
      <p class="text-center text-muted mb-4">Pick one or more strategies to try now.</p>

      <div class="strategy-section mb-4">
        <h5 class="text-accent">TIPP Skills</h5>
        <ul class="list-unstyled">
          <li>💧 Temperature – Splash cold water or hold something cool</li>
          <li>🏃 Intense movement – Jumping or brisk walking</li>
          <li>🌬️ Paced breathing – 4 in, 6 out (guided animation)</li>
          <li>💪 Muscle tensing & relaxing – step-by-step guide</li>
        </ul>
      </div>

      <div class="strategy-section mb-4">
        <h5 class="text-accent">Distraction</h5>
        <ul class="list-unstyled">
          <li>🔎 Name 5 things you can see (grounding)</li>
          <li>🔢 Count backwards from 100 by 7s</li>
          <li>🌿 Watch a 1-minute calming nature clip</li>
        </ul>
      </div>

      <div class="strategy-section mb-4">
        <h5 class="text-accent">Self-Soothing</h5>
        <ul class="list-unstyled">
          <li>🎧 Listen to a calming audio</li>
          <li>🪶 Focus on texture or object nearby</li>
          <li>🥤 Sip water slowly and feel the sensation</li>
        </ul>
      </div>

      <div class="text-center">
        <select v-model="strategy.selected" class="form-select w-auto d-inline-block mb-3">
          <option disabled value="">Select strategy to try</option>
          <option v-for="option in allStrategies" :key="option">{{ option }}</option>
        </select>
        <button class="btn btn-accent ms-2" :disabled="!strategy.selected" @click="step = 'reflect'">
          Try Now
        </button>
      </div>
    </div>

    <!-- Step 5: Reflect & Save -->
    <div v-else-if="step === 'reflect'">
      <h3 class="mb-3 text-center">Reflect & Save</h3>
      <p class="text-center text-muted">
        How did you feel after trying your chosen strategy?
      </p>

      <input type="range" min="1" max="5" v-model="reflection.mood" class="form-range mb-3" />
      <div class="text-center text-muted mb-3">
        Feeling: {{ moodLabels[reflection.mood - 1] }}
      </div>

      <textarea
        class="form-control mb-3"
        rows="3"
        placeholder="What worked or didn’t?"
        v-model="reflection.feedback"
      ></textarea>

      <label class="form-label fw-semibold">What will you try next time?</label>
      <select class="form-select mb-3" v-model="reflection.nextTime">
        <option disabled value="">Select</option>
        <option>Use paced breathing</option>
        <option>Take a walk</option>
        <option>Pause before reacting</option>
      </select>

      <div class="text-center">
        <button class="btn btn-accent" @click="saveActivity">Save to My Toolkit</button>
      </div>
    </div>

    <!-- Saved Confirmation -->
    <div v-else-if="step === 'done'" class="text-center">
      <h3 class="text-success mb-3">Saved to My Distress Toolkit!</h3>
      <p>You're learning to ride the wave, not fight it. Great work.</p>
      <!-- <button class="btn btn-outline-primary mt-3">Use Again</button> -->
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const step = ref('intro')

const recall = ref({
  whatHappened: '',
  emotion: '',
  emotionDetail: '',
  intensity: 5,
  urge: ''
})

const urge = ref({
  intensity: 5,
  choice: '',
  other: '',
  response: ''
})

const strategy = ref({
  selected: ''
})

const reflection = ref({
  mood: 3,
  feedback: '',
  nextTime: ''
})

const moodLabels = ['Very Distressed', 'Stressed', 'Neutral', 'Calmer', 'Relaxed']

const urgeOptions = [
  'Yelling or confronting',
  'Avoiding or withdrawing',
  'Shutting down',
  'Overthinking',
  'Using substances',
  'Other (type)'
]

const allStrategies = [
  'Temperature technique',
  'Intense movement',
  'Paced breathing',
  'Muscle relaxation',
  'Grounding – 5 things',
  'Counting backwards',
  'Nature clip',
  'Calming audio',
  'Grounding object',
  'Sip water mindfully'
]

const saveActivity = () => {
  const entry = {
    recall: recall.value,
    urge: urge.value,
    strategy: strategy.value,
    reflection: reflection.value,
    date: new Date().toISOString()
  }
  const saved = JSON.parse(localStorage.getItem('distressToolkit') || '[]')
  saved.push(entry)
  localStorage.setItem('distressToolkit', JSON.stringify(saved))
  step.value = 'done'
}
</script>

<style scoped>
.btn {
  padding: 0.9rem 1.2rem;
  font-size: 1rem;
  border-radius: 0.5rem;
}
.btn-accent {
  background-color: #d80621;
  color: #fff;
}
.btn-accent:hover {
  background-color: #b4051b;
}
.text-accent {
  color: #d80621;
}
.strategy-section ul {
  margin-left: 1rem;
}
.form-range {
  width: 100%;
}
@media (max-width: 576px) {
  .container {
    padding: 1rem;
  }
  .btn {
    width: 100%;
  }
}
</style>
