<template>
<div class="container py-4">
    <h2 class="text-center mb-4">Mission-Oriented Weekly Planning</h2>

    <!-- Progress indicator -->
    <!-- <div class="mb-3 d-flex justify-content-between align-items-center">
        <small class="text-muted">Step {{ step }} / 3</small>
        <div>
            <button class="btn btn-secondary btn-sm me-2" @click="loadFromStorage">Load</button>
            <button class="btn btn-outline-success btn-sm" @click="exportPlan">Export JSON</button>
        </div>
    </div> -->

    <!-- STEP 1: Pick / edit three missions -->
    <div v-if="step === 1" class="card p-3 mb-4">
        <h5 class="mb-3">Step 1 — Choose three weekly missions</h5>
        <p class="text-muted small mb-3">Choose a template or write a custom mission for each domain.</p>

        <div class="row g-3">
            <div class="col-12 col-md-4" v-for="(domain, idx) in domains" :key="domain.key">
                <div class="card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ domain.label }}</h6>
                        <small class="text-muted">{{ domain.key.toUpperCase() }}</small>
                    </div>

                    <!-- Template choices -->
                    <div class="mb-2">
                        <div class="small text-muted mb-1">Use a sample</div>
                        <div class="d-flex flex-wrap gap-2">
                            <button v-for="t in templates[domain.key]" :key="t.title"
                                    class="btn btn-outline-success btn-sm"
                                    @click="applyTemplate(idx, t)">
                                {{ t.title }}
                            </button>
                        </div>
                    </div>

                    <!-- Title & description -->
                    <div class="mb-2">
                        <label class="form-label small">Title</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="missions[idx].title" maxlength="50" placeholder="Enter goal title" />
                        <div class="small text-muted mt-1">{{ missions[idx].title.length }}/50</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Description (optional)</label>
                        <textarea class="form-control form-control-sm" rows="3"
                                  v-model="missions[idx].description" maxlength="250"
                                  placeholder="Why is this important?"></textarea>
                        <div class="small text-muted mt-1">{{ missions[idx].description.length }}/250</div>
                    </div>

                    <div class="d-flex justify-content-between mt-auto">
                        <button class="btn btn-secondary btn-sm" @click="clearMission(idx)">Clear</button>
                        <button class="btn btn-success btn-sm" :disabled="!missions[idx].title.trim()"
                                @click="openTaskEditor(idx)">
                            Add tasks
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-end gap-2">
            <button class="btn btn-secondary" @click="resetAll">Reset</button>
            <button class="btn btn-success" :disabled="!allTitlesFilled" @click="step = 2">Next: Tasks</button>
        </div>
    </div>

    <!-- STEP 2: Task breakdown (for selected mission index) -->
    <div v-if="step === 2" class="card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Step 2 — Weekly task breakdown</h5>
            <div>
                <button class="btn btn-secondary btn-sm me-2 w-100 mb-1" @click="step = 1">Back</button>
                <button class="btn btn-success btn-sm" @click="step = 3">Preview Week</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-5">
                <div class="mb-3">
                    <label class="fw-bold">Select mission to edit</label>
                    <select class="form-select" v-model.number="editingMission">
                        <option v-for="(m, i) in missions" :key="i" :value="i">
                            {{ domains[i].label }} — {{ m.title || '(empty)' }}
                        </option>
                    </select>
                </div>

                <div class="card p-3 mb-3">
                    <h6>Tasks for: <span class="fw-semibold">{{ missions[editingMission].title || '—' }}</span></h6>

                    <!-- Add new task -->
                    <div class="mb-2">
                        <input type="text" v-model="newTaskName" class="form-control form-control-sm" placeholder="Task name (e.g., Walk 20 min)" />
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Frequency</label>
                        <select class="form-select form-select-sm" v-model="newTaskFreq">
                            <option value="daily">Daily</option>
                            <option value="alternate">Alternate days</option>
                            <option value="nperweek">N times / week</option>
                            <option value="specific">Specific days</option>
                        </select>
                    </div>

                    <div v-if="newTaskFreq === 'nperweek'" class="mb-2">
                        <label class="form-label small">Times per week</label>
                        <input type="number" class="form-control form-control-sm" min="1" max="7" v-model.number="newTaskN" />
                    </div>

                    <div v-if="newTaskFreq === 'specific'" class="mb-2">
                        <label class="form-label small">Pick days</label>
                        <div class="d-flex gap-1 flex-wrap">
                            <button v-for="(d,i) in weekdays" :key="d"
                                    :class="['btn btn-sm', newTaskDays.includes(i) ? 'btn-success' : 'btn-outline-secondary']"
                                    @click="toggleNewDay(i)">{{ d.slice(0,3) }}</button>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Notes (optional)</label>
                        <input type="text" v-model="newTaskNote" class="form-control form-control-sm" placeholder="Short note" />
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-sm" @click="addTask">Add Task</button>
                        <button class="btn btn-secondary btn-sm" @click="resetNewTask">Clear</button>
                    </div>
                </div>
            </div>

            <!-- Task list for selected mission -->
            <div class="col-12 col-md-7">
                <div class="mb-3">
                    <h6 class="mb-2">Existing tasks</h6>
                    <div v-if="missions[editingMission].tasks.length === 0" class="text-muted small">No tasks yet.</div>

                    <div class="list-group">
                        <div v-for="(t, ti) in missions[editingMission].tasks" :key="ti" class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">{{ t.name }}</div>
                                <div class="small text-muted">
                                    <span v-if="t.freq === 'daily'">Daily</span>
                                    <span v-if="t.freq === 'alternate'">Alternate days</span>
                                    <span v-if="t.freq === 'nperweek'">{{ t.n }} times / week</span>
                                    <span v-if="t.freq === 'specific'">Days: {{ mapDays(t.days) }}</span>
                                    <span v-if="t.note"> • {{ t.note }}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm" @click="editTask(editingMission, ti)">Edit</button>
                                <button class="btn btn-secondary btn-sm" @click="removeTask(editingMission, ti)">Delete</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-success btn-sm" @click="autoDistribute(editingMission)">Auto-assign to week</button>
                        <button class="btn btn-outline-success btn-sm ms-2" @click="clearTasks(editingMission)">Clear tasks</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 3: Weekly calendar preview & Today checklist -->
    <div v-if="step === 3" class="card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Step 3 — Week Preview & Daily Checklist</h5>
            <div>
                <button class="btn btn-secondary btn-sm me-2" @click="step = 2">Back</button>
                <button class="btn btn-success btn-sm" @click="openReflection">Finish & Reflect</button>
            </div>
        </div>

        <!-- weekly calendar -->
        <div class="mb-3">
            <h6 class="mb-2">Weekly Planner (this week)</h6>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <div v-for="(day, i) in weekdays" :key="day" class="p-2 rounded border text-center" style="min-width:92px">
                    <div class="small fw-semibold">{{ day }}</div>
                    <div class="small text-muted">{{ calendarDayLabel(i) }}</div>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <div v-for="(dayTasks, di) in tasksByDay" :key="di" class="p-2 rounded" :class="{'bg-light': true}">
                    <div class="small fw-semibold mb-1">{{ weekdays[di] }}</div>
                    <div class="d-flex flex-wrap gap-2">
                        <span v-for="(task, ti) in dayTasks" :key="ti" class="badge bg-secondary text-white">
                            {{ task.fromDomain }}: {{ task.name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's checklist -->
        <div>
            <h6>Today's Checklist ({{ todayLabel }})</h6>
            <div v-if="todayTasks.length === 0" class="text-muted small">No tasks scheduled for today.</div>

            <div v-for="(t, i) in todayTasks" :key="i" class="d-flex align-items-center gap-2 mb-2">
                <input type="checkbox" class="form-check-input" v-model="t.done" @change="saveTodayStatus" />
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ t.fromDomain }} • {{ t.name }}</div>
                    <div class="small text-muted">{{ t.note }}</div>
                </div>
                <input type="text" v-model="t.noteQuick" placeholder="Quick note" class="form-control form-control-sm w-25" />
            </div>

            <div class="mt-2">
                <button class="btn btn-success btn-sm" @click="saveTodayStatus">Save Today</button>
                <button class="btn btn-secondary btn-sm ms-2" @click="resetToday">Reset</button>
            </div>
        </div>
    </div>

    <!-- Reflection (modal-like area) -->
    <div v-if="showReflectionModal" class="card p-3 mb-4">
        <h5 class="mb-2">Reflection & Journal</h5>
        <small class="text-muted">Answer any of these to finish the week</small>

        <div class="mt-3">
            <label class="form-label small">Prompt</label>
            <div class="p-2 rounded bg-light mb-2">{{ activePrompt }}</div>

            <textarea v-model="reflectionEntry" class="form-control mb-2" rows="4" placeholder="Write your reflection..."></textarea>

            <div class="d-flex gap-2">
                <button class="btn btn-success" @click="saveReflectionEntry">Save Reflection</button>
                <button class="btn btn-secondary" @click="closeReflection">Close</button>
            </div>
        </div>
    </div>

    <!-- Peer accountability -->
    <!-- <div class="card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Peer Accountability (optional)</h6>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" v-model="peerEnabled" id="peerToggle" />
                <label class="form-check-label small" for="peerToggle">Enable</label>
            </div>
        </div>

        <div v-if="peerEnabled" class="d-flex gap-2 align-items-center">
            <input type="text" v-model="peerCode" class="form-control form-control-sm w-auto" placeholder="Enter pair code" />
            <button class="btn btn-success btn-sm" @click="joinPeer">Join</button>
            <div v-if="peerJoined" class="small text-success ms-2">Paired (mock)</div>
        </div>
    </div> -->
</div>
</template>

<script setup>
import { ref, computed } from "vue";

/* STEP control */
const step = ref(1);

/* domains */
const domains = [
  { key: "physical", label: "Physical Wellness" },
  { key: "professional", label: "Professional Growth" },
  { key: "personal", label: "Personal Well-being" }
];

/* mission templates (short list) */
const templates = {
  physical: [
    { title: "Walk 20 min 3×/week", description: "Short daily walks or 3 longer walks across the week to build stamina." },
    { title: "Stretch 10 min daily", description: "Daily mobility routine to reduce stiffness." },
    { title: "Improve sleep hygiene", description: "No screens 30 min before bed; consistent sleep time." }
  ],
  professional: [
    { title: "Finish pending reports", description: "Complete outstanding administrative tasks this week." },
    { title: "Complete 1 micro-course", description: "Finish one short training module for skills update." },
    { title: "Improve handover notes", description: "Make concise, standard shift handover notes." }
  ],
  personal: [
    { title: "Family time 3× this week", description: "Plan short quality interactions with family or close friend." },
    { title: "Read 10 pages daily", description: "Short reading routine to unwind and learn." },
    { title: "Evening reflection", description: "3-minute journal entry each evening." }
  ]
};

/* missions: 3 objects */
const missions = ref([
  { domain: "physical", title: "", description: "", tasks: [] },
  { domain: "professional", title: "", description: "", tasks: [] },
  { domain: "personal", title: "", description: "", tasks: [] }
]);

/* convenience */
const allTitlesFilled = computed(() => missions.value.every(m => m.title && m.title.trim().length > 0));

/* apply template */
function applyTemplate(index, tmpl) {
  missions.value[index].title = tmpl.title;
  missions.value[index].description = tmpl.description;
  saveToStorage();
}

/* clear mission */
function clearMission(i) {
  missions.value[i].title = "";
  missions.value[i].description = "";
  missions.value[i].tasks = [];
  saveToStorage();
}

/* open task editor for domain index */
const editingMission = ref(0);

/* new task inputs */
const newTaskName = ref("");
const newTaskFreq = ref("daily"); // daily | alternate | nperweek | specific
const newTaskN = ref(3);
const newTaskDays = ref([]); // indices 0..6
const newTaskNote = ref("");

const weekdays = ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];

/* toggle specific day selection */
function toggleNewDay(i) {
  const pos = newTaskDays.value.indexOf(i);
  if (pos === -1) newTaskDays.value.push(i);
  else newTaskDays.value.splice(pos,1);
}

/* reset new task fields */
function resetNewTask() {
  newTaskName.value = "";
  newTaskFreq.value = "daily";
  newTaskN.value = 3;
  newTaskDays.value = [];
  newTaskNote.value = "";
}

/* add task to mission */
function addTask() {
  if (!newTaskName.value.trim()) return alert("Enter a task name.");
  const t = {
    name: newTaskName.value.trim(),
    freq: newTaskFreq.value,
    n: newTaskFreq.value === "nperweek" ? (newTaskN.value || 1) : undefined,
    days: newTaskFreq.value === "specific" ? [...newTaskDays.value] : undefined,
    note: newTaskNote.value || ""
  };
  missions.value[editingMission.value].tasks.push(t);
  resetNewTask();
  saveToStorage();
}

/* edit/remove task */
function removeTask(mi, ti) {
  missions.value[mi].tasks.splice(ti,1);
  saveToStorage();
}

/* map days array to pretty string */
function mapDays(days = []) {
  if (!days || days.length===0) return "—";
  return days.map(d=>weekdays[d]).join(", ");
}

/* auto distribute tasks across the week (naive) */
function autoDistribute(mi) {
  const tasks = missions.value[mi].tasks;
  // for tasks with freq nperweek but no days, pick evenly
  const weekLen = 7;
  for (const t of tasks) {
    if (t.freq === "nperweek" && (!t.days || t.days.length === 0)) {
      const n = Math.max(1, Math.min(7, t.n || 1));
      t.days = [];
      for (let k=0;k<n;k++) {
        t.days.push((k*Math.floor(weekLen/n)) % 7);
      }
    } else if (t.freq === "daily") {
      t.days = [0,1,2,3,4,5,6];
    } else if (t.freq === "alternate") {
      t.days = [0,2,4,6];
    } else if (t.freq === "specific") {
      t.days = t.days || [];
    }
  }
  saveToStorage();
  alert("Tasks auto-assigned to days where applicable.");
}

/* clear tasks of a mission */
function clearTasks(mi) {
  if (!confirm("Clear all tasks for this mission?")) return;
  missions.value[mi].tasks = [];
  saveToStorage();
}

/* calendar helper: create tasksByDay where each entry is array of tasks assigned for that day */
const tasksByDay = computed(() => {
  const arr = Array.from({length:7}, ()=>[]);
  missions.value.forEach((m, mi)=>{
    m.tasks.forEach(t=>{
      const days = t.days || [];
      // if no days assigned, try to infer from freq
      if (!days || days.length===0) {
        if (t.freq === "daily") {
          for (let d=0; d<7; d++) arr[d].push({...t, fromDomain: capitalize(m.domain)});
        } else if (t.freq === "alternate") {
          [0,2,4,6].forEach(d=>arr[d].push({...t, fromDomain: capitalize(m.domain)}));
        } else if (t.freq === "nperweek") {
          // distribute first n days
          const n = Math.max(1, Math.min(7, t.n || 1));
          for (let k=0;k<n;k++) arr[k].push({...t, fromDomain: capitalize(m.domain)});
        } else {
          // skip
        }
      } else {
        days.forEach(d => arr[d].push({...t, fromDomain: capitalize(m.domain)}));
      }
    });
  });
  return arr;
});

function calendarDayLabel(i) {
  return new Date().toLocaleDateString(undefined, {weekday:'short'});
}

/* Today helpers */
function getTodayIndex() {
  const today = new Date();
  // JS getDay: Sun=0...Sat=6 ; we prefer Mon=0..Sun=6
  const jsDay = today.getDay();
  return (jsDay + 6) % 7;
}
const todayIndex = ref(getTodayIndex());

/* build today's task list from tasksByDay */
const todayTasks = computed(() => {
  // clone tasks so we can add done flag and quick note
  const arr = tasksByDay.value[todayIndex.value] || [];
  return arr.map(t => ({
    fromDomain: t.fromDomain,
    name: t.name,
    note: t.note || "",
    done: false,
    noteQuick: ""
  }));
});

/* save today status (persist in localStorage under 'planner_done_[date]') */
function saveTodayStatus() {
  const dateKey = new Date().toISOString().slice(0,10);
  const data = todayTasks.value.map(t => ({name: t.name, domain: t.fromDomain, done: t.done, note: t.noteQuick}));
  localStorage.setItem("planner_done_" + dateKey, JSON.stringify(data));
  alert("Today's checklist saved.");
}

/* reset today's transient marks (does not delete stored history) */
function resetToday() {
  // remove today's saved marks
  const dateKey = new Date().toISOString().slice(0,10);
  localStorage.removeItem("planner_done_" + dateKey);
  alert("Today's checklist reset.");
}

/* reflection prompts */
const prompts = [
  "What progress made you proud this week?",
  "What challenged your consistency?",
  "What will you try differently next week?"
];
const showReflectionModal = ref(false);
const activePrompt = ref(prompts[0]);
const reflectionEntry = ref("");
function openReflection() {
  activePrompt.value = prompts[Math.floor(Math.random()*prompts.length)];
  reflectionEntry.value = "";
  showReflectionModal.value = true;
}
function closeReflection() { showReflectionModal.value = false; }
function saveReflectionEntry() {
  const key = "planner_reflections_v1";
  const arr = JSON.parse(localStorage.getItem(key) || "[]");
  arr.unshift({ date: new Date().toISOString(), prompt: activePrompt.value, text: reflectionEntry.value });
  localStorage.setItem(key, JSON.stringify(arr));
  showReflectionModal.value = false;
  alert("Reflection saved. Good work.");
}

/* peer accountability (frontend stub) */
const peerEnabled = ref(false);
const peerCode = ref("");
const peerJoined = ref(false);
function joinPeer() {
  if (!peerCode.value.trim()) return alert("Enter a pair code.");
  peerJoined.value = true;
  alert("Joined pair (mock). Peer features require backend integration.");
}

/* small helpers */
function capitalize(s) { return s[0].toUpperCase() + s.slice(1); }

/* storage: save/load missions */
function saveToStorage() {
  localStorage.setItem("planner_missions_v1", JSON.stringify(missions.value));
}
function loadFromStorage() {
  const raw = localStorage.getItem("planner_missions_v1");
  if (raw) {
    try {
      const parsed = JSON.parse(raw);
      if (Array.isArray(parsed) && parsed.length === 3) {
        missions.value = parsed;
        alert("Plan loaded.");
        return;
      }
    } catch(e) {}
  }
  alert("No saved plan found.");
}

/* small UI helpers */
function openTaskEditor(i) { editingMission.value = i; step.value = 2; }
function editTask(mi, ti) {
  const t = missions.value[mi].tasks[ti];
  // load into new task fields for editing; simple replace flow
  editingMission.value = mi;
  newTaskName.value = t.name;
  newTaskFreq.value = t.freq || "daily";
  newTaskN.value = t.n || 1;
  newTaskDays.value = t.days ? [...t.days] : [];
  newTaskNote.value = t.note || "";
  // remove existing so add will re-insert (simple edit flow)
  missions.value[mi].tasks.splice(ti,1);
  saveToStorage();
}

/* initial load */
(function init() {
  const stored = localStorage.getItem("planner_missions_v1");
  if (stored) {
    try { const p = JSON.parse(stored); if (Array.isArray(p) && p.length===3) missions.value = p; } catch(e){}
  }
})();

/* export plan */
function exportPlan() {
  const data = { meta: { exportedAt: new Date().toISOString() }, missions: missions.value };
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: "application/json" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "mission-plan.json";
  a.click();
  URL.revokeObjectURL(url);
}

</script>

<style scoped>
.card { border-radius: 0.6rem; }
.badge { font-size: 0.85rem; }
.btn { border-radius: 0.45rem; }
.list-group-item { border: none; border-bottom: 1px solid rgba(0,0,0,0.05); }
.bg-light { background: #f8f9fa; }
</style>
