<template>
  <div class="moral-activity-wrapper">

    <!-- STEP 1: Choose Scenario -->
    <div v-if="step === 1" class="screen">
      <h2 class="title">Choose a Scenario</h2>
      <p class="subtitle">Select a category:</p>

      <div class="category-buttons">
        <button
          v-for="cat in categories"
          :key="cat.name"
          @click="selectCategory(cat)"
          :class="{selected: selectedCategory?.name === cat.name}">
          {{ cat.name }}
        </button>
      </div>

      <div class="actions-row">
        <button class="primary-btn" :disabled="!selectedScenario" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- STEP 2: Display Scenario -->
    <div v-if="step === 2" class="screen">
      <h2 class="title">Ethical Dilemma</h2>
      <p class="subtitle">{{ selectedScenario.text }}</p>

      <div class="timer-note" v-if="timer > 0">
        Take {{ timer }} seconds to think before selecting your decision...
      </div>

      <div class="decision-options">
        <button v-for="(opt, index) in selectedScenario.options" :key="index"
                :class="{selected: selectedOption === index}"
                :disabled="timer > 0"
                @click="selectOption(index)">
          {{ opt }}
        </button>
      </div>

      <div class="actions-row">
        <button class="primary-btn" :disabled="selectedOption === null" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- STEP 3: Justification -->
    <div v-if="step === 3" class="screen">
      <h2 class="title">Justify Your Choice</h2>
      <p class="subtitle">Why did you choose this? (max 200 characters)</p>
      <textarea v-model="justification" maxlength="200" placeholder="Type your reasoning..."></textarea>

      <div class="actions-row">
        <button class="primary-btn" :disabled="!justification.trim()" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- STEP 4: Values Selection -->
    <div v-if="step === 4" class="screen">
      <h2 class="title">Which values guided your decision?</h2>
      <p class="subtitle">Select up to 3 values:</p>

      <div class="values-options">
        <button v-for="val in coreValues" :key="val" 
                :class="{selected: selectedValues.includes(val)}"
                @click="toggleValue(val)">
          {{ val }}
        </button>
      </div>

      <div class="actions-row">
        <button class="primary-btn" :disabled="selectedValues.length === 0" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- STEP 5: Reflection -->
    <div v-if="step === 5" class="screen">
      <h2 class="title">Reflection</h2>
      <p class="subtitle">Was this an easy or tough decision? Would your choice be different if someone else was involved?</p>

      <textarea v-model="reflection" maxlength="300" placeholder="Write your reflection..."></textarea>

      <div class="actions-row">
        <button class="primary-btn" :disabled="!reflection.trim()" @click="saveDecision">Save Decision</button>
        <button class="secondary-btn" @click="markStillThinking">Still Thinking</button>
      </div>
    </div>

    <!-- STEP 6: Summary / Motivational -->
    <div v-if="step === 6" class="screen">
      <h2 class="title">Decision Saved!</h2>
      <p class="subtitle">You chose: <strong>{{ selectedScenario.options[selectedOption] }}</strong></p>
      <p>Values you prioritized: {{ selectedValues.join(", ") }}</p>
      <p>Your reflection: {{ reflection || "Still Thinking" }}</p>
      <p>{{ motivationalMessage }}</p>

      <button class="primary-btn" @click="resetActivity">Back to Home</button>
    </div>

    <!-- LOG / Journal VIEW -->
    <div v-if="showLog" class="screen log-screen">
      <h2 class="title">Moral Compass Journal</h2>

      <div v-if="!journal.length" class="empty-log">No entries yet.</div>

      <div v-for="(entry, idx) in journal" :key="idx" class="log-entry">
        <p><strong>{{ entry.date }}</strong></p>
        <p><strong>Scenario:</strong> {{ entry.scenario }}</p>
        <p><strong>Choice:</strong> {{ entry.choice }}</p>
        <p><strong>Reason:</strong> {{ entry.justification }}</p>
        <p><strong>Values:</strong> {{ entry.values.join(", ") }}</p>
        <p><strong>Reflection:</strong> {{ entry.reflection || "Still Thinking" }}</p>
      </div>

      <div class="actions-row">
        <button class="primary-btn mt-3" @click="showLog=false">Close Log</button>
        <button class="secondary-btn mt-3" @click="clearJournal">Clear Journal</button>
      </div>
    </div>

    <!-- Floating Log Button -->
    <button class="floating-log-btn" @click="showLog=true">Moral Journal</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const step = ref(1);
const timer = ref(30);
const timerInterval = ref(null);

const categories = [
  { name: "Workplace Integrity" },
  { name: "Loyalty vs Duty" },
  { name: "Compassion vs Law" },
  { name: "Public vs Private Interest" }
];

const scenarios = [
  {
    category: "Workplace Integrity",
    text: "Your teammate skips a safety check to save time but gets good results. Do you report it, or support them?",
    options: ["Report the teammate", "Support the teammate ensuring no escalation"]
  },
  {
    category: "Loyalty vs Duty",
    text: "A colleague bends rules to help a civilian. Do you follow rules or support them?",
    options: ["Follow rules strictly", "Support colleague with caution"]
  },
  {
    category: "Compassion vs Law",
    text: "A shopkeeper blocks the road to feed stray animals. Fine him or explain kindly?",
    options: ["Issue fine", "Explain kindly and let it pass"]
  }
];

const coreValues = ["Fairness", "Loyalty", "Legality", "Wisdom", "Integrity", "Compassion", "Justice"];

const selectedCategory = ref(null);
const selectedScenario = ref(null);
const selectedOption = ref(null);
const justification = ref("");
const selectedValues = ref([]);
const reflection = ref("");
const journal = ref([]);
const showLog = ref(false);
const motivationalMessage = ref("Great job reflecting on your ethical decision!");

/* -------------------------
   FUNCTIONS
------------------------- */
function selectCategory(cat){
  selectedCategory.value = cat;
  const filtered = scenarios.filter(s => s.category === cat.name);
  selectedScenario.value = filtered[Math.floor(Math.random()*filtered.length)];
  timer.value = 30;
  startTimer();
}

function startTimer(){
  clearInterval(timerInterval.value);
  timerInterval.value = setInterval(()=>{
    if(timer.value>0) timer.value--;
    else clearInterval(timerInterval.value);
  },1000);
}

function nextStep(){ step.value++; }

function selectOption(idx){ selectedOption.value = idx; }

function toggleValue(val){
  if(selectedValues.value.includes(val)){
    selectedValues.value = selectedValues.value.filter(v=>v!==val);
  } else if(selectedValues.value.length<3){
    selectedValues.value.push(val);
  }
}

function saveDecision(){
  const entry = {
    date: new Date().toLocaleString(),
    scenario: selectedScenario.value?.text || "",
    choice: selectedScenario.value?.options[selectedOption.value] || "",
    justification: justification.value,
    values: selectedValues.value,
    reflection: reflection.value
  };
  journal.value.push(entry);
  localStorage.setItem("moralJournal", JSON.stringify(journal.value));
  step.value = 6;
}

function markStillThinking(){
  const entry = {
    date: new Date().toLocaleString(),
    scenario: selectedScenario.value?.text || "",
    choice: selectedScenario.value?.options[selectedOption.value] || "",
    justification: justification.value,
    values: selectedValues.value,
    reflection: null
  };
  journal.value.push(entry);
  localStorage.setItem("moralJournal", JSON.stringify(journal.value));
  step.value = 6;
}

function clearJournal(){
  if(!confirm("Clear all journal entries?")) return;
  journal.value = [];
  localStorage.removeItem("moralJournal");
}

function resetActivity(){
  step.value = 1;
  selectedCategory.value = null;
  selectedScenario.value = null;
  selectedOption.value = null;
  justification.value = "";
  selectedValues.value = [];
  reflection.value = "";
  timer.value = 30;
}

onMounted(()=>{
  try{
    const saved = JSON.parse(localStorage.getItem("moralJournal")||"[]");
    if(Array.isArray(saved)) journal.value = saved;
  }catch(e){ journal.value = []; }
});
</script>

<style scoped>
.moral-activity-wrapper { 
    padding: 20px; 
    max-width: 500px; 
    margin: auto; 
    font-family: "Segoe UI", sans-serif; 
    color: #1b2b33; 
    position: relative; 
}

.screen { margin-bottom: 18px; }
.title { font-size: 1.6rem; font-weight: 700; margin-bottom: 6px; }
.subtitle { color: #556; margin-bottom: 12px; }

.category-buttons button,
.decision-options button,
.values-options button{
    margin: 6px 6px 0 0;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid #ccc;
    background: #f5f5f5;
    color: #1b2b33;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s ease;
}
.category-buttons button:hover,
.decision-options button:hover,
.values-options button:hover {
    background: #d1d5db; /* subtle hover */
}

.selected {
    background-color: #1f2937 !important; /* dark background for selected */
    color: #ffffff !important;           /* white text */
    border-color: #111827 !important;
}

textarea { 
    width: 100%; 
    min-height: 80px; 
    padding: 10px; 
    border-radius: 10px; 
    border: 1px solid #ccc; 
    margin-bottom: 12px; 
    font-size: 1rem;
}

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
    transition: background 0.2s ease;
}
.primary-btn:hover:not(:disabled) { background: #3b6fd2; }
.primary-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.secondary-btn { 
    width: 100%; 
    padding: 10px; 
    background: #efefef; 
    border: none; 
    border-radius: 10px; 
    cursor: pointer; 
    margin-top: 8px; 
    font-weight: 600; 
    transition: background 0.2s ease;
}
.secondary-btn:hover { background: #d9d9d9; }

.log-screen .log-entry { 
    background: #f7f7f8; 
    padding: 12px; 
    border-radius: 12px; 
    margin-bottom: 10px; 
}
.empty-log { 
    color: #666; 
    padding: 12px; 
    background:#fafafa; 
    border-radius:10px; 
}

.floating-log-btn { 
    width:100%; 
    color: #fff; 
    padding: 12px 18px; 
    border-radius: 999px; 
    border: none; 
    cursor: pointer; 
    font-weight: 700; 
    margin-top: 12px; 
    background: #4ec7ff; 
    transition: background 0.2s ease;
}
.floating-log-btn:hover { background: #2fa1dc; }

.actions-row { display: flex; gap: 12px; flex-direction: column; }
.timer-note { margin-bottom: 12px; color: #a33; font-weight: 600; }

</style>

