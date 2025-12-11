<template>
  <div class="container py-5">
    <h1 class="text-center mb-3">Self-Discipline — Willpower Wallet</h1>
    <p class="text-muted text-center mb-4">Earn discipline points, track efforts, redeem for small healthy rewards.</p>

    <!-- Log Effort -->
    <div class="card mb-3 p-3">
      <h5>Log a disciplined effort</h5>
      <div class="mb-2">
        <label class="form-label">What did you do?</label>
        <select v-model="effort.type" class="form-select w-auto mb-2">
          <option value="Skipped sugar">Skipped sugar</option>
          <option value="Managed anger calmly">Managed anger calmly</option>
          <option value="Avoided social media">Avoided social media</option>
          <option value="Slept on time">Slept on time</option>
          <option value="Custom">-- Custom --</option>
        </select>
        <input v-if="effort.type === 'Custom'" v-model="effort.custom" class="form-control mb-2" placeholder="Describe your effort" />
      </div>

      <div class="mb-2">
        <label class="form-label">Difficulty</label>
        <select v-model="effort.difficulty" class="form-select w-auto">
          <option value="easy">Easy (+1)</option>
          <option value="moderate">Moderate (+3)</option>
          <option value="hard">Hard (+5)</option>
        </select>
      </div>

      <div class="d-flex gap-2 mt-2">
        <button class="btn btn-secondary" @click="resetEffort">Cancel</button>
        <button class="btn btn-accent" @click="logEffort">Log Effort</button>
      </div>
    </div>

    <!-- Wallet -->
    <div class="card mb-3 p-3">
      <h5>Your Wallet</h5>
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="small text-muted">Balance</div>
          <div class="fs-3 fw-bold">{{ balance }} pts</div>
        </div>
        <div class="text-end">
          <div class="small text-muted">Total earned</div>
          <div>{{ totalEarned }} pts</div>
        </div>
      </div>
    </div>

    <!-- History -->
    <div class="card mb-3 p-3">
      <h5>Recent Efforts</h5>
      <div v-if="effortLog.length === 0" class="text-muted">No efforts yet.</div>
      <ul class="list-group">
        <li v-for="(e,i) in effortLog.slice(0,8)" :key="e.id" class="list-group-item d-flex justify-content-between align-items-start">
          <div>
            <strong>{{ e.title }}</strong>
            <div class="small text-muted">{{ e.date }}</div>
          </div>
          <div class="text-end">
            <div :class="['fw-bold', e.points > 0 ? 'text-success' : 'text-muted']">+{{ e.points }}</div>
            <small class="text-muted">{{ e.difficulty }}</small>
          </div>
        </li>
      </ul>
    </div>

    <!-- Redeem -->
    <div class="card mb-3 p-3">
      <h5>Redeem Points</h5>
      <div class="mb-2">
        <label class="form-label">Choose reward</label>
        <select v-model="redeem.selected" class="form-select w-auto mb-2">
          <option v-for="r in rewards" :key="r.id" :value="r.id">{{ r.title }} — {{ r.cost }} pts</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-secondary" @click="openCreateReward">Create Reward</button>
        <button class="btn btn-accent" :disabled="!canRedeem" @click="redeemPoints">Redeem</button>
      </div>
    </div>

    <!-- Create reward -->
    <div v-if="showCreateReward" class="card mb-3 p-3">
      <h5>Create Reward</h5>
      <input v-model="newReward.title" class="form-control mb-2" placeholder="Reward title (e.g., Evening movie)" />
      <input type="number" v-model.number="newReward.cost" class="form-control mb-2 w-auto" placeholder="Cost in points" />
      <div class="d-flex gap-2">
        <button class="btn btn-secondary" @click="closeCreateReward">Cancel</button>
        <button class="btn btn-accent" @click="createReward">Save Reward</button>
      </div>
    </div>

    <!-- Wallet actions -->
    <div class="card p-3">
      <h5>Summary</h5>
      <div class="small text-muted mb-2">Weekly points earned: <strong>{{ weeklyPoints }}</strong></div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" @click="clearWallet">Clear Wallet</button>
        <button class="btn btn-outline-primary" @click="exportWallet">Export Log</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const effort = ref({ type: 'Skipped sugar', custom: '', difficulty: 'moderate' })
const effortLogKey = 'willpower-effort-log-v1'
const rewardsKey = 'willpower-rewards-v1'
const walletKey = 'willpower-wallet-v1'

// load logs/rewards/wallet
const effortLog = ref(JSON.parse(localStorage.getItem(effortLogKey) || '[]'))
const rewards = ref(JSON.parse(localStorage.getItem(rewardsKey) || '[]') || [
  { id: 'r1', title: 'Evening movie', cost: 10 },
  { id: 'r2', title: 'Favorite snack', cost: 5 }
])
const wallet = ref(JSON.parse(localStorage.getItem(walletKey) || '{ "balance": 0, "total": 0 }'))

const balance = computed(() => wallet.value.balance || 0)
const totalEarned = computed(() => wallet.value.total || 0)

const difficultyPoints = { easy: 1, moderate: 3, hard: 5 }

const resetEffort = () => {
  effort.value = { type: 'Skipped sugar', custom: '', difficulty: 'moderate' }
}

// log effort
const logEffort = () => {
  const title = effort.value.type === 'Custom' ? (effort.value.custom || 'Custom effort') : effort.value.type
  const points = difficultyPoints[effort.value.difficulty] || 1
  const rec = { id: Date.now(), title, difficulty: effort.value.difficulty, points, date: new Date().toLocaleString() }
  effortLog.value.unshift(rec)
  localStorage.setItem(effortLogKey, JSON.stringify(effortLog.value))
  // add to wallet
  wallet.value.balance = (wallet.value.balance || 0) + points
  wallet.value.total = (wallet.value.total || 0) + points
  localStorage.setItem(walletKey, JSON.stringify(wallet.value))
  resetEffort()
  alert(`+${points} points added to wallet`)
}

// redeem logic
const redeem = ref({ selected: null })
const redeemSelected = ref(null)
const showCreateReward = ref(false)
const newReward = ref({ title: '', cost: 10 })
const createReward = () => {
  if (!newReward.value.title.trim() || !newReward.value.cost) { alert('Fill reward'); return }
  const r = { id: 'r' + Date.now(), title: newReward.value.title.trim(), cost: Number(newReward.value.cost) }
  rewards.value.unshift(r)
  localStorage.setItem(rewardsKey, JSON.stringify(rewards.value))
  newReward.value = { title: '', cost: 10 }
  showCreateReward.value = false
}

const openCreateReward = () => showCreateReward.value = true
const closeCreateReward = () => { showCreateReward.value = false; newReward.value = { title: '', cost: 10 } }

const redeemSelectedId = ref(null)
const canRedeem = computed(() => {
  const id = redeemSelectedId.value
  if (!id) return false
  const r = rewards.value.find(x => x.id === id)
  return r && (wallet.value.balance >= r.cost)
})

const redeemPoints = () => {
  const id = redeemSelectedId.value
  const r = rewards.value.find(x => x.id === id)
  if (!r) { alert('Select a reward'); return }
  if (wallet.value.balance < r.cost) { alert('Not enough points'); return }
  if (!confirm(`Redeem "${r.title}" for ${r.cost} pts?`)) return
  wallet.value.balance -= r.cost
  localStorage.setItem(walletKey, JSON.stringify(wallet.value))
  // log redemption as negative points
  const rec = { id: Date.now(), title: `Redeemed: ${r.title}`, difficulty: 'redeem', points: -r.cost, date: new Date().toLocaleString() }
  effortLog.value.unshift(rec)
  localStorage.setItem(effortLogKey, JSON.stringify(effortLog.value))
  alert('Reward redeemed. Enjoy.')
}

// compute weekly points
const weeklyPoints = computed(() => {
  const last7 = effortLog.value.slice(0, 1000) // simple
  const today = new Date()
  const weekAgo = new Date(Date.now() - 7*24*60*60*1000)
  return last7.filter(e => new Date(e.date) > weekAgo).reduce((s, e) => s + (e.points > 0 ? e.points : 0), 0)
})

// export / clear
const exportWallet = () => {
  const blob = new Blob([JSON.stringify({ wallet: wallet.value, efforts: effortLog.value, rewards: rewards.value }, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a'); a.href = url; a.download = 'willpower-wallet.json'; a.click(); URL.revokeObjectURL(url)
}
const clearWallet = () => {
  if (!confirm('Clear wallet and logs?')) return
  localStorage.removeItem(walletKey); localStorage.removeItem(effortLogKey)
  wallet.value = { balance: 0, total: 0 }
  effortLog.value = []
}

</script>

<style scoped>
.btn-accent { background-color: #d80621; color: #fff; border: none; }
.card { border-radius: 0.6rem; }
.list-group-item.active { background-color: rgba(216,8,33,0.08); }
</style>
