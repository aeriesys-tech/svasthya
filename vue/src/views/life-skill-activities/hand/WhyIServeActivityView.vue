<template>
  <div class="container py-4">

    <!-- Header -->
    <div class="text-center mb-3">
      <h1 class="fw-bold">Why I Serve</h1>
      <p class="text-muted">Remember what brought you here — a short reflection to reconnect with purpose.</p>
    </div>

    <!-- Step Indicator -->
    <div class="text-center mb-3">
      <small class="text-muted">Step {{ step }} / 5</small>
      <div class="progress mt-2" style="height:6px;">
        <div
          class="progress-bar bg-danger"
          role="progressbar"
          :style="{ width: Math.round((step - 1) / 4 * 100) + '%' }"
          :aria-valuenow="Math.round((step - 1) / 4 * 100)"
          aria-valuemin="0"
          aria-valuemax="100"
        ></div>
      </div>
    </div>

    <!-- Step 1: Launch -->
    <div v-if="step === 1" class="text-center">
      <p class="lead">This is your space to remember what inspired you. Take a deep breath, and let's reflect.</p>
      <button class="btn btn-danger btn-lg w-100 rounded-pill mb-2" @click="nextStep">Start Reflection</button>
      <button class="btn btn-outline-secondary w-100 rounded-pill" @click="goToJournal">See Motivation Journal</button>
    </div>

    <!-- Step 2: Prompt 1 -->
    <div v-if="step === 2">
      <h5 class="text-center fw-semibold mb-3">1/4 — Why did you choose to serve in the police force?</h5>
      <p class="text-muted text-center small mb-3">
        Examples: “To protect those who can’t protect themselves.” — “To make a difference in my community.”
      </p>

      <textarea
        class="form-control mb-2"
        rows="4"
        v-model="answers.prompt1"
        placeholder="Type your answer here..."
      ></textarea>

      <div class="d-flex gap-2">
        <!-- <button class="btn btn-outline-secondary w-50 rounded-pill" @click="useVoice('prompt1')">🎤 Voice</button> -->
        <button class="btn btn-danger w-50 rounded-pill" :disabled="!answers.prompt1.trim()" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- Step 3: Prompt 2 -->
    <div v-if="step === 3">
      <h5 class="text-center fw-semibold mb-3">2/4 — What moment made you proud to wear the uniform?</h5>
      <p class="text-muted text-center small mb-3">
        Examples: “Helping a lost child reunite with their parent.” — “De-escalating a dangerous situation peacefully.”
      </p>

      <textarea
        class="form-control mb-2"
        rows="4"
        v-model="answers.prompt2"
        placeholder="Describe the moment..."
      ></textarea>

      <div class="d-flex gap-2">
        <!-- <button class="btn btn-outline-secondary w-50 rounded-pill" @click="useVoice('prompt2')">🎤 Voice</button> -->
        <button class="btn btn-danger w-50 rounded-pill" :disabled="!answers.prompt2.trim()" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- Step 4: Prompt 3 -->
    <div v-if="step === 4">
      <h5 class="text-center fw-semibold mb-3">3/4 — What do you want to stand for, even on hard days?</h5>
      <p class="text-muted text-center small mb-3">
        Examples: “Calm leadership under pressure.” — “Being someone others can rely on.”
      </p>

      <textarea
        class="form-control mb-2"
        rows="4"
        v-model="answers.prompt3"
        placeholder="Write what you want to stand for..."
      ></textarea>

      <div class="d-flex gap-2">
        <!-- <button class="btn btn-outline-secondary w-50 rounded-pill" @click="useVoice('prompt3')">🎤 Voice</button> -->
        <button class="btn btn-danger w-50 rounded-pill" :disabled="!answers.prompt3.trim()" @click="nextStep">Next</button>
      </div>
    </div>

    <!-- Step 5: Write Statement & Reminder -->
    <div v-if="step === 5">
      <h5 class="text-center fw-semibold mb-3">4/4 — Write your personal "Why I Serve" statement</h5>
      <p class="text-muted text-center small mb-3">
        Keep it short and true — 1–2 sentences max.
      </p>

      <textarea
        class="form-control mb-2"
        rows="3"
        v-model="answers.statement"
        placeholder="My Why I Serve is..."
        maxlength="200"
      ></textarea>

      <div class="form-check form-switch d-flex align-items-center justify-content-between mb-2">
        <label class="form-check-label">Set as daily reminder</label>
        <input class="form-check-input" type="checkbox" v-model="reminder.enabled" id="remToggle">
      </div>

      <div v-if="reminder.enabled" class="mb-3">
        <label class="form-label small">Reminder time</label>
        <input type="time" class="form-control mb-2" v-model="reminder.time" />
        <div class="small text-muted">Preview: <strong>{{ reminderPreview }}</strong></div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button class="btn btn-secondary w-50 rounded-pill" @click="prevStep">Previous</button>
        <button class="btn btn-success w-50 rounded-pill" :disabled="!answers.statement.trim()" @click="saveStatement">Save My Statement</button>
      </div>
    </div>

    <!-- Completion / Save -->
    <div v-if="step === 6" class="text-center">
      <h4 class="fw-bold mb-2">Saved</h4>
      <p class="text-muted mb-3">Your "Why I Serve" statement is saved in your Motivation Journal.</p>

      <div class="card mb-3">
        <div class="card-body text-center">
          <div class="fw-semibold mb-2">Your Statement</div>
          <div class="mb-2">{{ answers.statement }}</div>
          <div class="text-muted small">Reminder: {{ reminder.enabled ? reminderPreview : 'Not set' }}</div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary w-50 rounded-pill" @click="editLatest">Edit</button>
        <button class="btn btn-primary w-100 rounded-pill" @click="goToJournal">See Motivation Journal</button>
      </div>
    </div>

    <!-- Motivation Journal view -->
    <div v-if="viewJournal" class="mt-5">
      <h5 class="text-center fw-semibold mb-3">Motivation Journal</h5>

      <div v-if="journal.length === 0" class="text-center text-muted mb-3">
        No entries yet. Your saved statements will appear here.
      </div>

      <ul class="list-group mb-3">
        <li class="list-group-item" v-for="(entry, idx) in journalSorted" :key="entry.id">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">{{ entry.statement }}</div>
              <div class="small text-muted">Saved: {{ formatDate(entry.savedAt) }}</div>
              <div v-if="entry.promptSummary" class="small text-muted mt-1">Notes: {{ entry.promptSummary }}</div>
            </div>
            <div class="d-flex flex-column align-items-end">
              <div class="mb-2">
                <button class="btn btn-outline-primary btn-sm me-1" @click="useAsReminder(entry)">Set Reminder</button>
                <button class="btn btn-outline-secondary btn-sm" @click="loadForEdit(idx)">Edit</button>
              </div>
              <div>
                <button class="btn btn-danger btn-sm" @click="archiveEntry(idx)">{{ entry.archived ? 'Archived' : 'Archive' }}</button>
              </div>
            </div>
          </div>
        </li>
      </ul>

      <div class="text-center">
        <button class="btn btn-secondary rounded-pill me-2" @click="closeJournal">Close</button>
        <button class="btn btn-outline-danger rounded-pill" @click="clearAll">Clear All</button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const step = ref(1);
const viewJournal = ref(false);

const answers = ref({
  prompt1: '',
  prompt2: '',
  prompt3: '',
  statement: ''
});

const reminder = ref({
  enabled: false,
  time: '07:00' // default
});

// Motivation journal persisted in localStorage
const journalKey = 'motivation_journal_v1';
const journal = ref(JSON.parse(localStorage.getItem(journalKey) || '[]'));

// Helpers
function nextStep() {
  if (step.value < 6) step.value++;
}
function prevStep() {
  if (step.value > 1) step.value--;
}

// Format a short summary of prompts to attach to saved entry
function promptSummaryOf(ans) {
  const parts = [];
  if (ans.prompt1) parts.push(ans.prompt1.slice(0, 80));
  if (ans.prompt2) parts.push(ans.prompt2.slice(0, 80));
  if (ans.prompt3) parts.push(ans.prompt3.slice(0, 80));
  return parts.join(' • ');
}

// Save statement to journal
function saveStatement() {
  const entry = {
    id: Date.now(),
    statement: answers.value.statement.trim(),
    prompt1: answers.value.prompt1.trim(),
    prompt2: answers.value.prompt2.trim(),
    prompt3: answers.value.prompt3.trim(),
    promptSummary: promptSummaryOf(answers.value),
    reminder: reminder.value.enabled ? { time: reminder.value.time } : null,
    savedAt: new Date().toISOString(),
    archived: false
  };

  journal.value.push(entry);
  persistJournal();
  step.value = 6;
  // keep viewJournal closed until user opens
}

// Persist journal to localStorage
function persistJournal() {
  localStorage.setItem(journalKey, JSON.stringify(journal.value));
}

// Open Journal
function goToJournal() {
  viewJournal.value = true;
  step.value = 1; // hide wizard
}

// Close Journal and return to start
function closeJournal() {
  viewJournal.value = false;
  step.value = 1;
}

// UI helpers
const reminderPreview = computed(() => {
  if (!reminder.value.enabled) return 'Not set';
  const t = reminder.value.time || '07:00';
  return `${t} — "${answers.value.statement || 'Your Why I Serve' }"`;
});

function formatDate(iso) {
  const d = new Date(iso);
  return d.toLocaleString();
}

// Archive entry
function archiveEntry(idx) {
  journal.value[idx].archived = !journal.value[idx].archived;
  persistJournal();
}

// Load entry for editing
function loadForEdit(idx) {
  const e = journal.value[idx];
  answers.value.prompt1 = e.prompt1 || '';
  answers.value.prompt2 = e.prompt2 || '';
  answers.value.prompt3 = e.prompt3 || '';
  answers.value.statement = e.statement || '';
  reminder.value.enabled = !!e.reminder;
  reminder.value.time = e.reminder?.time || reminder.value.time || '07:00';
  // navigate to step 5 to edit statement
  viewJournal.value = false;
  step.value = 5;
}

// Use entry as reminder (quick)
function useAsReminder(entry) {
  reminder.value.enabled = true;
  reminder.value.time = entry.reminder?.time || reminder.value.time || '07:00';
  // show preview (already bound)
  alert('Reminder set from this entry (preview visible below). To save it, edit & Save My Statement.');
}

// Edit latest saved statement (from completion screen)
function editLatest() {
  const last = journal.value[journal.value.length - 1];
  if (!last) return;
  answers.value.prompt1 = last.prompt1 || '';
  answers.value.prompt2 = last.prompt2 || '';
  answers.value.prompt3 = last.prompt3 || '';
  answers.value.statement = last.statement || '';
  reminder.value.enabled = !!last.reminder;
  reminder.value.time = last.reminder?.time || reminder.value.time || '07:00';
  step.value = 5;
}

// Clear all
function clearAll() {
  if (!confirm('Clear all saved motivation journal entries?')) return;
  journal.value = [];
  persistJournal();
}

// Voice placeholder
function useVoice(field) {
  alert('Voice-to-text not yet enabled in this demo. Integrate Web Speech API or native implementation here.');
}

// Close journal/show home
function closeJournalView() {
  viewJournal.value = false;
  step.value = 1;
}

// Expose computed sorted journal (most recent first)
const journalSorted = computed(() => {
  return [...journal.value].sort((a, b) => b.id - a.id);
});

// On component mount, ensure defaults
if (!Array.isArray(journal.value)) journal.value = [];
persistJournal();
</script>

<style scoped>
/* mobile-first, centered UI */
.container {
  max-width: 680px;
  margin: 0 auto;
}

/* center buttons and content on small screens */
.btn {
  font-weight: 600;
}

/* ensure list items wrap nicely on mobile */
.list-group-item {
  word-break: break-word;
}

/* rounded-pill buttons are used for a modern mobile feel */
textarea.form-control {
  resize: vertical;
  min-height: 72px;
}

/* keep things visually compact on mobile */
@media (max-width: 576px) {
  .container {
    padding-left: 16px;
    padding-right: 16px;
  }
}
</style>
