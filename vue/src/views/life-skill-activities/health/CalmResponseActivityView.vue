<template>
  <div class="calm-root">
    <!-- Header -->
    <header class="hdr">
      <h1>Calm Response — On-the-Scene Reset</h1>
      <p class="sub">Quick, discreet tools to steady your breath and regain control.</p>
    </header>

    <!-- STEP: Intro -->
    <section v-if="stage === 'intro'" class="card">
      <h2 class="card-title">On-the-Scene Reset</h2>
      <p class="card-sub">Use this 1-minute, low-profile breathing reset to steady your body and mind.</p>

      <div class="controls-row mb-3">
        <label class="inline-toggle">
          <input type="checkbox" v-model="discreetMode" />
          <span>Discreet (vibrate only)</span>
        </label>
      </div>

      <div class="actions">
        <button class="btn primary mb-3 mt-3" @click="startPrepare">Start Reset</button>
        <button class="btn outline" @click="openGrounding">Grounding (5-4-3-2-1)</button>
      </div>
    </section>

    <!-- STEP: Pre-countdown -->
    <section v-if="stage === 'countdown'" class="card center-card">
      <div class="countdown-big">{{ preCountdown }}</div>
      <div class="muted">Preparing — get comfortable. Starts in {{ preCountdown }}...</div>
      <div class="actions" style="margin-top:12px;">
        <button class="btn outline" @click="cancelPrepare">Cancel</button>
      </div>
    </section>

    <!-- STEP: Breathing session -->
    <section v-if="stage === 'breathing'" class="card breathing-card">
      <h2 class="card-title">Box Breathing — Stay steady</h2>
      <p class="card-sub">Follow the bubble. Inhale • Hold • Exhale • Hold</p>

      <div class="visual-row">
        <div class="bubble-wrap">
          <div
            class="bubble"
            :class="phaseClass"
            :style="bubbleStyle"
            aria-live="polite"
            role="img"
            :aria-label="phaseLabel + ', ' + phaseCountdown + ' seconds left'"
          >
            <!-- optional inner lung shape for feel -->
            <svg v-if="showLung" class="lung-svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid meet">
              <g :fill="bubbleColorInner">
                <path d="M50 10 C30 12 20 30 20 50 C20 70 30 85 50 90 C70 85 80 70 80 50 C80 30 70 12 50 10 Z" />
              </g>
            </svg>
          </div>
        </div>

        <div class="phase-info">
          <div class="phase-text">{{ phaseLabel }}</div>
          <div class="phase-count">{{ phaseCountdown }}</div>
          <div class="cycles" v-if="cyclesTotal">
            Cycle {{ currentCycle }} / {{ cyclesTotal }}
          </div>
        </div>
      </div>

      <div class="controls-row">
        <button class="btn" @click="togglePause">{{ isPaused ? 'Resume' : 'Pause' }}</button>
        <button class="btn outline" @click="stopSession">Stop</button>
      </div>

      <div class="tiny-note">Discreet mode: <strong>{{ discreetMode ? 'ON' : 'OFF' }}</strong></div>
    </section>

    <!-- STEP: Grounding micro-exercise -->
    <section v-if="stage === 'grounding'" class="card">
      <h2 class="card-title">5-4-3-2-1 Grounding</h2>
      <p class="card-sub">Tap to move through senses. Silent and field-friendly.</p>

      <div class="ground-step">
        <div class="ground-number">{{ groundIndex + 1 }} / 5</div>
        <div class="ground-prompt">{{ groundPrompts[groundIndex] }}</div>
        <div class="ground-helper small muted">{{ groundHelpers[groundIndex] }}</div>
      </div>

      <div class="actions">
        <button class="btn primary mx-2" @click="advanceGround">Next</button>
        <button class="btn outline" @click="closeGrounding">Close</button>
      </div>
    </section>

    <!-- STEP: Affirmation / Log -->
    <section v-if="stage === 'done'" class="card">
      <h2 class="card-title">Reset Complete</h2>
      <p class="card-sub">You did a tactical reset. How do you want to proceed?</p>

      <label class="inline-field">
        <span class="label">Short note (private)</span>
        <input type="text" v-model="logNote" placeholder="Optional — save a short note" />
      </label>

      <div class="actions">
        <button class="btn primary" @click="confirmReady">I'm ready (return)</button>
        <button class="btn outline" @click="logIncidentLater">Log Incident Later</button>
      </div>

      <div class="muted small">Saved logs are stored locally on this device.</div>
    </section>

    <!-- Toast small -->
    <div v-if="toast" class="toast">{{ toast }}</div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onBeforeUnmount } from 'vue'

/* ---------- State ---------- */
const stage = ref('intro') // intro | countdown | breathing | grounding | done
const discreetMode = ref(true) // vibrate-only default

/* Pre-countdown */
const preCountdown = ref(3)
let preInterval = null

/* Breathing phases (Box breathing by default) */
const phasesTemplate = [
  { phase: 'inhale', seconds: 4, label: 'Inhale' },
  { phase: 'hold', seconds: 4, label: 'Hold' },
  { phase: 'exhale', seconds: 4, label: 'Exhale' },
  { phase: 'hold', seconds: 4, label: 'Hold' }
]

let phases = [] // runtime copy
let phaseTimeout = null
let tickInterval = null

const phaseIndex = ref(0)
const phaseCountdown = ref(0)
const isPaused = ref(false)
const currentCycle = ref(0)
const cyclesTotal = ref(4) // 4 cycles => ~1 minute for 4-4-4-4

/* Bubble / visual */
const bubbleScale = ref(1)
const bubbleColor = ref('#4e8cff') // outer
const bubbleColorInner = ref('#ffffff') // inner svg fill
const showLung = ref(true) // draw inner lung shape

/* logging */
const logNote = ref('')
const toast = ref('')

/* grounding */
const stageBackup = ref(null)
const groundIndex = ref(0)
const groundPrompts = [
  'Name 5 things you can see (silently)',
  'Name 4 things you can touch (silently)',
  'Name 3 things you can hear (silently)',
  'Name 2 things you can smell (silently)',
  'Name 1 thing you can taste or notice your breath'
]
const groundHelpers = [
  'Look around and notice details.',
  'Notice texture — your gear, your clothes.',
  'Listen carefully for nearby sounds.',
  'Take a quiet sniff; notice a scent.',
  'Gently notice your breath or saliva.'
]

/* Toast helper */
function showToast(msg, ms = 1800) {
  toast.value = msg
  setTimeout(() => { toast.value = '' }, ms)
}

/* Utility: vibrate if available & allowed */
function vibrate(pattern = 50) {
  try {
    if (discreetMode.value && navigator && navigator.vibrate) {
      // short, subtle
      navigator.vibrate(typeof pattern === 'number' ? [pattern] : pattern)
    } else if (!discreetMode.value && navigator && navigator.vibrate) {
      // non-discreet: stronger pattern
      navigator.vibrate([50, 30, 50])
    }
  } catch (e) {
    // ignore
  }
}

/* ---------- Pre-countdown flow ---------- */
function startPrepare() {
  stage.value = 'countdown'
  preCountdown.value = 3
  vibrate(30)
  preInterval = setInterval(() => {
    preCountdown.value--
    if (preCountdown.value <= 0) {
      clearInterval(preInterval)
      preInterval = null
      startBreathingSession()
    }
  }, 1000)
}

function cancelPrepare() {
  if (preInterval) {
    clearInterval(preInterval)
    preInterval = null
  }
  stage.value = 'intro'
}

/* ---------- Breathing core ---------- */
function startBreathingSession() {
  // Reset
  phaseIndex.value = 0
  currentCycle.value = 0
  isPaused.value = false
  phases = JSON.parse(JSON.stringify(phasesTemplate)) // clone
  stage.value = 'breathing'
  nextPhase() // begins cycle
}

function nextPhase() {
  // clear any previous timers
  clearPhaseTimers()

  const p = phases[phaseIndex.value]
  if (!p) {
    // safety fallback - restart
    phaseIndex.value = 0
    return nextPhase()
  }

  // set countdown to phase seconds
  phaseCountdown.value = p.seconds
  updateVisualForPhase(p.phase)

  // vibrate subtly at phase start (discreet)
  vibrate(35)

  // tick down every second for UI countdown
  tickInterval = setInterval(() => {
    if (isPaused.value) return // pause ticks visually
    phaseCountdown.value = Math.max(0, phaseCountdown.value - 1)
  }, 1000)

  // after phase.seconds, move to next
  phaseTimeout = setTimeout(() => {
    // finish of phase
    // if we moved past last phase, we completed a cycle
    phaseIndex.value = (phaseIndex.value + 1) % phases.length
    if (phaseIndex.value === 0) {
      currentCycle.value++
      // if we've reached cyclesTotal, end session
      if (currentCycle.value >= cyclesTotal.value) {
        finishBreathingSession()
        return
      }
    }
    // go next
    nextPhase()
  }, p.seconds * 1000)
}

function updateVisualForPhase(phase) {
  // Set bubble scale and color instantly
  if (phase === 'inhale') {
    // expand
    animateBubbleTo(1.28)
    bubbleColor.value = '#7bd389' // calm green
    bubbleColorInner.value = '#ffffff'
  } else if (phase === 'exhale') {
    animateBubbleTo(0.72) // deflate
    bubbleColor.value = '#ffd93d' // warm exhale color
    bubbleColorInner.value = '#ffffff'
  } else if (phase === 'hold') {
    animateBubbleTo(1.02)
    bubbleColor.value = '#4e8cff' // hold color (blue)
    bubbleColorInner.value = '#ffffff'
  } else {
    animateBubbleTo(1)
    bubbleColor.value = '#4e8cff'
    bubbleColorInner.value = '#ffffff'
  }
}

/* Smooth animated change of bubbleScale */
let animFrame = null
function animateBubbleTo(targetScale) {
  cancelAnimationFrame(animFrame)
  const start = bubbleScale.value
  const duration = 350 // ms easing for smoother feel
  const startTime = performance.now()
  function step(ts) {
    const t = Math.min(1, (ts - startTime) / duration)
    // easeOutQuad
    const ease = 1 - (1 - t) * (1 - t)
    bubbleScale.value = start + (targetScale - start) * ease
    if (t < 1) animFrame = requestAnimationFrame(step)
  }
  animFrame = requestAnimationFrame(step)
}

function clearPhaseTimers() {
  if (phaseTimeout) { clearTimeout(phaseTimeout); phaseTimeout = null }
  if (tickInterval) { clearInterval(tickInterval); tickInterval = null }
}

/* Pause / resume */
function togglePause() {
  isPaused.value = !isPaused.value
  if (isPaused.value) {
    // pause timers
    clearPhaseTimers()
    showToast('Paused')
  } else {
    // resume: start nextPhase with remaining countdown
    // To keep logic simple, restart current phase with remaining time:
    // create a temporary phase object matching remaining seconds
    const p = phases[phaseIndex.value]
    if (!p) return
    // create a small wrapper to continue at current phase countdown
    // We'll use current phaseCountdown as remaining seconds
    clearPhaseTimers()
    // set visual state again
    updateVisualForPhase(p.phase)
    // vibrate once at resume
    vibrate(30)
    // tick down UI
    tickInterval = setInterval(() => {
      if (isPaused.value) return
      phaseCountdown.value = Math.max(0, phaseCountdown.value - 1)
    }, 1000)
    // set timeout for remaining seconds
    phaseTimeout = setTimeout(() => {
      // next phase
      phaseIndex.value = (phaseIndex.value + 1) % phases.length
      if (phaseIndex.value === 0) {
        currentCycle.value++
        if (currentCycle.value >= cyclesTotal.value) {
          finishBreathingSession()
          return
        }
      }
      nextPhase()
    }, phaseCountdown.value * 1000)
  }
}

/* Stop / finish */
function stopSession() {
  clearPhaseTimers()
  isPaused.value = false
  stage.value = 'intro'
  showToast('Stopped')
}

function finishBreathingSession() {
  clearPhaseTimers()
  animFrame && cancelAnimationFrame(animFrame)
  phaseIndex.value = 0
  phaseCountdown.value = 0
  bubbleScale.value = 1
  // small vibrate on completion
  vibrate([30, 20, 30])
  stage.value = 'done'
}

/* ---------- Grounding ---------- */
function openGrounding() {
  stageBackup.value = stage.value
  stage.value = 'grounding'
  groundIndex.value = 0
}

function advanceGround() {
  groundIndex.value++
  vibrate(20)
  if (groundIndex.value >= groundPrompts.length) {
    // finish
    stage.value = stageBackup.value || 'intro'
    groundIndex.value = 0
    showToast('Grounding complete')
  }
}
function closeGrounding() {
  stage.value = stageBackup.value || 'intro'
  groundIndex.value = 0
}

/* ---------- Logging & Affirmation ---------- */
function confirmReady() {
  // just go back to intro
  stage.value = 'intro'
  showToast('Good — you’re ready')
}

function logIncidentLater() {
  // store in localStorage under 'calm_resets'
  const logs = JSON.parse(localStorage.getItem('calm_resets') || '[]')
  logs.push({
    date: new Date().toISOString(),
    cyclesCompleted: currentCycle.value,
    note: logNote.value || '',
    discreetMode: discreetMode.value
  })
  localStorage.setItem('calm_resets', JSON.stringify(logs))
  logNote.value = ''
  stage.value = 'intro'
  showToast('Logged locally')
}

/* UI computed values */
const phaseLabel = computed(() => {
  const p = phases[phaseIndex.value]
  return p ? `${p.label}` : ''
})
const phaseClass = computed(() => {
  const p = phases[phaseIndex.value]
  return p ? p.phase : ''
})
const phaseIndexSafe = computed(() => Math.max(0, phaseIndex.value))

/* bubble style binding */
const bubbleStyle = computed(() => ({
  transform: `scale(${bubbleScale.value})`,
  background: bubbleColor.value,
  boxShadow: `0 8px 30px ${hexToRgba(bubbleColor.value, 0.18)}`
}))

/* helper: rgba of hex */
function hexToRgba(hex, alpha = 1) {
  const h = hex.replace('#','')
  const bigint = parseInt(h,16)
  const r = (bigint >> 16) & 255
  const g = (bigint >> 8) & 255
  const b = bigint & 255
  return `rgba(${r},${g},${b},${alpha})`
}

/* Cleanup */
onBeforeUnmount(() => {
  clearInterval(preInterval)
  clearPhaseTimers()
  animFrame && cancelAnimationFrame(animFrame)
})

</script>

<style scoped>
/* Mobile-first, centered, bright UI */
.calm-root {
  padding: 14px;
  max-width: 560px;
  margin: 0 auto;
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  color: #123;
}

/* header */
.hdr { text-align:center; margin-bottom:12px; }
.hdr h1 { margin:0; font-size:1.15rem; font-weight:700; }
.hdr .sub { margin-top:6px; color:#50626f; font-size:0.92rem; }

/* shared card */
.card {
  background:#fff;
  border-radius:12px;
  padding:14px;
  box-shadow: 0 6px 20px rgba(19,30,45,0.04);
  margin-bottom:14px;
  text-align:center;
}
.center-card { text-align:center; }

/* title/sub inside card */
.card-title { font-size:1.05rem; font-weight:700; margin-bottom:6px; }
.card-sub { color:#556; font-size:0.92rem; margin-bottom:12px; }

/* countdown big */
.countdown-big {
  font-size:72px;
  font-weight:800;
  color:#4e8cff;
  line-height:1;
  margin:10px 0;
}

/* visual row layout */
.visual-row {
  display:flex;
  gap:14px;
  align-items:center;
  justify-content:center;
  flex-wrap:wrap;
}

/* bubble wrap */
.bubble-wrap {
  width:220px;
  height:220px;
  display:flex;
  align-items:center;
  justify-content:center;
}
.bubble {
  width: 92%;
  height: 92%;
  border-radius: 50%;
  transition: transform 0.35s ease, background 0.35s ease, box-shadow 0.35s ease;
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
  overflow:hidden;
}

/* optional inner lung svg for organic feel */
.lung-svg { width:76%; height:76%; opacity:0.95; }

/* phase info */
.phase-info { min-width:120px; text-align:center; }
.phase-text { font-weight:700; font-size:1.05rem; margin-top:8px; }
.phase-count { font-size:1.6rem; font-weight:800; color:#123; margin-top:6px; }
.cycles { color:#596; margin-top:6px; font-weight:600; }

/* controls */
.controls-row { display:flex; gap:10px; justify-content:center; margin-top:12px; flex-wrap:wrap; }
.btn {
  background:#fff;
  border:1px solid #d6dbe0;
  padding:10px 14px;
  border-radius:10px;
  min-width:100px;
  font-weight:700;
  cursor:pointer;
}
.btn.primary { background:#d80621; color:#fff; border-color:transparent; box-shadow:0 6px 18px rgba(216,6,33,0.12); }
.btn.outline { background:transparent; border:1px solid #d6dbe0; color:#1d2b33; }

/* small inline toggle */
.inline-toggle { display:inline-flex; gap:8px; align-items:center; cursor:pointer; }
.inline-toggle input { margin-right:6px; }

/* tiny note */
.tiny-note { color:#6b7a88; margin-top:8px; font-size:0.9rem; }

/* grounding */
.ground-step { padding:8px 6px; margin:8px 0; }
.ground-number { font-size:1.2rem; font-weight:800; }
.ground-prompt { font-size:1rem; margin-top:6px; }
.ground-helper { color:#556; margin-top:6px; font-size:0.88rem; }

/* toast */
.toast {
  position: fixed;
  left: 50%;
  transform: translateX(-50%);
  bottom: 18px;
  background: rgba(0,0,0,0.8);
  color: #fff;
  padding: 8px 12px;
  border-radius: 999px;
  font-weight:700;
  z-index:1200;
}

/* phase classes (for reference if needed) */


/* Responsive smaller */
@media (max-width:420px) {
  .bubble-wrap { width:170px; height:170px; }
  .phase-count { font-size:1.3rem; }
  .countdown-big { font-size:56px; }
}
</style>
