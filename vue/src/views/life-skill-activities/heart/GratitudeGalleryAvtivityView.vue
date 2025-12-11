<template>
    <div class="container py-4 gratitude-app">

        <!-- Header -->
        <h1 class="text-center fw-bold mb-3">Gratitude Gallery</h1>
        <p class="text-center text-muted mb-4">“Even in a tough week, there’s something good.” Submit a short gratitude — it will appear anonymously in the gallery.</p>

        <!-- --- 1. Welcome / Orientation --- -->
        <div v-if="step === 1" class="card p-3 mb-3 rounded-3 shadow-sm text-center">
            <div class="mb-3">
                <i class="bi bi-heart-fill fs-1 text-warning"></i>
            </div>
            <p class="lead">This takes under a minute. Share one short thing you're thankful for — a sentence, a moment, or a kind gesture.</p>
            <button class="btn btn-danger w-100 mt-2" @click="start">Start</button>
        </div>

        <!-- --- 2. Add Gratitude --- -->
        <div v-if="step === 2" class="card p-3 mb-3 rounded-3 shadow-sm">
            <h5 class="text-center mb-2">I’m thankful for…</h5>

            <textarea
                v-model="form.text"
                maxlength="160"
                rows="3"
                class="form-control mb-2"
                placeholder="Finish the sentence: I’m thankful for… (max 160 characters)"
                @input="onTextInput"
            ></textarea>
            <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                <div>{{ remainingChars }} characters left</div>
                <div>{{ form.text.trim().length ? 'Ready' : 'Required' }}</div>
            </div>

            <!-- Optional photo (no faces) -->
            <label class="form-label fw-semibold small">Optional photo (no faces)</label>
            <input type="file" accept="image/*" @change="onPhotoChange" class="form-control mb-2" />

            <div v-if="photoPreview" class="text-center mb-2">
                <img :src="photoPreview" class="img-preview rounded" alt="preview" />
                <div class="small text-muted mt-1">Photo preview (will be stored locally)</div>
            </div>

            <!-- Suggestions -->
            <div class="mb-2">
                <small class="text-muted">Examples:</small>
                <div class="d-flex gap-2 flex-wrap mt-1">
                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="applySuggestion('My partner covering my shift')">My partner covering my shift</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="applySuggestion('A quiet moment with a hot cup of tea')">Hot cup of tea</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="applySuggestion('A colleague who had my back')">A colleague who had my back</button>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-outline-secondary w-50" @click="step = 1">Back</button>
                <button class="btn btn-danger w-50" :disabled="!canSubmit" @click="submitGratitude">Add to Gallery</button>
            </div>
        </div>

        <!-- --- 3. Submit Animation --- -->
        <div v-if="step === 3" class="card p-4 mb-3 rounded-3 shadow-sm text-center">
            <div class="animation-box mb-3">
                <div class="floating-graphic">✨</div>
            </div>
            <h5 class="mb-2">Thank you — your gratitude has been added</h5>
            <p class="text-muted small">It will appear anonymously in the gallery for others to read and react.</p>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-outline-secondary w-50" @click="goToGallery">View Gallery</button>
                <button class="btn btn-danger w-50" @click="start">Add another</button>
            </div>
        </div>

        <!-- --- 4. Gallery Feed --- -->
        <div v-if="step === 4" class="gallery-section mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Gratitude Wall</h5>
                <div>
                    <button class="btn btn-outline-secondary btn-sm me-2" @click="refreshFromStorage">Refresh</button>
                    <button class="btn btn-danger btn-sm" @click="step = 2">Add New</button>
                </div>
            </div>

            <div v-if="entries.length === 0" class="card p-3 text-center mb-3">
                <p class="mb-0 text-muted">No entries yet. Be the first to add one.</p>
            </div>

            <div v-for="(entry, idx) in entries" :key="entry.id" class="card p-3 mb-3 rounded-3 shadow-sm entry-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted">Anonymous • {{ formatDate(entry.ts) }}</div>
                        <div class="fw-semibold mt-1" v-text="entry.text"></div>
                    </div>
                    <div v-if="entry.photo" class="ms-2">
                        <img :src="entry.photo" class="img-thumb rounded" alt="photo" />
                    </div>
                </div>

                <!-- Reactions -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2 reactions-row">
                    <div class="d-flex gap-1 align-items-center reaction-item" v-for="(r, key) in reactionTypes" :key="key">
                        <button class="btn reaction-btn btn-sm" :class="{'reacted': userReacted(entry.id, key)}" @click="toggleReaction(entry.id, key)">
                            <span v-html="r.emoji"></span>
                        </button>
                        <div class="small text-muted">{{ entry.reactions[key] || 0 }}</div>
                    </div>

                    <div class="ms-auto d-flex gap-2 align-items-center">
                        <button class="btn btn-outline-secondary btn-sm" @click="toggleCommentBox(idx)">
                            💬 Comment
                        </button>
                    </div>
                </div>

                <!-- Comments -->
                <div v-if="entry.showComment" class="comment-box mt-2">
                    <div v-for="(c, ci) in entry.comments" :key="ci" class="small mb-1 comment-row">
                        <span class="fw-semibold">Someone:</span> {{ c }}
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <input type="text" class="form-control form-control-sm" v-model="entry._newComment" placeholder="Say something kind (optional)" @keyup.enter="addComment(entry)" />
                        <button class="btn btn-primary btn-sm" @click="addComment(entry)" :disabled="!entry._newComment">Send</button>
                    </div>
                </div>
            </div>

            <!-- Load more / clear -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <button class="btn btn-outline-secondary btn-sm" @click="loadMore" :disabled="allLoaded">Load more</button>
                </div>
                <div>
                    <button class="btn btn-link text-danger small" @click="clearGallery" v-if="entries.length">Clear local gallery</button>
                </div>
            </div>
        </div>

        <!-- --- 5. Mood Reflection + Insight (bottom section) --- -->
        <div v-if="step === 4" class="card p-3 mb-5 rounded-3 shadow-sm">
            <h5 class="text-center mb-2">Mood Reflection</h5>
            <p class="text-center small text-muted mb-2">How did adding gratitude make you feel?</p>

            <div class="d-flex justify-content-center gap-2 mb-2 mood-row-wrap">
                <button v-for="m in moods" :key="m.label" type="button"
                    class="btn btn-outline-secondary mood-btn"
                    :class="selectedMood === m.label ? 'btn-danger' : ''"
                    @click="selectedMood = m.label">
                    <span class="me-1">{{ m.emoji }}</span> {{ m.label }}
                </button>
            </div>

            <textarea v-model="moodNote" rows="2" maxlength="120" class="form-control mb-2" placeholder="One thing I’ll carry forward this week... (optional)"></textarea>

            <div class="d-flex gap-2 mt-2">
                <button class="btn btn-outline-secondary w-50" @click="downloadGallery">Export (JSON)</button>
                <button class="btn btn-success w-50" :disabled="!selectedMood && !moodNote" @click="saveReflection">Save Reflection</button>
            </div>

            <div v-if="reflectionSaved" class="mt-3 text-center small text-success">Reflection saved. Thank you — keep it up!</div>
            <div class="mt-3 small text-muted text-center">Insight: Practicing weekly gratitude can reduce feelings of burnout and increase wellbeing.</div>
        </div>

    </div>
</template>

<script setup>
/*
  GratitudeGallerySingle.vue
  - Single-file activity implementing full "Gratitude Gallery" flow
  - Mobile-first, localStorage based
*/

import { ref, reactive, computed, onMounted } from 'vue'

// Steps: 1 = welcome, 2 = add, 3 = submit anim, 4 = gallery+reflection
const step = ref(1)

// Form for add
const form = reactive({
    text: '',
    photoFile: null,   // File object
    photoDataUrl: null // base64 preview
})

// photo preview reactive prop
const photoPreview = ref(null)

// entries loaded from localStorage
const entries = ref([])
const pageSize = 10
const allLoaded = ref(false)
let loadIndex = 0

// reactions config
const reactionTypes = {
    heart: { emoji: '❤️' },
    clap:  { emoji: '🙌' },
    like:  { emoji: '👍' },
    star:  { emoji: '🌟' }
}

// quick moods for reflection
const moods = [
    { label: 'Calm', emoji: '😌' },
    { label: 'Grateful', emoji: '😊' },
    { label: 'Hopeful', emoji: '🙂' },
    { label: 'Uplifted', emoji: '😄' },
    { label: 'Neutral', emoji: '😐' },
]

const selectedMood = ref('')
const moodNote = ref('')
const reflectionSaved = ref(false)

// basic client-side "user id" to prevent double react in local storage per session
const sessionId = `sess_${Date.now()}_${Math.floor(Math.random()*9999)}`

// localStorage key
const STORAGE_KEY = 'gratitude_gallery_entries_v1'
const REFLECTION_KEY = 'gratitude_gallery_reflections_v1'

// remaining chars
const remainingChars = computed(() => 160 - (form.text ? form.text.length : 0))
const canSubmit = computed(() => form.text && form.text.trim().length > 0)

// Helpers
function start() {
    step.value = 2
    resetForm()
}

function onTextInput() {
    // simple trimming rule: do not auto-trim, but enforce maxlength via textarea attribute
}

function applySuggestion(s) {
    form.text = s
}

function onPhotoChange(e) {
    const file = e.target.files && e.target.files[0]
    if (!file) {
        form.photoFile = null
        photoPreview.value = null
        return
    }
    // create preview
    form.photoFile = file
    const reader = new FileReader()
    reader.onload = (ev) => {
        form.photoDataUrl = ev.target.result
        photoPreview.value = ev.target.result
    }
    reader.readAsDataURL(file)
}

// submit: create entry, save to localStorage
function submitGratitude() {
    const entry = {
        id: 'g_' + Date.now() + '_' + Math.floor(Math.random()*999),
        text: form.text.trim(),
        photo: form.photoDataUrl || null,
        ts: Date.now(),
        reactions: { heart: 0, clap: 0, like: 0, star: 0 },
        comments: [],
        meta: { anonymous: true },
        // UI helpers:
        showComment: false,
        _newComment: '',
        _reactedBy: {} // tracks sessionIds reacting in-memory (also stored)
    }

    // read existing
    const raw = localStorage.getItem(STORAGE_KEY)
    const arr = raw ? JSON.parse(raw) : []
    arr.unshift(entry)
    localStorage.setItem(STORAGE_KEY, JSON.stringify(arr))
    // show submit anim
    step.value = 3
    // after short delay move to gallery and load entries
    setTimeout(() => {
        goToGallery()
    }, 1100)
}

function goToGallery() {
    step.value = 4
    loadIndex = 0
    entries.value = []
    allLoaded.value = false
    loadMore()
}

// load entries with paging
function loadMore() {
    const raw = localStorage.getItem(STORAGE_KEY)
    const arr = raw ? JSON.parse(raw) : []
    if (!arr || arr.length === 0) {
        allLoaded.value = true
        entries.value = []
        return
    }
    const next = arr.slice(loadIndex, loadIndex + pageSize)
    // ensure UI helpers exist
    next.forEach(e => {
        e.showComment = false
        e._newComment = ''
        if (!e.reactions) e.reactions = { heart:0, clap:0, like:0, star:0 }
        if (!e.comments) e.comments = []
    })
    entries.value.push(...next)
    loadIndex += next.length
    if (loadIndex >= arr.length) allLoaded.value = true
}

// refresh from storage (reload all)
function refreshFromStorage() {
    loadIndex = 0
    entries.value = []
    allLoaded.value = false
    loadMore()
}

// clear local gallery (debug)
function clearGallery() {
    if (!confirm('Clear local gallery (this only clears localStorage)?')) return
    localStorage.removeItem(STORAGE_KEY)
    entries.value = []
    allLoaded.value = true
}

// reactions: toggles
function toggleReaction(entryId, key) {
    // mutate in localStorage and entries
    const raw = localStorage.getItem(STORAGE_KEY)
    const arr = raw ? JSON.parse(raw) : []
    const idx = arr.findIndex(e => e.id === entryId)
    if (idx === -1) return

    // Use a simple per-session "react set" stored in entry._reactedBy[sessionId] to prevent multiple increments
    const entry = arr[idx]
    entry._reactedBy = entry._reactedBy || {}
    const userReactKey = `${sessionId}_${key}`
    if (entry._reactedBy[userReactKey]) {
        // already reacted -> remove reaction
        entry.reactions[key] = Math.max(0, (entry.reactions && entry.reactions[key]) ? entry.reactions[key] - 1 : 0)
        delete entry._reactedBy[userReactKey]
    } else {
        entry.reactions = entry.reactions || { heart:0, clap:0, like:0, star:0 }
        entry.reactions[key] = (entry.reactions[key] || 0) + 1
        entry._reactedBy[userReactKey] = true
    }

    // write back
    arr[idx] = entry
    localStorage.setItem(STORAGE_KEY, JSON.stringify(arr))

    // update visible entries array
    const vIdx = entries.value.findIndex(e => e.id === entryId)
    if (vIdx !== -1) entries.value[vIdx] = { ...entry, showComment: entries.value[vIdx].showComment, _newComment: entries.value[vIdx]._newComment }
}

// detect if current session reacted for UI
function userReacted(entryId, key) {
    const raw = localStorage.getItem(STORAGE_KEY)
    const arr = raw ? JSON.parse(raw) : []
    const entry = arr.find(e => e.id === entryId)
    if (!entry || !entry._reactedBy) return false
    return !!entry._reactedBy[`${sessionId}_${key}`]
}

// comment toggles
function toggleCommentBox(idx) {
    entries.value[idx].showComment = !entries.value[idx].showComment
}

// add comment to entry (persist)
function addComment(entry) {
    if (!entry._newComment || !entry._newComment.trim()) return
    const raw = localStorage.getItem(STORAGE_KEY)
    const arr = raw ? JSON.parse(raw) : []
    const idx = arr.findIndex(e => e.id === entry.id)
    if (idx === -1) return
    arr[idx].comments = arr[idx].comments || []
    arr[idx].comments.push(entry._newComment.trim())
    // persist
    localStorage.setItem(STORAGE_KEY, JSON.stringify(arr))
    // update UI entry
    entry.comments.push(entry._newComment.trim())
    entry._newComment = ''
}

// date formatting
function formatDate(ts) {
    const d = new Date(ts)
    return d.toLocaleString()
}

// export gallery (download JSON)
function downloadGallery() {
    const raw = localStorage.getItem(STORAGE_KEY) || '[]'
    const blob = new Blob([raw], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `gratitude_gallery_${new Date().toISOString().slice(0,10)}.json`
    a.click()
    URL.revokeObjectURL(url)
}

// save reflection (store mood + note)
function saveReflection() {
    const raw = localStorage.getItem(REFLECTION_KEY)
    const arr = raw ? JSON.parse(raw) : []
    arr.unshift({
        ts: Date.now(),
        mood: selectedMood.value || null,
        note: moodNote.value || ''
    })
    localStorage.setItem(REFLECTION_KEY, JSON.stringify(arr))
    reflectionSaved.value = true
    setTimeout(() => reflectionSaved.value = false, 2800)
    // clear selection if needed
    selectedMood.value = ''
    moodNote.value = ''
}

// reset form helpers
function resetForm() {
    form.text = ''
    form.photoFile = null
    form.photoDataUrl = null
    photoPreview.value = null
}

// on mount ensure entries available
onMounted(() => {
    // create initial arrays if not present
    const raw = localStorage.getItem(STORAGE_KEY)
    if (!raw) localStorage.setItem(STORAGE_KEY, JSON.stringify([]))
})
</script>

<style scoped>
/* Mobile-first styling, centered headings/buttons, avoids horizontal scrolls */

/* Container tweaks */
.gratitude-app {
    max-width: 760px;
    margin: 0 auto;
    padding-left: 12px;
    padding-right: 12px;
}

/* Card visuals */
.card {
    border: 1px solid rgba(0,0,0,0.04);
    background: #fff;
}

/* Photo preview */
.img-preview {
    max-width: 160px;
    max-height: 160px;
    object-fit: cover;
    display: inline-block;
}

/* Thumbnails in gallery */
.img-thumb {
    width: 64px;
    height: 64px;
    object-fit: cover;
}

/* animation box */
.animation-box {
    height: 110px;
    display:flex;
    align-items:center;
    justify-content:center;
}
.floating-graphic {
    font-size: 48px;
    animation: floatUp 1s ease-in-out;
}
@keyframes floatUp {
    0% { transform: translateY(12px) scale(0.9); opacity: 0; }
    60% { transform: translateY(-6px) scale(1.05); opacity: 1; }
    100% { transform: translateY(0) scale(1); opacity: 1; }
}

/* Reaction buttons row */
.reactions-row { 
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items:center;
}
.reaction-btn {
    min-width:38px;
    height:38px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    background:#fff;
    border:1px solid rgba(0,0,0,0.08);
    font-size:18px;
}
.reaction-btn.reacted {
    background: #f8d7da; /* red-ish theme */
    border-color: #dc3545;
    color: #000;
}

/* comment rows */
.comment-row { 
    background: #f8f9fa;
    padding:6px 8px;
    border-radius:6px;
}

/* mood row - avoid horizontal scrolling */
.mood-row-wrap {
    display:flex;
    justify-content:center;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;       /* wrap to next line on small screens */
}
.mood-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:18px;
    padding:6px 10px;
}

/* centered buttons and headings */
.text-center { text-align:center !important; }

/* small-screen optimizations */
@media (max-width: 420px) {
    .img-preview { max-width: 130px; max-height: 130px; }
    .img-thumb { width:52px; height:52px; }
    .reaction-btn { min-width:34px; height:34px; font-size:16px; }
    .floating-graphic { font-size:40px; }
}

/* ensure no horizontal overflow from emoji buttons */
.entry-card, .reactions-row, .mood-row-wrap {
    overflow-wrap: anywhere;
    word-break: break-word;
}

/* ensure action buttons centered and compact on mobile */
.d-flex.gap-2 > .btn {
    flex: 1 1 0;
}

/* subtle shadow for better contrast */
.shadow-sm {
    box-shadow: 0 1px 6px rgba(0,0,0,0.04);
}
</style>
