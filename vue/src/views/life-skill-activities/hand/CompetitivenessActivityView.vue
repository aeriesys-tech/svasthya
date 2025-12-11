<template>
  <div class="badgeboard-root">
    <!-- Header -->
    <header class="header-card">
      <h1 class="title">Badge Board</h1>
      <p class="subtitle">Choose goals, log completions and earn streak badges.</p>
    </header>

    <!-- STEP 1: Select Goals -->
    <section v-if="stage === 'choose'" class="card">
      <h2 class="section-title">Choose up to <strong>3</strong> goals</h2>

      <div class="goals-grid">
        <label
          v-for="g in goalCatalog"
          :key="g.id"
          :class="['goal-card', selectedGoalIds.includes(g.id) ? 'goal-active' : '']"
        >
          <input
            type="checkbox"
            v-model="selectedGoalIds"
            :value="g.id"
            :disabled="selectedGoalIds.length >= 3 && !selectedGoalIds.includes(g.id)"
            class="visually-hidden"
          />
          <div class="goal-name">{{ g.title }}</div>
          <div class="goal-meta">{{ g.type === 'daily' ? 'Daily' : 'Weekly' }}</div>
          <div class="goal-desc">{{ g.desc }}</div>
        </label>
      </div>

      <div class="actions-row">
        <button class="btn-primary" :disabled="selectedGoalIds.length === 0" @click="confirmGoals">
          Continue
        </button>
        <button class="btn-outline" @click="resetAll">Reset</button>
      </div>
    </section>

    <!-- STEP 2: Badge Board & Logging -->
    <section v-if="stage === 'board'" class="card">
      <h2 class="section-title">Your Badge Board</h2>

      <div class="board-note">Tap a goal to log today's completion. Weekly goals log for the current calendar week.</div>

      <div class="user-goals">
        <div
          v-for="goal in userGoals"
          :key="goal.id"
          class="user-goal-card"
        >
          <div class="goal-header">
            <div>
              <div class="goal-title">{{ goal.title }}</div>
              <div class="goal-type">{{ goal.type === 'daily' ? 'Daily Habit' : 'Weekly Habit' }}</div>
            </div>

            <div class="goal-actions">
              <button
                class="btn-log"
                :class="hasCompletedToday(goal.id) ? 'logged' : ''"
                @click="toggleLogToday(goal.id)"
                :title="hasCompletedToday(goal.id) ? 'Undo today' : 'Mark as done today'"
              >
                {{ hasCompletedToday(goal.id) ? 'Done' : 'Log' }}
              </button>
            </div>
          </div>

          <div class="progress-row">
            <div class="progress-left">
              <div class="progress-label">Streak</div>
              <div class="progress-value">{{ computeStreak(goal.id) }} {{ goal.type === 'daily' ? 'days' : 'weeks' }}</div>
            </div>

            <div class="progress-bar-wrap" role="progressbar" :aria-valuenow="streakPercent(goal.id)" aria-valuemin="0" aria-valuemax="100">
              <div class="progress-bar-bg">
                <div class="progress-bar-fill" :style="{ width: streakPercent(goal.id) + '%' }"></div>
              </div>
            </div>
          </div>

          <div class="badges-row">
            <span
              v-for="b in badgesForGoal(goal.id)"
              :key="b.id"
              class="badge-pill"
              :class="b.awarded ? 'badge-awarded' : 'badge-locked'"
            >
              {{ b.label }}
            </span>
          </div>
        </div>
      </div>

      <div class="actions-row">
        <button class="btn-primary" @click="stage = 'choose'">Edit Goals</button>
        <button class="btn-outline" @click="clearAllData">Clear Data</button>
      </div>
    </section>

    <!-- Simple popup when badge unlocked -->
    <transition name="fade">
      <div v-if="popupBadge" class="popup-badge">
        <div class="popup-card">
          <div class="popup-title">🎉 Badge Unlocked!</div>
          <div class="popup-text">{{ popupBadge }}</div>
          <button class="btn-primary btn-sm" @click="popupBadge = ''">Nice!</button>
        </div>
      </div>
    </transition>

    <!-- Toast / small footer -->
    <footer class="footer-note">
      <small>Data saved locally on this device.</small>
    </footer>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const goalCatalog = [
  { id: 'g_journal', title: 'Daily Journal', type: 'daily', desc: 'Write a short journal entry.' },
  { id: 'g_mindful', title: 'Mindful Breathing', type: 'daily', desc: '3–5 minute breathing.' },
  { id: 'g_move', title: 'Move / Exercise', type: 'daily', desc: 'Short movement or walk.' },
  { id: 'g_gratitude', title: 'Gratitude Note', type: 'weekly', desc: 'Post one gratitude note.' },
  { id: 'g_connect', title: 'Connect With Someone', type: 'weekly', desc: 'Reach out and connect.' },
  { id: 'g_sleep', title: 'Sleep Routine', type: 'daily', desc: 'Prioritize sleep hygiene.' }
]

const STORAGE_KEYS = {
  userGoals: 'bb_user_goals_v1',
  entries: 'bb_entries_v1',
  badges: 'bb_badges_v1'
}

const stage = ref('choose')
const selectedGoalIds = ref([])
const userGoals = ref([])
const entries = ref({})
const badgesState = ref({})
const popupBadge = ref('')

function dateKey(d = new Date()) {
  return d.toISOString().slice(0, 10)
}

function weekKey(dateObj = new Date()) {
  const d = new Date(Date.UTC(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate()))
  const dayNum = d.getUTCDay() || 7
  d.setUTCDate(d.getUTCDate() + 4 - dayNum)
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1))
  const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7)
  return `${d.getUTCFullYear()}-W${String(weekNo).padStart(2, '0')}`
}

function loadFromStorage() {
  try { userGoals.value = JSON.parse(localStorage.getItem(STORAGE_KEYS.userGoals) || '[]') } catch { userGoals.value = [] }
  try { entries.value = JSON.parse(localStorage.getItem(STORAGE_KEYS.entries) || '{}') } catch { entries.value = {} }
  try { badgesState.value = JSON.parse(localStorage.getItem(STORAGE_KEYS.badges) || '{}') } catch { badgesState.value = {} }

  if (userGoals.value.length) {
    stage.value = 'board'
    selectedGoalIds.value = userGoals.value.map(g => g.id)
    ensureBadgesInit()
  }
}
loadFromStorage()

function persistAll() {
  localStorage.setItem(STORAGE_KEYS.userGoals, JSON.stringify(userGoals.value))
  localStorage.setItem(STORAGE_KEYS.entries, JSON.stringify(entries.value))
  localStorage.setItem(STORAGE_KEYS.badges, JSON.stringify(badgesState.value))
}

function confirmGoals() {
  userGoals.value = selectedGoalIds.value.map(id => goalCatalog.find(g => g.id === id))
  userGoals.value.forEach(g => {
    if (!entries.value[g.id]) entries.value[g.id] = []
    if (!badgesState.value[g.id]) badgesState.value[g.id] = []
  })
  persistAll()
  stage.value = 'board'
  ensureBadgesInit()
}

function resetAll() {
  selectedGoalIds.value = []
  userGoals.value = []
  entries.value = {}
  badgesState.value = {}
  persistAll()
  stage.value = 'choose'
}

function clearAllData() {
  if (!confirm('Clear all saved badgeboard data?')) return
  localStorage.removeItem(STORAGE_KEYS.userGoals)
  localStorage.removeItem(STORAGE_KEYS.entries)
  localStorage.removeItem(STORAGE_KEYS.badges)
  resetAll()
}

function defaultBadgesForGoal(goal) {
  if (goal.type === 'daily') {
    return [
      { id: 'daily_3', label: '3-Day Streak', threshold: 3 },
      { id: 'daily_7', label: '7-Day Streak', threshold: 7 },
      { id: 'daily_14', label: '14-Day Streak', threshold: 14 }
    ]
  } else {
    return [
      { id: 'week_2', label: '2-Week Streak', threshold: 2 },
      { id: 'week_4', label: '4-Week Streak', threshold: 4 }
    ]
  }
}

function ensureBadgesInit() {
  userGoals.value.forEach(g => {
    if (!badgesState.value[g.id]) badgesState.value[g.id] = []
  })
  persistAll()
}

function badgesForGoal(goalId) {
  const goal = userGoals.value.find(g => g.id === goalId)
  if (!goal) return []
  const awardedSet = new Set(badgesState.value[goalId] || [])
  return defaultBadgesForGoal(goal).map(b => ({ ...b, awarded: awardedSet.has(b.id) }))
}

function hasCompletedToday(goalId) {
  const goal = userGoals.value.find(g => g.id === goalId)
  if (!goal) return false
  const arr = entries.value[goalId] || []
  if (goal.type === 'daily') return arr.includes(dateKey())
  const curWeek = weekKey()
  return arr.some(d => weekKey(new Date(d)) === curWeek)
}

function toggleLogToday(goalId) {
  const goal = userGoals.value.find(g => g.id === goalId)
  if (!goal) return
  const arr = entries.value[goalId] || []
  if (goal.type === 'daily') {
    const today = dateKey()
    const i = arr.indexOf(today)
    if (i === -1) { arr.push(today); checkAndAward(goal) }
    else arr.splice(i, 1)
  } else {
    const today = dateKey()
    const curWeek = weekKey()
    const foundIndex = arr.findIndex(d => weekKey(new Date(d)) === curWeek)
    if (foundIndex === -1) { arr.push(today); checkAndAward(goal) }
    else arr.splice(foundIndex, 1)
  }
  entries.value[goalId] = arr
  persistAll()
}

function computeStreak(goalId) {
  const goal = userGoals.value.find(g => g.id === goalId)
  if (!goal) return 0
  const arr = (entries.value[goalId] || []).slice().sort()
  if (goal.type === 'daily') {
    let count = 0, day = new Date()
    while (arr.includes(dateKey(day))) { count++; day.setDate(day.getDate() - 1) }
    return count
  } else {
    let count = 0, wk = new Date()
    while (arr.some(d => weekKey(new Date(d)) === weekKey(wk))) { count++; wk.setDate(wk.getDate() - 7) }
    return count
  }
}

function streakPercent(goalId) {
  const goal = userGoals.value.find(g => g.id === goalId)
  if (!goal) return 0
  const s = computeStreak(goalId)
  const next = nextBadgeThreshold(goalId)
  return next ? Math.min(100, Math.round((s / next) * 100)) : 100
}

function nextBadgeThreshold(goalId) {
  const goal = userGoals.value.find(g => g.id === goalId)
  if (!goal) return null
  const all = defaultBadgesForGoal(goal).map(b => b.threshold).sort((a,b)=>a-b)
  const awarded = badgesState.value[goalId] || []
  const awardedThresh = defaultBadgesForGoal(goal).filter(b => awarded.includes(b.id)).map(b => b.threshold)
  const current = computeStreak(goalId)
  for (let t of all) if (t > current && !awardedThresh.includes(t)) return t
  return null
}

function checkAndAward(goal) {
  const cur = computeStreak(goal.id)
  const defs = defaultBadgesForGoal(goal)
  const awarded = badgesState.value[goal.id] || []
  defs.forEach(d => {
    if (cur >= d.threshold && !awarded.includes(d.id)) {
      awarded.push(d.id)
      badgesState.value[goal.id] = awarded
      popupBadge.value = `${goal.title} — ${d.label}`
      setTimeout(() => { popupBadge.value = '' }, 3500)
      persistAll()
    }
  })
}

watch([userGoals, entries, badgesState], persistAll, { deep: true })
onMounted(() => { ensureBadgesInit() })

</script>

<style scoped>
.badgeboard-root {
  padding: 14px;
  max-width: 720px;
  margin: 0 auto;
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  color: #152234;
}

.header-card { text-align:center; margin-bottom:12px; }
.title { margin:0; font-size:1.25rem; font-weight:700; }
.subtitle { margin:6px 0 0; font-size:0.95rem; color:#5c6b73; }

.card { background:#fff; border-radius:12px; padding:12px; box-shadow:0 6px 20px rgba(19,30,45,0.03); margin-bottom:14px; }
.section-title { font-size:1.05rem; text-align:center; margin-bottom:10px; }

.goals-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; }
.goal-card { border-radius:10px; padding:10px; border:1px solid #e6e9ec; cursor:pointer; display:flex; flex-direction:column; gap:6px; transition:transform .12s ease, box-shadow .12s ease, background .12s ease; }
.goal-card:hover { transform:translateY(-3px); box-shadow:0 6px 16px rgba(11,20,34,0.04); }
.goal-active { background:#fef0f2; border-color:#ffd8da; box-shadow:0 8px 22px rgba(216,6,33,0.04); }
.goal-name { font-weight:700; font-size:0.98rem; }
.goal-meta { font-size:0.78rem; color:#6c7b83; }
.goal-desc { font-size:0.82rem; color:#475a64; }

.actions-row { display:flex; gap:10px; margin-top:12px; justify-content:center; align-items:center; }
.btn-primary { background:#d80621; color:#fff; border:none; padding:10px 14px; border-radius:10px; font-weight:700; min-width:120px; cursor:pointer; }
.btn-outline { background:transparent; border:1px solid #d6dbe0; color:#1d2b33; padding:9px 12px; border-radius:10px; cursor:pointer; }

.user-goals { display:flex; flex-direction:column; gap:10px; margin-top:10px; }
.user-goal-card { border-radius:10px; padding:10px; border:1px solid #f0f2f3; background:#fff; }
.goal-header { display:flex; justify-content:space-between; align-items:center; }
.goal-title { font-weight:700; }
.goal-type { font-size:0.82rem; color:#5b6a71; }

.btn-log { background:#fff; border:1px solid #d6dbe0; padding:8px 12px; border-radius:8px; font-weight:700; cursor:pointer; }
.btn-log.logged { background:#d80621; color:#fff; border-color:transparent; box-shadow:0 8px 18px rgba(216,6,33,0.12); }

.progress-row { display:flex; gap:12px; align-items:center; margin-top:10px; }
.progress-left { width:90px; text-align:left; }
.progress-label { font-size:0.78rem; color:#6c7b83; }
.progress-value { font-weight:700; font-size:0.95rem; }
.progress-bar-wrap { flex:1; }
.progress-bar-bg { background:#f1f4f6; height:10px; border-radius:999px; overflow:hidden; }
.progress-bar-fill { height:100%; background:linear-gradient(90deg,#ffd93d 0%,#ffb23a 100%); width:0%; transition:width 0.35s ease; }

.badges-row { display:flex; gap:8px; flex-wrap:wrap; margin-top:10px; }
.badge-pill { font-size:0.78rem; padding:6px 8px; border-radius:999px; border:1px solid #e6e9ec; background:#fafafa; color:#26373f; }
.badge-awarded { background:linear-gradient(180deg,#fff7ed,#fff0db); border-color:#ffd7a8; box-shadow:0 6px 14px rgba(255,173,60,0.08); font-weight:700; }
.badge-locked { opacity:0.45; }

.popup-badge { position:fixed; left:16px; right:16px; bottom:20px; display:flex; justify-content:center; z-index:1200; }
.popup-card { background:#fff; padding:12px 14px; border-radius:12px; box-shadow:0 12px 40px rgba(11,20,34,0.12); text-align:center; }
.popup-title { font-weight:700; font-size:1rem; }
.popup-text { margin-top:6px; font-size:0.85rem; }

.footer-note { text-align:center; font-size:0.75rem; color:#7b8a93; margin-top:10px; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.35s; }
.fade-enter-from, .fade-leave-to { opacity:0; }
</style>
