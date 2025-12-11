<template>
	<div class="container py-4 gratitude-wall">
		<h1 class="text-center fw-bold mb-2">Gratitude Wall</h1>
		<p class="text-center text-muted mb-4">Share a short thank-you note. Choose visibility and category. Be kind — posts are anonymous by default.</p>

		<!-- Create / Edit card -->
		<div class="card p-3 mb-3 rounded-3 shadow-sm">
			<h5 class="text-center mb-2">{{ editMode ? 'Edit Post' : 'Create a Gratitude Post' }}</h5>

			<textarea
				v-model="form.text"
				class="form-control mb-2"
				rows="3"
				maxlength="250"
				placeholder="Write your gratitude message (max 250 characters)"
			></textarea>
			<div class="d-flex justify-content-between small text-muted mb-2">
				<div>{{ 250 - (form.text ? form.text.length : 0) }} chars left</div>
				<div>{{ form.text.trim().length ? 'Ready' : 'Required' }}</div>
			</div>

			<div class="mb-2">
				<label class="form-label small mb-1">Category</label>
				<select v-model="form.category" class="form-select form-select-sm">
					<option value="">Select (optional)</option>
					<option>Family</option>
					<option>Colleagues</option>
					<option>Nature</option>
					<option>Community</option>
					<option>Other</option>
				</select>
			</div>

			<div class="mb-2">
				<label class="form-label small mb-1">Visibility</label>
				<select v-model="form.visibility" class="form-select form-select-sm">
					<option value="Public">Public</option>
					<option value="Team">Team-only</option>
					<option value="Private">Private</option>
				</select>
			</div>

			<div class="mb-2">
				<label class="form-label small mb-1">Optional photo (no faces)</label>
				<input type="file" accept="image/*" @change="onPhotoChange" class="form-control form-control-sm" />
			</div>

			<div v-if="photoPreview" class="text-center mb-2">
				<img :src="photoPreview" class="img-preview rounded" alt="preview" />
				<div class="small text-muted mt-1">Photo preview</div>
			</div>

			<div class="d-flex gap-2 mt-2">
				<button class="btn btn-outline-secondary w-50" @click="resetForm">Reset</button>
				<button class="btn btn-danger w-50" :disabled="!canPost" @click="savePost">{{ editMode ? 'Save Changes' : 'Post' }}</button>
			</div>
		</div>

		<!-- Filters / Controls -->
		<div class="d-flex gap-2 align-items-center mb-3 flex-wrap controls">
			<div class="flex-grow-1">
				<select v-model="filters.category" class="form-select form-select-sm">
					<option value="">All categories</option>
					<option>Family</option>
					<option>Colleagues</option>
					<option>Nature</option>
					<option>Community</option>
					<option>Other</option>
				</select>
			</div>

			<div>
				<select v-model="filters.visibility" class="form-select form-select-sm">
					<option value="Public">Public</option>
					<option value="Team">Team-only</option>
					<option value="Private">Private</option>
					<option value="">All</option>
				</select>
			</div>

			<div>
				<button class="btn btn-outline-secondary btn-sm" @click="refreshFeed">Refresh</button>
			</div>
		</div>

		<!-- Weekly summary / badges -->
		<div class="card p-3 mb-3 rounded-3 shadow-sm text-center">
			<h6 class="mb-1">Weekly Check-in</h6>
			<div class="small text-muted mb-2">This week you posted: <strong>{{ weeklyCounts.thisWeek }}</strong> times</div>
			<div class="d-flex justify-content-center gap-2 align-items-center">
				<div class="badge-box text-center">
					<div class="badge-text">3-week streak</div>
					<div v-if="badges.streak" class="badge badge-success">Earned</div>
					<div v-else class="badge badge-secondary">Locked</div>
				</div>
				<div class="small text-muted">Most reacted post this week: <strong>{{ weeklyTop ? weeklyTop.text.slice(0,40) : '—' }}</strong></div>
			</div>
		</div>

		<!-- Feed -->
		<div class="gallery-section">
			<div v-if="feed.length === 0" class="card p-3 mb-3 text-center">
				<p class="mb-0 text-muted">No posts match the filters.</p>
			</div>

			<div v-for="(post, idx) in feed" :key="post.id" class="card p-3 mb-3 rounded-3 shadow-sm post-card">
				<div class="d-flex justify-content-between align-items-start mb-2">
					<div>
						<div class="small text-muted">{{ post.category || 'Uncategorized' }} • {{ post.visibility }}</div>
						<div class="fw-semibold mt-1" v-text="post.text"></div>
						<div class="small text-muted mt-1">{{ formatDate(post.ts) }}</div>
					</div>
					<div v-if="post.photo" class="ms-2">
						<img :src="post.photo" class="img-thumb rounded" alt="photo" />
					</div>
				</div>

				<!-- Reaction row -->
				<div class="d-flex gap-2 align-items-center flex-wrap reaction-row">
					<button class="btn reaction-btn btn-sm" :class="{ reacted: reacted(post.id,'heart') }" @click="toggleReaction(post.id,'heart')">❤️</button>
					<span class="small text-muted">{{ post.reactions.heart || 0 }}</span>

					<button class="btn reaction-btn btn-sm" :class="{ reacted: reacted(post.id,'clap') }" @click="toggleReaction(post.id,'clap')">👏</button>
					<span class="small text-muted">{{ post.reactions.clap || 0 }}</span>

					<button class="btn reaction-btn btn-sm" :class="{ reacted: reacted(post.id,'ty') }" @click="toggleReaction(post.id,'ty')">🙏</button>
					<span class="small text-muted">{{ post.reactions.ty || 0 }}</span>

					<!-- action buttons -->
					<div class="ms-auto d-flex gap-2">
						<button class="btn btn-outline-secondary btn-sm" @click="startEdit(post)">Edit</button>
						<button class="btn btn-outline-danger btn-sm" @click="deletePost(post.id)">Delete</button>
						<button class="btn btn-outline-warning btn-sm" @click="reportPost(post.id)">Report</button>
					</div>
				</div>

				<!-- Reported badge -->
				<div v-if="post.reported" class="small text-danger mt-2">⚠️ Reported — flagged for review</div>
			</div>

			<!-- pagination controls -->
			<div class="d-flex justify-content-between align-items-center mt-2">
				<button class="btn btn-outline-secondary btn-sm" @click="loadPrev" :disabled="page === 1">Prev</button>
				<div class="small text-muted">Page {{ page }}</div>
				<button class="btn btn-outline-secondary btn-sm" @click="loadNext" :disabled="!hasMore">Next</button>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watchEffect } from 'vue'

/* Local storage key */
const STORAGE_KEY = 'gratitude_wall_posts_v1'
/* simple session id */
const sessionId = `sess_${Date.now()}_${Math.floor(Math.random()*10000)}`

/* Form */
const form = reactive({
	text: '',
	category: '',
	visibility: 'Public',
	photoFile: null,
	photoDataUrl: null,
	editingId: null
})
const photoPreview = ref(null)
const editMode = computed(() => !!form.editingId)
const canPost = computed(() => form.text && form.text.trim().length > 0 && form.text.trim().length <= 250)

/* paging and feed */
const page = ref(1)
const perPage = 8
const allPosts = ref([]) // loaded from storage
const filters = reactive({ category: '', visibility: 'Public' })
const feed = ref([])

/* badges / weekly stats */
const badges = reactive({ streak: false })
const weeklyCounts = reactive({ thisWeek: 0 })
const weeklyTop = ref(null)

/* lifecycle */
onMounted(() => {
	ensureStorage()
	loadAllPosts()
	applyFilters()
	computeWeekly()
})

/* storage helpers */
function ensureStorage() {
	if (!localStorage.getItem(STORAGE_KEY)) localStorage.setItem(STORAGE_KEY, JSON.stringify([]))
}
function loadAllPosts() {
	const raw = localStorage.getItem(STORAGE_KEY)
	allPosts.value = raw ? JSON.parse(raw) : []
	// ensure reaction objects exist
	allPosts.value.forEach(p => {
		if (!p.reactions) p.reactions = { heart: 0, clap: 0, ty: 0 }
		if (!p._reacted) p._reacted = {} // keyed by sessionId+reaction
	})
}
function persistAll() {
	localStorage.setItem(STORAGE_KEY, JSON.stringify(allPosts.value))
}

/* photo handling */
function onPhotoChange(e) {
	const file = e.target.files && e.target.files[0]
	if (!file) {
		form.photoFile = null
		photoPreview.value = null
		form.photoDataUrl = null
		return
	}
	form.photoFile = file
	const reader = new FileReader()
	reader.onload = (ev) => {
		form.photoDataUrl = ev.target.result
		photoPreview.value = ev.target.result
	}
	reader.readAsDataURL(file)
}

/* create / save post */
function savePost() {
	const now = Date.now()
	if (form.editingId) {
		const idx = allPosts.value.findIndex(p => p.id === form.editingId)
		if (idx !== -1) {
			allPosts.value[idx].text = form.text.trim()
			allPosts.value[idx].category = form.category
			allPosts.value[idx].visibility = form.visibility
			allPosts.value[idx].photo = form.photoDataUrl || null
			allPosts.value[idx].editedAt = now
		}
	} else {
		const post = {
			id: 'p_' + now + '_' + Math.floor(Math.random()*999),
			text: form.text.trim(),
			category: form.category,
			visibility: form.visibility,
			photo: form.photoDataUrl || null,
			ts: now,
			editedAt: null,
			reactions: { heart: 0, clap: 0, ty: 0 },
			_reported: false,
			reported: false,
			_reacted: {} // session reaction map
		}
		allPosts.value.unshift(post)
	}
	persistAll()
	resetForm()
	loadAllPosts()
	applyFilters()
	computeWeekly()
}

/* edit flow */
function startEdit(post) {
	form.text = post.text
	form.category = post.category
	form.visibility = post.visibility
	form.photoDataUrl = post.photo || null
	photoPreview.value = post.photo || null
	form.editingId = post.id
	window.scrollTo({ top: 0, behavior: 'smooth' })
}

/* delete */
function deletePost(id) {
	if (!confirm('Delete this post? This will remove it locally.')) return
	const idx = allPosts.value.findIndex(p => p.id === id)
	if (idx !== -1) {
		allPosts.value.splice(idx, 1)
		persistAll()
		loadAllPosts()
		applyFilters()
		computeWeekly()
	}
}

/* report */
function reportPost(id) {
	const idx = allPosts.value.findIndex(p => p.id === id)
	if (idx === -1) return
	allPosts.value[idx].reported = true
	allPosts.value[idx]._reported = true
	persistAll()
	loadAllPosts()
	applyFilters()
	alert('Reported locally. Moderator review required (server integration needed).')
}

/* reset form */
function resetForm() {
	form.text = ''
	form.category = ''
	form.visibility = 'Public'
	form.photoFile = null
	form.photoDataUrl = null
	form.editingId = null
	photoPreview.value = null
}

/* reactions: simple session toggle */
function toggleReaction(postId, key) {
	const idx = allPosts.value.findIndex(p => p.id === postId)
	if (idx === -1) return
	const post = allPosts.value[idx]
	post._reacted = post._reacted || {}
	const reactKey = `${sessionId}_${key}`
	if (post._reacted[reactKey]) {
		// undo
		post.reactions[key] = Math.max(0, (post.reactions[key] || 0) - 1)
		delete post._reacted[reactKey]
	} else {
		post.reactions[key] = (post.reactions[key] || 0) + 1
		post._reacted[reactKey] = true
	}
	persistAll()
	loadAllPosts()
	applyFilters()
}

/* check reacted */
function reacted(postId, key) {
	const post = allPosts.value.find(p => p.id === postId)
	if (!post || !post._reacted) return false
	return !!post._reacted[`${sessionId}_${key}`]
}

/* feed + filtering + pagination */
const hasMore = computed(() => {
	const totalFiltered = filteredPosts.value.length
	return page.value * perPage < totalFiltered
})
function applyFilters() {
	page.value = 1
	const filtered = filteredPosts.value
	const start = (page.value - 1) * perPage
	feed.value = filtered.slice(start, start + perPage)
}
function loadNext() {
	if (!hasMore.value) return
	page.value++
	const filtered = filteredPosts.value
	const start = (page.value - 1) * perPage
	feed.value = filtered.slice(start, start + perPage)
}
function loadPrev() {
	if (page.value === 1) return
	page.value--
	const filtered = filteredPosts.value
	const start = (page.value - 1) * perPage
	feed.value = filtered.slice(start, start + perPage)
}
function refreshFeed() {
	loadAllPosts()
	applyFilters()
}

/* filtered computed */
const filteredPosts = computed(() => {
	// sort by ts desc
	let arr = [...allPosts.value].sort((a,b) => b.ts - a.ts)
	// visibility filter: if filters.visibility is empty => show all; else only that visibility
	if (filters.visibility) arr = arr.filter(p => p.visibility === filters.visibility)
	// category filter
	if (filters.category) arr = arr.filter(p => p.category === filters.category)
	return arr
})

/* weekly computations and badges */
function startOfWeek(ts = Date.now()) {
	// start Monday (set hours to 0)
	const d = new Date(ts)
	const day = (d.getDay() + 6) % 7 // Monday=0
	d.setDate(d.getDate() - day)
	d.setHours(0,0,0,0)
	return d.getTime()
}
function computeWeekly() {
	const now = Date.now()
	const weekStart = startOfWeek(now)
	const weekEnd = weekStart + 7*24*3600*1000
	const thisWeekPosts = allPosts.value.filter(p => p.ts >= weekStart && p.ts < weekEnd)
	weeklyCounts.thisWeek = thisWeekPosts.length
	// most reacted this week
	if (thisWeekPosts.length) {
		const sorted = [...thisWeekPosts].sort((a,b) => {
			const ar = (a.reactions.heart||0) + (a.reactions.clap||0) + (a.reactions.ty||0)
			const br = (b.reactions.heart||0) + (b.reactions.clap||0) + (b.reactions.ty||0)
			return br - ar
		})
		weeklyTop.value = sorted[0]
	} else weeklyTop.value = null

	// compute simple 3-week streak: check previous 3 weeks (including this) whether user posted at least once per week
	let weekCount = 0
	let base = weekStart
	for (let i=0;i<3;i++) {
		const s = base - i*7*24*3600*1000
		const start = startOfWeek(s)
		const end = start + 7*24*3600*1000
		const any = allPosts.value.some(p => p.ts >= start && p.ts < end)
		if (any) weekCount++
	}
	badges.streak = (weekCount >= 3)
}

/* computed for weeklyTop text */
const weeklyTopComputed = computed(() => weeklyTop.value)

/* helper date format */
function formatDate(ts) {
	const d = new Date(ts)
	return d.toLocaleString()
}

/* initial load and apply */
onMounted(() => {
	loadAllPosts()
	applyFilters()
	computeWeekly()
})

/* watch filters (simple) */
watchEffect(() => {
	applyFilters()
})

</script>

<style scoped>
/* Mobile-first styles, centered headings & buttons, no horizontal scroll */

/* container */
.gratitude-wall {
	max-width: 760px;
	margin: 0 auto;
	padding-left: 12px;
	padding-right: 12px;
}

/* visuals */
.card {
	border: 1px solid rgba(0,0,0,0.05);
	background: #fff;
}
.img-preview {
	max-width: 160px;
	max-height: 160px;
	object-fit: cover;
	margin: 0 auto;
	display: block;
}
.img-thumb {
	width: 64px;
	height: 64px;
	object-fit: cover;
}

/* reaction styles */
.reaction-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:8px; }
.reaction-btn { min-width:36px; height:36px; border-radius:8px; font-size:16px; display:flex; align-items:center; justify-content:center; }
.reaction-btn.reacted { background: #f8d7da; border: 1px solid #dc3545; color: #000; }

.controls select { min-width: 120px; }

/* ensure no horizontal overflow */
.post-card, .reaction-row, .controls { overflow-wrap:anywhere; word-break:break-word; }

/* small screen tweaks */
@media (max-width:480px) {
	.img-preview { max-width: 120px; max-height: 120px; }
	.img-thumb { width: 52px; height: 52px; }
	.reaction-btn { min-width:32px; height:32px; font-size:14px; }
}

/* center controls on small */
.d-flex.gap-2 > .btn { flex: 1 1 0; }

/* utility */
.text-center { text-align:center !important; }
.shadow-sm { box-shadow: 0 1px 6px rgba(0,0,0,0.04); }

/* badges */
.badge { padding:0.35rem 0.6rem; border-radius:0.5rem; }
.badge-success { background:#d4edda; color:#155724; }
.badge-secondary { background:#e9ecef; color:#6c757d; }

</style>
