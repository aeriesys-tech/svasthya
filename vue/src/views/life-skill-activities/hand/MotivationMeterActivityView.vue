<template>
    <div class="meter-container">
        <!-- Header -->
        <h1 class="title">Motivation Meter</h1>
        <p class="subtitle">How are you feeling today? What’s lifting you — or holding you down?</p>

        <!-- Launch / Start -->
        <div v-if="!started" class="card center-card">
            <p class="lead text-center">
                Quick check-in: Track how you’re doing and spot what’s helping or hurting your day.
            </p>
            <button class="btn-primary btn-block" @click="startCheckIn">Start Check-In</button>
        </div>

        <!-- Check-in Form -->
        <div v-else class="card p-3">
            <div class="centered">

                <!-- Mood -->
                <div class="scale-row">
                    <label class="scale-label">Mood</label>
                    <div class="scale-control">
                        <div class="icons-row">
                            <span>😞</span><span>😐</span><span>🙂</span><span>😊</span><span>😁</span>
                        </div>

                        <input
                            type="range"
                            min="1"
                            max="5"
                            v-model.number="mood"
                            @input="updateFill($event, 'mood')"
                            :style="rangeStyle.mood"
                        />

                        <div class="small text-muted">Selected: {{ mood }}</div>
                    </div>
                </div>

                <!-- Energy -->
                <div class="scale-row">
                    <label class="scale-label">Energy</label>
                    <div class="scale-control">
                        <div class="icons-row small-icons">
                            <span>🥱</span><span>😴</span><span>😌</span><span>⚡</span><span>🚀</span>
                        </div>

                        <input
                            type="range"
                            min="1"
                            max="5"
                            v-model.number="energy"
                            @input="updateFill($event, 'energy')"
                            :style="rangeStyle.energy"
                        />

                        <div class="small text-muted">Selected: {{ energy }}</div>
                    </div>
                </div>

                <!-- Motivation -->
                <div class="scale-row">
                    <label class="scale-label">Motivation</label>
                    <div class="scale-control">
                        <div class="icons-row small-icons">
                            <span>⬇️</span><span>↘️</span><span>➡️</span><span>↗️</span><span>⬆️</span>
                        </div>

                        <input
                            type="range"
                            min="1"
                            max="5"
                            v-model.number="motivation"
                            @input="updateFill($event, 'motivation')"
                            :style="rangeStyle.motivation"
                        />

                        <div class="small text-muted">Selected: {{ motivation }}</div>
                    </div>
                </div>

                <!-- Prompt / Note -->
                <div class="prompt-box mt-2">
                    <div class="fw-semibold text-center">{{ currentPrompt }}</div>

                    <input
                        type="text"
                        v-model="note"
                        placeholder="Type your quick note..."
                        class="text-input mt-2"
                        maxlength="250"
                    />

                    <div class="tag-row mt-2">
                        <button
                            v-for="t in tagsList"
                            :key="t"
                            type="button"
                            :class="['tag-btn', chosenTags.includes(t) ? 'tag-active' : '' ]"
                            @click="toggleTag(t)"
                        >
                            {{ t }}
                        </button>
                    </div>
                </div>

                <div class="mt-3 centered">
                    <button class="btn-primary btn-block" :disabled="!canSubmit" @click="submitCheckIn">
                        Submit Check-In
                    </button>
                </div>
            </div>
        </div>

        <!-- Feedback -->
        <div v-if="lastTip" class="card mt-3 feedback-card">
            <div class="fw-semibold text-center">Quick Insight</div>
            <div class="mt-2 text-center small">{{ lastTip }}</div>
        </div>

        <!-- Trends -->
        <div class="card mt-3">
            <div class="centered">
                <h3 class="small-title">Your 7-day Trends</h3>

                <div class="chart-wrap">
                    <svg :width="chartW" :height="chartH">
                        <!-- Grid -->
                        <g v-for="i in 4" :key="i" :transform="'translate(0,' + ((i)*(chartH-40)/4 + 20) + ')'">
                            <line x1="40" y1="0" :x2="chartW-10" y2="0" stroke="#eee" />
                        </g>

                        <!-- Lines -->
                        <polyline :points="pointsFor('mood')" fill="none" stroke="#ff6b6b" stroke-width="3" />
                        <polyline :points="pointsFor('energy')" fill="none" stroke="#ffd93d" stroke-width="3" />
                        <polyline :points="pointsFor('motivation')" fill="none" stroke="#4dabf7" stroke-width="3" />

                        <!-- Dots -->
                        <g v-for="(d,idx) in last7" :key="'dot'+idx">
                            <circle :cx="xFor(idx)" :cy="yForValue(d.mood)" r="6" fill="#fff" stroke="#ff6b6b" stroke-width="2" @click="openDay(idx)" />
                            <circle :cx="xFor(idx)" :cy="yForValue(d.energy)" r="6" fill="#fff" stroke="#ffd93d" stroke-width="2" @click="openDay(idx)" />
                            <circle :cx="xFor(idx)" :cy="yForValue(d.motivation)" r="6" fill="#fff" stroke="#4dabf7" stroke-width="2" @click="openDay(idx)" />
                        </g>

                    </svg>
                </div>

                <div class="legend mt-2">
                    <span class="legend-item"><span class="dot" style="background:#ff6b6b"></span> Mood</span>
                    <span class="legend-item"><span class="dot" style="background:#ffd93d"></span> Energy</span>
                    <span class="legend-item"><span class="dot" style="background:#4dabf7"></span> Motivation</span>
                </div>

                <!-- Day Preview -->
                <div v-if="selectedDay !== null" class="day-preview mt-3">
                    <div class="fw-semibold text-center">Preview — {{ formattedDay(selectedDay) }}</div>

                    <div class="small text-center mt-1">
                        Mood: {{ last7[selectedDay].mood }} •
                        Energy: {{ last7[selectedDay].energy }} •
                        Motivation: {{ last7[selectedDay].motivation }}
                    </div>

                    <div class="mt-2 text-center small text-muted">
                        Note: {{ last7[selectedDay].note || '—' }}
                    </div>

                    <div class="mt-2 text-center">
                        <button class="btn-outline btn-sm" @click="selectedDay = null">Close</button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Streak & Badges -->
        <div class="card mt-3 centered">
            <div class="small-title">Streaks & Badges</div>
            <div class="mt-2 small text-center">Check-in streak: <strong>{{ streak }}</strong> days</div>
            <div class="mt-2 small text-center">
                Badges:
                <span v-if="badges.length">{{ badges.join(', ') }}</span>
                <span v-else>—</span>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";

/* -------- STATE -------- */
const started = ref(false);
const mood = ref(3);
const energy = ref(3);
const motivation = ref(3);

/* ====== SLIDER FILL LOGIC ====== */
const rangeStyle = ref({
    mood: "",
    energy: "",
    motivation: ""
});

function updateFill(event, type) {
    const min = +event.target.min;
    const max = +event.target.max;
    const val = +event.target.value;

    const percent = ((val - min) / (max - min)) * 100;

    const colors = {
        mood: "#ff6b6b",
        energy: "#ffd93d",
        motivation: "#4dabf7"
    };

    rangeStyle.value[type] = `
        background: linear-gradient(
            to right,
            ${colors[type]} 0%,
            ${colors[type]} ${percent}%,
            #ddd ${percent}%,
            #ddd 100%
        );
    `;
}

/* init fill */
onMounted(() => {
    updateFill({ target: { min: 1, max: 5, value: mood.value } }, "mood");
    updateFill({ target: { min: 1, max: 5, value: energy.value } }, "energy");
    updateFill({ target: { min: 1, max: 5, value: motivation.value } }, "motivation");
});

/* ---------- REST OF YOUR LOGIC (UNCHANGED) ---------- */
const note = ref("");
const chosenTags = ref([]);

const tagsList = ["duty", "family", "stress", "conflict", "training", "break"];

const prompts = [
    "What's one thing helping you today?",
    "What's draining your energy right now?",
    "Anything on your mind today?",
    "What small win did you have today?"
];
const currentPrompt = ref(prompts[Math.floor(Math.random() * prompts.length)]);

const STORAGE_KEY = "motivation_meter_entries_v1";
const entries = ref([]);

function loadEntries() {
    try {
        entries.value = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    } catch {
        entries.value = [];
    }
}
loadEntries();

function dateKey(d) {
    return d.toISOString().slice(0, 10);
}

const last7 = computed(() => {
    const now = new Date();
    const list = [];
    for (let i = 6; i >= 0; i--) {
        const d = new Date(now);
        d.setDate(now.getDate() - i);
        const key = dateKey(d);
        const found = entries.value.find((e) => e.key === key);
        list.push(
            found || {
                key,
                date: d.toISOString(),
                mood: 0,
                energy: 0,
                motivation: 0,
                note: "",
                tags: []
            }
        );
    }
    return list;
});

const canSubmit = computed(() => mood.value && energy.value && motivation.value);

function startCheckIn() {
    started.value = true;
}

function toggleTag(t) {
    const idx = chosenTags.value.indexOf(t);
    if (idx === -1) chosenTags.value.push(t);
    else chosenTags.value.splice(idx, 1);
}

const lastTip = ref("");

function submitCheckIn() {
    const now = new Date();
    const key = dateKey(now);

    const payload = {
        key,
        date: now.toISOString(),
        mood: mood.value,
        energy: energy.value,
        motivation: motivation.value,
        note: note.value.trim(),
        tags: [...chosenTags.value]
    };

    const idx = entries.value.findIndex((e) => e.key === key);
    if (idx > -1) entries.value.splice(idx, 1, payload);
    else entries.value.push(payload);

    localStorage.setItem(STORAGE_KEY, JSON.stringify(entries.value));

    computeTip();
    computeBadges();

    started.value = false;
    note.value = "";
    chosenTags.value = [];
    currentPrompt.value = prompts[Math.floor(Math.random() * prompts.length)];

    /* refresh fills */
    updateFill({ target: { min: 1, max: 5, value: mood.value } }, "mood");
    updateFill({ target: { min: 1, max: 5, value: energy.value } }, "energy");
    updateFill({ target: { min: 1, max: 5, value: motivation.value } }, "motivation");
}

function computeTip() {
    const recent = entries.value.slice(-6);
    const low = recent.filter((e) => e.energy <= 2).length;
    const last = entries.value[entries.value.length - 1];

    if (low >= 3) {
        lastTip.value = "You've had several low-energy check-ins — consider a small break.";
        return;
    }
    if (last.motivation >= 4 && last.energy <= 2) {
        lastTip.value = "High motivation but low energy — a quick recharge might help.";
        return;
    }
    if (last.mood <= 2) {
        lastTip.value = "Tough day — focus on one tiny win to lift your mood.";
        return;
    }
    lastTip.value = "Nice work keeping track — habits like this build clarity.";
}

/* BADGES */
const streak = ref(0);
const badges = ref([]);

function computeBadges() {
    const keys = entries.value.map((e) => e.key);
    const set = new Set(keys);

    let count = 0;
    const today = new Date();
    for (let i = 0; ; i++) {
        const d = new Date(today);
        d.setDate(today.getDate() - i);
        if (set.has(dateKey(d))) count++;
        else break;
    }
    streak.value = count;

    const b = [];
    if (streak.value >= 3) b.push("3-Day Streak");
    if (streak.value >= 5) b.push("5-Day Streak");
    if (entries.value.length >= 10) b.push("Consistent Tracker");
    badges.value = b;
}

onMounted(() => {
    loadEntries();
    computeBadges();
});

const chartW = 360;
const chartH = 180;

function xFor(i) {
    const left = 40;
    const right = chartW - 30;
    return left + ((right - left) / 6) * i;
}

function yForValue(v) {
    const top = 20;
    const bottom = chartH - 20;
    const val = v === 0 ? 0.2 : v;
    return bottom - ((bottom - top) * (val / 5));
}

function pointsFor(type) {
    return last7.value
        .map((d, i) => `${xFor(i)},${yForValue(d[type])}`)
        .join(" ");
}

const selectedDay = ref(null);

function openDay(i) {
    selectedDay.value = i;
}

function formattedDay(i) {
    return new Date(last7.value[i].date).toLocaleDateString();
}

watch(entries, computeBadges, { deep: true });
</script>

<style scoped>
/* ----------- FULL CLEAN CSS WITH SLIDER FIX ----------- */

.meter-container {
    padding: 12px;
    max-width: 560px;
    margin: 0 auto;
    font-family: system-ui;
}

.scale-row {
    margin-bottom: 18px;
}

.scale-label {
    font-weight: 600;
    margin-bottom: 4px;
    display: block;
}

.scale-control input[type="range"] {
    width: 100%;
    height: 10px;
    border-radius: 10px;
    appearance: none;
    -webkit-appearance: none;
    background: #ddd;
    outline: none;
    margin-top: 4px;
}

/* RANGE THUMB */
.scale-control input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 22px;
    width: 22px;
    border-radius: 50%;
    background: #444;
    cursor: pointer;
}

.scale-control input[type="range"]::-moz-range-thumb {
    height: 22px;
    width: 22px;
    border-radius: 50%;
    background: #444;
    cursor: pointer;
}

.icons-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
    font-size: 20px;
}

.small-icons span {
    font-size: 18px;
}

/* Buttons / Cards / Text */
.card {
    background: #fff;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    margin-bottom: 8px;
}

.btn-primary {
    background: #d80621;
    color: #fff;
    border: none;
    padding: 10px;
    width: 100%;
    border-radius: 8px;
    font-size: 16px;
}

.btn-outline {
    padding: 6px 12px;
    border: 1px solid #d80621;
    background: white;
    border-radius: 8px;
}

/* Tags */
.tag-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.tag-btn {
    padding: 4px 10px;
    font-size: 13px;
    border-radius: 8px;
    border: 1px solid #aaa;
    background: #f7f7f7;
}

.tag-active {
    background: #d80621;
    color: white;
    border-color: #d80621;
}

/* Trends Chart */
.chart-wrap {
    margin-top: 10px;
}

.legend {
    display: flex;
    justify-content: center;
    gap: 14px;
}

.legend-item .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
}
</style>
