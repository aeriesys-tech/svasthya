<template>
  <div class="reframe-activity-wrapper">

    <!-- STEP 1: Negative Thought Entry -->
    <div v-if="step === 1" class="screen">
      <h2 class="title">Reframe That Thought</h2>
      <p class="subtitle">What's a thought that’s bothering you today?</p>

      <textarea v-model="negativeThought" placeholder="Type your thought here..." maxlength="150"></textarea>

      <div class="actions-row">
        <button class="primary-btn" :disabled="!negativeThought.trim()" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- STEP 2: Emotion Before -->
    <div v-if="step === 2" class="screen">
      <h2 class="title">How does this thought make you feel?</h2>
      <p class="subtitle">Select your emotion and intensity:</p>

      <div class="emotion-options">
        <button v-for="emo in emotions" :key="emo.name"
                :class="{selected: selectedEmotion?.name === emo.name}"
                @click="selectEmotion(emo)">
          {{ emo.icon }} {{ emo.name }}
        </button>
      </div>

      <div v-if="selectedEmotion" class="intensity-slider">
        <label>Intensity: {{ emotionIntensity }}</label>
        <input type="range" min="0" max="10" v-model.number="emotionIntensity">
      </div>

      <div class="actions-row">
        <button class="primary-btn" :disabled="!selectedEmotion" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- STEP 3: Reality Check / Guided Reframe -->
    <div v-if="step === 3" class="screen">
      <h2 class="title">Reality Check</h2>
      <p class="subtitle">
        Is this thought 100% true all the time? Can you think of a time you did well in a similar situation?
      </p>

      <textarea v-model="realityCheckNote" placeholder="Write your reality check notes..."></textarea>

      <div class="actions-row">
        <button class="primary-btn" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- STEP 4: Reframe It -->
    <div v-if="step === 4" class="screen">
      <h2 class="title">Reframe Your Thought</h2>
      <p class="subtitle">Now rewrite your thought in a more balanced or kind way:</p>

      <textarea v-model="reframedThought" placeholder="Type your reframed thought..."></textarea>

      <div class="suggestions">
        <p>Suggestions:</p>
        <button v-for="sug in reframingSuggestions" :key="sug" @click="reframedThought = sug">{{ sug }}</button>
      </div>

      <div class="actions-row">
        <button class="primary-btn" :disabled="!reframedThought.trim()" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- STEP 5: Emotion After Reframe -->
    <div v-if="step === 5" class="screen">
      <h2 class="title">How do you feel now?</h2>
      <p class="subtitle">Select your emotion and intensity:</p>

      <div class="emotion-options">
        <button v-for="emo in emotions" :key="emo.name"
                :class="{selected: postEmotion?.name === emo.name}"
                @click="selectPostEmotion(emo)">
          {{ emo.icon }} {{ emo.name }}
        </button>
      </div>

      <div v-if="postEmotion" class="intensity-slider">
        <label>Intensity: {{ postEmotionIntensity }}</label>
        <input type="range" min="0" max="10" v-model.number="postEmotionIntensity">
      </div>

      <div class="actions-row">
        <button class="primary-btn" :disabled="!postEmotion" @click="saveReframe">Save & Finish</button>
      </div>
    </div>

    <!-- STEP 6: Summary / Motivational Message -->
    <div v-if="step === 6" class="screen">
      <h2 class="title">Well Done!</h2>
      <p class="subtitle">{{ motivationalMessage }}</p>

      <div class="streak-info">
        <p>Reframe Streak: {{ streak }} days</p>
        <p>Badge: {{ badge }}</p>
      </div>

      <button class="primary-btn" @click="resetActivity">Back to Home</button>
    </div>

    <!-- LOG VIEW -->
    <div v-if="showLog" class="screen log-screen">
      <h2 class="title">My Reframe Log</h2>

      <div v-if="!reframeLog.length" class="empty-log">No entries yet.</div>

      <div v-for="(entry, idx) in reframeLog" :key="idx" class="log-entry">
        <p><strong>{{ entry.date }}</strong></p>
        <p><strong>Negative Thought:</strong> {{ entry.negative }}</p>
        <p><strong>Reframed:</strong> {{ entry.reframed }}</p>

        <p>
          <strong>Before:</strong>
          {{ entry.emotionBefore?.icon || "❓" }} {{ entry.emotionBefore?.name || "Unknown" }} 
          ({{ entry.intensityBefore || 0 }}/10)
        </p>
        <p>
          <strong>After:</strong>
          {{ entry.emotionAfter?.icon || "❓" }} {{ entry.emotionAfter?.name || "Unknown" }}
          ({{ entry.intensityAfter || 0 }}/10)
        </p>
      </div>

      <div class="actions-row">
        <button class="primary-btn mt-3" @click="showLog=false">Close Log</button>
        <button class="secondary-btn mt-3" @click="clearLog">Clear Log</button>
      </div>
    </div>

    <!-- Floating Log Button -->
    <button class="floating-log-btn btn btn-secondary" @click="showLog=true">Reframe Log</button>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

/* ------------------------------
   STATE
------------------------------ */
const step = ref(1);

const negativeThought = ref("");
const realityCheckNote = ref("");
const reframedThought = ref("");

// Emotions for selection
const emotions = [
  { name: "Sad", icon: "😔" },
  { name: "Angry", icon: "😡" },
  { name: "Anxious", icon: "😰" },
  { name: "Frustrated", icon: "😐" },
  { name: "Happy", icon: "😊" },
  { name: "Calm", icon: "😌" }
];

const selectedEmotion = ref(null);
const emotionIntensity = ref(5);

const postEmotion = ref(null);
const postEmotionIntensity = ref(5);

const reframeLog = ref([]);
const showLog = ref(false);
const streak = ref(0);
const badge = ref("");

const motivationalMessages = [
  "You took a big step toward stronger thinking!",
  "Great job reframing your thought!",
  "Keep practicing, your mindset is improving!"
];
const motivationalMessage = ref("");

const reframingSuggestions = [
  "One mistake doesn’t define me.",
  "I’ve succeeded before, I can do it again.",
  "I’m allowed to learn and grow."
];

/* ------------------------------
   HELPERS
------------------------------ */
function nextStep() {
  step.value++;
}

function selectEmotion(emo) {
  selectedEmotion.value = emo;
}

function selectPostEmotion(emo) {
  postEmotion.value = emo;
}

/* SAVE ENTRY */
function saveReframe(){
  const entry = {
    date: new Date().toLocaleString(),
    negative: negativeThought.value,
    reframed: reframedThought.value,
    emotionBefore: selectedEmotion.value || {name: "Unknown", icon: "❓"},
    intensityBefore: emotionIntensity.value ?? 0,
    emotionAfter: postEmotion.value || {name: "Unknown", icon: "❓"},
    intensityAfter: postEmotionIntensity.value ?? 0
  };
  reframeLog.value.push(entry);
  localStorage.setItem("reframeLog", JSON.stringify(reframeLog.value));

  motivationalMessage.value = motivationalMessages[Math.floor(Math.random()*motivationalMessages.length)];
  updateStreak();

  step.value = 6;
}

/* update streak using reframeLog dates */
function updateStreak() {
  const log = reframeLog.value;
  if (!log.length) {
    streak.value = 0;
    badge.value = "None";
    return;
  }

  let count = 0;
  const lastIndex = log.length - 1;
  const lastDate = new Date(log[lastIndex].date);
  const lastDayStart = new Date(lastDate.getFullYear(), lastDate.getMonth(), lastDate.getDate()).getTime();

  for (let i = lastIndex; i >= 0; i--) {
    const d = new Date(log[i].date);
    const dayStart = new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime();
    const diffDays = Math.round((lastDayStart - dayStart) / 86400000);
    if (diffDays === count) {
      count++;
    } else {
      break;
    }
  }

  streak.value = count;

  if (streak.value >= 14) badge.value = "Gold 🥇";
  else if (streak.value >= 7) badge.value = "Silver 🥈";
  else if (streak.value >= 3) badge.value = "Bronze 🥉";
  else badge.value = "None";
}

/* CLEAR LOG */
function clearLog() {
  if (!confirm("Clear all Reframe Log entries?")) return;
  reframeLog.value = [];
  localStorage.removeItem("reframeLog");
  streak.value = 0;
  badge.value = "None";
}

/* RESET ACTIVITY */
function resetActivity() {
  step.value = 1;
  negativeThought.value = "";
  realityCheckNote.value = "";
  reframedThought.value = "";
  selectedEmotion.value = null;
  emotionIntensity.value = 5;
  postEmotion.value = null;
  postEmotionIntensity.value = 5;
}

/* ------------------------------
   LIFECYCLE
------------------------------ */
onMounted(() => {
  try {
    const saved = JSON.parse(localStorage.getItem("reframeLog") || "[]");
    if (Array.isArray(saved)) reframeLog.value = saved;
  } catch (e) {
    reframeLog.value = [];
  }
  updateStreak();
});
</script>

<style scoped>
.reframe-activity-wrapper {
  padding: 20px;
  max-width: 460px;
  margin: auto;
  font-family: "Segoe UI", sans-serif;
  position: relative;
  color: #1b2b33;
}
.screen { margin-bottom: 18px; }
.title { font-size: 1.6rem; font-weight: 700; margin-bottom: 6px; }
.subtitle { color: #556; margin-bottom: 12px; }

/* Buttons */
.primary-btn {
  width: 100%;
  padding: 12px;
  margin-top: 12px;
  border-radius: 12px;
  background: #4e8cff;
  color: #fff;
  border: none;
  cursor: pointer;
  font-weight: 700;
  transition: background .18s;
}
.primary-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.primary-btn:hover:not(:disabled) { background: #3b6fd0; }

.secondary-btn {
  width: 100%;
  padding: 10px;
  background: #efefef;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  margin-top: 8px;
  font-weight: 600;
}
.secondary-btn:hover { background: #e0e0e0; }

/* Textareas */
textarea {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
  margin-bottom: 12px;
}

/* Emotion options */
.emotion-options {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.emotion-options button {
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #ccc;
  cursor: pointer;
  background: #f5f5f5;
}
.emotion-options button.selected { background: #4e8cff; color: #fff; }

/* Slider */
.intensity-slider { margin: 12px 0; }
.intensity-slider input { width: 100%; }

/* Suggestions */
.suggestions button {
  margin: 4px 4px 0 0;
  padding: 6px 10px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  background: #eef6ff;
}
.suggestions button:hover { background: #4e8cff; color: #fff; }

/* Log */
.log-screen .log-entry {
  background: #f7f7f8;
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 10px;
}
.empty-log { color: #666; padding: 12px; background:#fafafa; border-radius:10px; }

/* Floating log */
.floating-log-btn {
  width:100%;
  color: #fff;
  padding: 12px 18px;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font-weight: 700;
  margin-top: 12px;
}

/* small utility */
.actions-row { display: flex; gap: 12px; flex-direction: column; }
</style>
