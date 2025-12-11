<template>
    <div class="container py-5">
        <h1 class="text-center mb-4">Resilience Mapping</h1>

        <!-- INTRO -->
        <div v-if="step === 0" class="card p-4 shadow-sm mb-4">
            <h4 class="mb-3">Welcome</h4>
            <p>Let’s explore your personal strengths and supports that help you stay resilient.
                Think of this as your <strong>Resilience Map</strong>.</p>

            <p class="mt-3 fw-semibold">
                <strong>“Your Resilience Map shows you how strong and supported you really are.”</strong>
            </p>

            <button class="btn btn-success mt-3" @click="step = 1">Start Activity</button>
        </div>

        <!-- STEP 1: CORE -->
        <div v-if="step === 1" class="card p-4 shadow-sm mb-4">
            <h4 class="mb-3">Step 1 — Your Center</h4>
            <p>Enter your name, nickname, or a word that represents you.</p>

            <input type="text" v-model="center" class="form-control mb-3" placeholder="Enter your name…">

            <button class="btn btn-success" :disabled="!center.trim()" @click="saveCenter">
                Continue
            </button>
        </div>

        <!-- STEP 2: BRANCH INPUT -->
        <div v-if="step === 2" class="card p-4 shadow-sm mb-4">
            <h4 class="mb-3">Step 2 — {{ currentBranch.title }}</h4>
            <p>{{ currentBranch.prompt }}</p>

            <div class="input-group mb-3">
                <input type="text" v-model="itemInput" class="form-control" placeholder="Add an item…">
                <button class="btn btn-success" @click="addItem" :disabled="!itemInput.trim()">+ Add</button>
            </div>

            <div class="mb-3">
                <span v-for="(i, idx) in branches[currentBranchKey]" :key="idx"
                      class="badge rounded-pill me-2 mb-2 branch-badge"
                      :class="colorClass(currentBranchKey)">
                    {{ i }}
                </span>
            </div>

            <div class="d-flex justify-content-between">
                <button class="btn btn-secondary" @click="prevBranch" :disabled="currentBranchKey === 'support'">
                    Back
                </button>

                <button class="btn btn-success" @click="nextBranch">Next</button>
            </div>
        </div>

        <!-- STEP 3: RESILIENCE MAP -->
        <div v-if="step === 3" class="card p-4 shadow-sm mb-4">
            <h4 class="mb-3">Your Resilience Map</h4>

            <div class="map-container">

                <!-- Support -->
                <div class="branch-group">
                    <div class="branch-title">Support Network</div>
                    <div v-for="(item, i) in branches.support" :key="'s'+i"
                         class="branch-item" :class="colorClass('support')">
                        {{ item }}
                    </div>
                </div>

                <!-- Strengths -->
                <div class="branch-group">
                    <div class="branch-title">Personal Strengths</div>
                    <div v-for="(item, i) in branches.strengths" :key="'st'+i"
                         class="branch-item" :class="colorClass('strengths')">
                        {{ item }}
                    </div>
                </div>

                <!-- Center bubble -->
                <div class="map-center">{{ center }}</div>

                <!-- Coping -->
                <div class="branch-group">
                    <div class="branch-title">Coping Strategies</div>
                    <div v-for="(item, i) in branches.coping" :key="'c'+i"
                         class="branch-item" :class="colorClass('coping')">
                        {{ item }}
                    </div>
                </div>

                <!-- Hobbies -->
                <div class="branch-group">
                    <div class="branch-title">Hobbies & Outlets</div>
                    <div v-for="(item, i) in branches.hobbies" :key="'h'+i"
                         class="branch-item" :class="colorClass('hobbies')">
                        {{ item }}
                    </div>
                </div>

            </div>

            <button class="btn btn-success mt-3" @click="step = 4">Continue</button>
        </div>

        <!-- STEP 4: REFLECTION -->
        <div v-if="step === 4" class="card p-4 shadow-sm mb-4">
            <h4 class="mb-3">Reflection</h4>

            <label class="fw-bold">Which branch feels strongest now?</label>
            <select v-model="reflection.strongest" class="form-select mb-3">
                <option disabled value="">Select…</option>
                <option value="support">Support Network</option>
                <option value="strengths">Personal Strengths</option>
                <option value="coping">Coping Strategies</option>
                <option value="hobbies">Hobbies / Outlets</option>
            </select>

            <label class="fw-bold">Which branch needs improvement?</label>
            <select v-model="reflection.improve" class="form-select mb-2">
                <option disabled value="">Select…</option>
                <option value="support">Support Network</option>
                <option value="strengths">Personal Strengths</option>
                <option value="coping">Coping Strategies</option>
                <option value="hobbies">Hobbies / Outlets</option>
            </select>

            <textarea v-model="reflection.improveNote"
                      class="form-control mb-3" rows="2"
                      placeholder="Add a note (optional)…"></textarea>

            <label class="fw-bold">Anything surprising?</label>
            <textarea v-model="reflection.surprise" class="form-control mb-3" rows="3"></textarea>

            <button class="btn btn-success" @click="saveReflection">Finish Activity</button>
        </div>

        <!-- STEP 5: FINAL -->
        <div v-if="step === 5" class="card p-4 shadow-sm mb-4 text-center">
            <h4 class="mb-3">Great Work!</h4>
            <p>You’ve completed your Resilience Map.</p>

            <button class="btn btn-outline-success me-2 mb-3" @click="step = 3">View Map</button>
            <!-- <button class="btn btn-outline-success" @click="downloadJSON">Export Map</button> -->
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue"

const step = ref(0)

/* STEP 1 */
const center = ref("")
const saveCenter = () => {
    localStorage.setItem("resilience_center", center.value)
    step.value = 2
}

/* STEP 2 */
const branches = ref({
    support: [],
    strengths: [],
    coping: [],
    hobbies: [],
})

const itemInput = ref("")
const currentBranchKey = ref("support")

const branchOrder = ["support", "strengths", "coping", "hobbies"]

const branchTitles = {
    support: "Support Network",
    strengths: "Personal Strengths",
    coping: "Coping Strategies",
    hobbies: "Hobbies & Outlets",
}

const branchPrompts = {
    support: "Who supports you when you're stressed?",
    strengths: "What personal qualities help you cope?",
    coping: "What do you usually do when stressed?",
    hobbies: "What activities help you recharge?",
}

const currentBranch = computed(() => ({
    title: branchTitles[currentBranchKey.value],
    prompt: branchPrompts[currentBranchKey.value],
}))

const addItem = () => {
    if (!itemInput.value.trim()) return
    branches.value[currentBranchKey.value].push(itemInput.value.trim())
    itemInput.value = ""
}

const nextBranch = () => {
    const i = branchOrder.indexOf(currentBranchKey.value)
    if (i < branchOrder.length - 1) {
        currentBranchKey.value = branchOrder[i + 1]
    } else step.value = 3
}

const prevBranch = () => {
    const i = branchOrder.indexOf(currentBranchKey.value)
    if (i > 0) currentBranchKey.value = branchOrder[i - 1]
}

/* STEP 3 */
const colorClass = (key) => ({
    support: "bg-support",
    strengths: "bg-strengths",
    coping: "bg-coping",
    hobbies: "bg-hobbies",
}[key])

/* STEP 4 */
const reflection = ref({
    strongest: "",
    improve: "",
    improveNote: "",
    surprise: "",
})

const saveReflection = () => {
    localStorage.setItem("resilience_map", JSON.stringify({
        center: center.value,
        branches: branches.value,
        reflection: reflection.value
    }))
    step.value = 5
}

/* EXPORT */
const downloadJSON = () => {
    const blob = new Blob(
        [JSON.stringify({ center: center.value, branches: branches.value, reflection: reflection.value }, null, 2)],
        { type: "application/json" }
    )
    const url = URL.createObjectURL(blob)
    const a = document.createElement("a")
    a.href = url
    a.download = "resilience-map.json"
    a.click()
}
</script>

<style scoped>
/* Responsive Map (no overlap) */
.map-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 28px;
    width: 100%;
    max-width: 480px;
    margin: 0 auto;
}

/* Center Bubble */
.map-center {
    background: #D80621;
    color: white;
    padding: 24px;
    border-radius: 100px;
    width: 150px;
    height: 150px;
    font-size: 20px;
    font-weight: bold;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Groups */
.branch-group {
    text-align: center;
    width: 100%;
}

/* Titles */
.branch-title {
    font-weight: bold;
    margin-bottom: 6px;
}

/* Items */
.branch-item {
    display: inline-block;
    padding: 6px 12px;
    margin: 4px;
    border-radius: 20px;
    font-size: 14px;
}

/* Colors */
.bg-support { background: #ffd9cc; }
.bg-strengths { background: #e3d4ff; }
.bg-coping { background: #d4f7e3; }
.bg-hobbies { background: #fff2b8; }

.branch-badge {
    font-size: 13px;
    padding: 7px 11px;
}
</style>
