<template>
  <div class="container py-5">
    <h1 class="text-center mb-3">Self-Discipline — Discipline Tracker</h1>
    <p class="text-muted text-center mb-4">Pick one small daily habit and build it into a routine.</p>

    <!-- Intro / Select Habit -->
    <div class="card mb-4 p-3">
      <h5 class="mb-2">Choose or Create a Habit</h5>

      <div class="mb-3">
        <div class="list-group">
          <button
            v-for="h in presets"
            :key="h.id"
            class="list-group-item list-group-item-action"
            :class="{ active: activeHabit?.id === h.id && !activeHabit.custom }"
            @click="selectPreset(h)"
          >
            <div class="d-flex justify-content-between">
              <div>
                <strong>{{ h.title }}</strong>
                <div class="small text-muted">{{ h.time }} • {{ h.duration }}</div>
              </div>
              <div class="small text-muted">{{ h.estimate }}</div>
            </div>
          </button>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Or create your own habit</label>
        <div class="input-group">
          <input v-model="custom.title" class="form-control" placeholder="Describe your habit (5–15 min)" />
          <select v-model="custom.duration" class="form-select w-auto">
            <option value="5 min">5 min</option>
            <option value="10 min">10 min</option>
            <option value="15 min">15 min</option>
          </select>
          <button class="btn btn-outline-secondary" @click="createCustomHabit">Create</button>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-secondary" @click="clearActive">Clear</button>
        <button class="btn btn-accent" :disabled="!activeHabit" @click="openScheduler">Set Reminder & Start</button>
      </div>
    </div>

    <!-- Scheduler & Reminder -->
    <div v-if="showScheduler" class="card mb-4 p-3">
      <h5>Reminder Scheduler</h5>
      <div class="mb-3">
        <label class="form-label">Reminder time</label>
        <input type="time" v-model="reminderTime" class="form-control w-auto" />
      </div>

      <div class="mb-3">
        <label class="form-label">Reminder tone</label>
        <select v-model="reminderTone" class="form-select w-auto">
          <option value="friendly">Friendly</option>
          <option value="direct">Direct</option>
          <option value="humorous">Humorous</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-secondary" @click="showScheduler = false">Cancel</button>
        <button class="btn btn-accent" @click="saveSchedule">Save & Enable</button>
      </div>

      <small class="text-muted d-block mt-2">
        Note: Browser push notifications need permission. We'll ask once when you enable reminders.
      </small>
    </div>

    <!-- Today Log -->
    <div class="card mb-4 p-3">
      <h5>Today's Habit</h5>
      <div v-if="!activeHabit" class="text-muted">No habit selected yet.</div>

      <div v-else>
        <div class="mb-2">
          <strong>{{ activeHabit.title }}</strong>
          <div class="small text-muted">{{ activeHabit.duration }}</div>
        </div>

        <div class="mb-3 d-flex gap-2">
          <button class="btn" :class="todayStatus === 'done' ? 'btn-success' : 'btn-outline-success'" @click="markToday('done')">✅ I did it</button>
          <button class="btn" :class="todayStatus === 'missed' ? 'btn-danger' : 'btn-outline-danger'" @click="markToday('missed')">❌ I missed it</button>
        </div>

        <div v-if="todayStatus" class="mb-3">
          <label class="form-label">Reflection (optional)</label>
          <textarea v-model="todayNote" class="form-control mb-2" rows="2" placeholder="What helped / what blocked you?"></textarea>

          <div class="d-flex align-items-center gap-2">
            <label class="me-2 mb-0">Mood:</label>
            <button v-for="m in moods" :key="m" class="btn btn-sm" :class="mood === m ? 'btn-accent' : 'btn-outline-secondary'" @click="mood = m">{{ m }}</button>
          </div>

          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-secondary" @click="undoToday">Undo</button>
            <button class="btn btn-accent" @click="saveToday">Save Log</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Progress & Summary -->
    <div class="card mb-4 p-3">
      <h5>Progress & Streaks</h5>
      <div class="mb-2">
        <div class="d-flex justify-content-between">
          <div><strong>Current streak</strong></div>
          <div><strong>{{ streak }} days</strong></div>
        </div>
      </div>

      <div class="mb-2">
        <div class="small text-muted mb-1">Last 7 days</div>
        <div class="d-flex gap-1">
          <div v-for="d in lastSeven" :key="d.day" class="text-center" style="min-width:48px">
            <div :class="['p-2 rounded', d.status === 'done' ? 'bg-success text-white' : (d.status === 'missed' ? 'bg-danger text-white' : 'bg-light text-muted')]">
              {{ d.label }}
            </div>
          </div>
        </div>
      </div>

      <div class="mt-3">
        <button class="btn btn-outline-primary me-2" @click="openWeeklySummary">Weekly Summary</button>
        <button class="btn btn-outline-secondary" @click="exportLogs">Export (JSON)</button>
      </div>
    </div>

    <!-- Weekly Summary Modal-like -->
    <div v-if="showWeekly" class="card mb-4 p-3">
      <h5>Weekly Summary</h5>
      <p class="mb-2">This week: <strong>{{ weeklyCompleted }}</strong> / 7 days completed</p>
      <p class="mb-2">Longest streak (stored): <strong>{{ longestStreak }}</strong></p>
      <div class="d-flex gap-2">
        <button class="btn btn-secondary" @click="showWeekly = false">Close</button>
        <button class="btn btn-outline-danger" @click="clearAllLogs">Clear Logs</button>
      </div>
    </div>

    <!-- Badges -->
    <div class="card p-3">
      <h5>Badges</h5>
      <div class="d-flex gap-2 flex-wrap">
        <div class="badge bg-light text-dark p-2" v-for="b in badges" :key="b">{{ b }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Preset habits
const presets = [
  { id: 'h1', title: '10 min breathing / meditation', duration: '10 min', time: 'Morning', estimate: '5–15 min', preset: true },
  { id: 'h2', title: 'Check & clean gear before shift', duration: '10 min', time: 'Before shift', estimate: '5–15 min', preset: true },
  { id: 'h3', title: 'Keep phone away during meals', duration: '5 min', time: 'Any', estimate: '5 min', preset: true },
  { id: 'h4', title: 'Drink water before every meal', duration: '2 min', time: 'Meal times', estimate: '2 min', preset: true },
  { id: 'h5', title: 'Stretch 5 minutes after waking', duration: '5 min', time: 'Morning', estimate: '5 min', preset: true },
]

// local state
const activeHabit = ref(null)
const custom = ref({ title: '', duration: '5 min' })
const showScheduler = ref(false)
const reminderTime = ref('07:00')
const reminderTone = ref('friendly')
const reminderEnabled = ref(false)

const todayStatus = ref(null)
const todayNote = ref('')
const mood = ref('')
const moods = ['😊', '😐', '😔']

const HABIT_KEY = 'discipline-habit-v1'
const LOG_KEY = 'discipline-logs-v1'
const SCHEDULE_KEY = 'discipline-schedule-v1'
const BADGE_KEY = 'discipline-badges-v1'

const load = () => {
  try {
    const h = JSON.parse(localStorage.getItem(HABIT_KEY))
    if (h) activeHabit.value = h
  } catch (e) {}
}
load()

const selectPreset = (h) => {
  activeHabit.value = { ...h, custom: false }
}

const createCustomHabit = () => {
  if (!custom.value.title.trim()) {
    alert('Please enter your habit description before creating it.')
    return
  }
  activeHabit.value = { id: 'c' + Date.now(), title: custom.value.title.trim(), duration: custom.value.duration, custom: true }
  custom.value.title = ''
  custom.value.duration = '5 min'
  alert('Custom habit added! Let’s make it part of your routine 💪')
}

const clearActive = () => {
  activeHabit.value = null
  localStorage.removeItem(HABIT_KEY)
  alert('Habit selection cleared.')
}

const openScheduler = () => {
  if (!activeHabit.value) {
    alert('Please select or create a habit first.')
    return
  }
  showScheduler.value = true
}

const saveSchedule = async () => {
  localStorage.setItem(HABIT_KEY, JSON.stringify(activeHabit.value))
  reminderEnabled.value = true
  localStorage.setItem(SCHEDULE_KEY, JSON.stringify({ time: reminderTime.value, tone: reminderTone.value }))
  showScheduler.value = false
  if (window.Notification && Notification.permission !== 'granted') {
    try {
      await Notification.requestPermission()
    } catch (e) {}
  }
  alert('Reminder set successfully! Stay consistent and make it a daily win ✨')
}

// logging helpers
const pushLog = (entry) => {
  const all = JSON.parse(localStorage.getItem(LOG_KEY) || '[]')
  all.unshift(entry)
  localStorage.setItem(LOG_KEY, JSON.stringify(all.slice(0, 1000)))
}

const markToday = (status) => {
  todayStatus.value = status
  todayNote.value = ''
  mood.value = ''
}

const undoToday = () => {
  todayStatus.value = null
  todayNote.value = ''
  mood.value = ''
}

const saveToday = () => {
  if (!todayStatus.value) {
    alert('Please mark today as Done ✅ or Missed ❌ first.')
    return
  }
  const entry = {
    dateISO: new Date().toISOString().slice(0,10),
    dateFull: new Date().toLocaleString(),
    habit: activeHabit.value ? activeHabit.value.title : 'No habit',
    status: todayStatus.value,
    note: todayNote.value || '',
    mood: mood.value || ''
  }
  pushLog(entry)
  updateLongest()
  todayStatus.value = null
  todayNote.value = ''
  mood.value = ''
  alert('Your effort has been saved! Keep up the discipline 🌟')
}

const getLogs = () => JSON.parse(localStorage.getItem(LOG_KEY) || '[]')

const lastSeven = computed(() => {
  const today = new Date()
  const arr = []
  const logs = getLogs()
  for (let i = 6; i >= 0; i--) {
    const d = new Date()
    d.setDate(today.getDate() - i)
    const iso = d.toISOString().slice(0,10)
    const dayLog = logs.find(l => l.dateISO === iso)
    arr.push({
      day: iso,
      label: d.toLocaleDateString(undefined, { weekday: 'short' }).slice(0,3),
      status: dayLog ? dayLog.status : null
    })
  }
  return arr
})

const weeklyCompleted = computed(() => lastSeven.value.filter(d => d.status === 'done').length)

const streak = computed(() => {
  const logs = getLogs().map(l => l.dateISO)
  const uniq = Array.from(new Set(logs)).sort().reverse()
  let count = 0
  for (let i = 0; i < 365; i++) {
    const iso = new Date(Date.now() - i*24*60*60*1000).toISOString().slice(0,10)
    if (uniq.includes(iso)) count++
    else break
  }
  return count
})

const longestStreak = ref(Number(localStorage.getItem('discipline-longest-streak') || 0))

const updateLongest = () => {
  const s = streak.value
  if (s > longestStreak.value) {
    longestStreak.value = s
    localStorage.setItem('discipline-longest-streak', s)
  }
}

const badges = computed(() => {
  const b = []
  if (streak.value >= 3) b.push('🔥 3-Day Spark')
  if (streak.value >= 7) b.push('🏅 7-Day Streak Champ')
  if (streak.value >= 14) b.push('💪 Consistency Builder')
  if (streak.value >= 30) b.push('🌟 30 Days Strong')
  return b
})

const showWeekly = ref(false)
const openWeeklySummary = () => { showWeekly.value = true }

const exportLogs = () => {
  const data = getLogs()
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'discipline-logs.json'
  a.click()
  URL.revokeObjectURL(url)
  alert('Logs exported successfully!')
}

const clearAllLogs = () => {
  if (!confirm('Clear all discipline logs?')) return
  localStorage.removeItem(LOG_KEY)
  localStorage.removeItem('discipline-longest-streak')
  longestStreak.value = 0
  showWeekly.value = false
  alert('All progress logs cleared. You can start fresh anytime 💫')
}

watch(activeHabit, (v) => {
  if (v) localStorage.setItem(HABIT_KEY, JSON.stringify(v))
}, { deep: true })
</script>

<style scoped>
.btn-accent {
  background-color: #D80621;
  color: #fff;
  border: none;
}
.btn-accent:hover {
  background-color: #b1051b;
}
.card {
  border-radius: 0.6rem;
}
.list-group-item.active {
  background-color: #D80621;
  color: #fff;
  border-color: #D80621;
}
.list-group-item.active small {
  color: #fff !important;
}
</style>

