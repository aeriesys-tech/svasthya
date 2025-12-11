<template>
    <div class="container py-4">

        <!-- Step 1: Welcome / Intro -->
        <div v-if="step === 1" class="text-center">
            <h2 class="mb-3">Small Talk Builder</h2>
            <p class="mb-4">Not every conversation needs to be serious. Let’s practice being friendly, casual, and human. Get ready for some small talk!</p>
            <button class="btn btn-primary" @click="step++">Start Practice</button>
        </div>

        <!-- Step 2: Select Scene -->
        <div v-else-if="step === 2" class="text-center">
            <h4 class="mb-3">Choose a situation to practice your small talk</h4>
            <div class="d-flex flex-column gap-3 align-items-center">
                <button v-for="(scene, index) in scenes" :key="index" class="btn btn-outline-primary w-100" @click="selectScene(scene)">
                    <span class="me-2">{{ scene.icon }}</span> {{ scene.title }}
                </button>
            </div>
        </div>

        <!-- Step 3: Chat Simulator -->
        <div v-else-if="step === 3">
            <h5 class="text-center mb-3">Let's start the conversation!</h5>
            <div class="chat-box mb-3 p-2 border rounded" style="min-height:200px; background:#f8f9fa;">
                <div v-for="(msg, i) in chatHistory" :key="i" :class="['mb-2', msg.sender==='user'?'text-end':'text-start']">
                    <span :class="['badge', msg.sender==='user'?'bg-primary':'bg-secondary']">{{ msg.text }}</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <select v-model="selectedReply" class="form-select flex-grow-1">
                    <option disabled value="">Choose a reply</option>
                    <option v-for="(reply,index) in currentReplies" :key="index" :value="reply">{{ reply }}</option>
                </select>
                <input v-model="typedReply" type="text" class="form-control flex-grow-1" placeholder="Or type your own"/>
                <button class="btn btn-primary" @click="sendReply" :disabled="!selectedReply && !typedReply">Send</button>
            </div>
        </div>

        <!-- Step 4: Emoji Tone Feedback -->
        <div v-else-if="step === 4" class="text-center">
            <h4 class="mb-3">Conversation Completed!</h4>
            <p class="mb-3">Your Friendliness Score: {{ friendlinessScore }}%</p>
            <div class="d-flex justify-content-center flex-wrap gap-3 mb-3">
                <div class="p-2 border rounded">
                    😊 Used Name: {{ feedback.usedName ? '✔️' : '❌' }}
                </div>
                <div class="p-2 border rounded">
                    ❓ Open-ended Question: {{ feedback.openQuestion ? '✔️' : '❌' }}
                </div>
                <div class="p-2 border rounded">
                    ❤️ Showed Empathy: {{ feedback.empathy ? '✔️' : '❌' }}
                </div>
            </div>
            <button class="btn btn-primary" @click="step++">Next: Reflection</button>
        </div>

        <!-- Step 5: Reflection -->
        <div v-else-if="step === 5" class="text-center">
            <h5 class="mb-3">How did that conversation feel?</h5>
            <textarea v-model="reflection" class="form-control mb-3" placeholder="Would you try this in real life today?" rows="3"></textarea>
            <button class="btn btn-primary" :disabled="!reflection || reflection.length<15" @click="saveSession">Save Reflection</button>
        </div>

        <!-- Step 6: Save / End Screen -->
        <div v-else-if="step === 6" class="text-center">
            <h4 class="mb-3">Great job! You’ve completed this small talk practice.</h4>
            <p>Notification: This conversation counts towards your weekly practice streak!</p>
            <button class="btn btn-primary mt-2" @click="goHome">Back to Activities</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const step = ref(1)

// Scenes / contexts
const scenes = [
    { title: 'Colleague in the break room', icon: '💬' },
    { title: 'Citizen at tea stall', icon: '☕' },
    { title: 'Talking to a family member', icon: '🏠' },
    { title: 'Co-passenger while commuting', icon: '🚌' }
]

const selectedScene = ref(null)
const chatHistory = ref([])
const currentReplies = ref([])
const selectedReply = ref('')
const typedReply = ref('')
const reflection = ref('')
const friendlinessScore = ref(0)
const feedback = ref({
    usedName: false,
    openQuestion: false,
    empathy: false
})

// Sample conversation for demo
const demoConversations = {
    'Colleague in the break room': [
        { text: "Long shift, huh? You look tired.", replies: ["Yeah, but finally done!", "Same here, you okay?"] },
        { text: "I noticed you've been handling the reports well.", replies: ["Thanks! It's been busy.", "Appreciate it."] },
        { text: "Any plans for the weekend?", replies: ["Just relaxing.", "Maybe catch up on sleep."] },
        { text: "Do you want to grab coffee later?", replies: ["Sure, sounds good!", "Maybe another time."] },
        { text: "See you at the briefing tomorrow then.", replies: ["Yes, see you!", "Looking forward to it."] }
    ],

    'Citizen at tea stall': [
        { text: "Lovely weather today, isn't it?", replies: ["Yes, very pleasant!", "Quite sunny indeed."] },
        { text: "How’s the tea today?", replies: ["Perfect as always!", "A bit stronger than usual."] },
        { text: "Do you come here often?", replies: ["Every morning!", "Just when I feel like it."] },
        { text: "Nice chatting with you.", replies: ["Likewise!", "Thanks, have a good day!"] }
    ],

    'Talking to a family member': [
        { text: "Hey! How was your day?", replies: ["It was long, but okay.", "Pretty tiring."] },
        { text: "Did anything interesting happen at work?", replies: ["A lot, actually!", "Nothing special."] },
        { text: "Want to have dinner together?", replies: ["Sure, let’s eat.", "Maybe later."] },
        { text: "How are you feeling now?", replies: ["Better after relaxing.", "A bit exhausted."] }
    ],

    'Co-passenger while commuting': [
        { text: "Morning! Busy ride today, huh?", replies: ["Yeah, quite crowded.", "Tell me about it."] },
        { text: "Do you commute this route often?", replies: ["Every day.", "Sometimes, depends on work."] },
        { text: "Nice weather for a commute, though.", replies: ["Indeed, much better than rain.", "Yes, thankfully."] },
        { text: "Have a good day at work!", replies: ["Thanks, you too!", "Appreciate it!"] }
    ]
}


const selectScene = (scene) => {
    selectedScene.value = scene
    chatHistory.value = []
    currentReplies.value = demoConversations[scene.title][0].replies
    step.value = 3
}

const sendReply = () => {
    const userText = typedReply.value || selectedReply.value
    chatHistory.value.push({ text: userText, sender: 'user' })

    // Add character reply if any remaining
    const nextMsgIndex = chatHistory.value.filter(m=>m.sender==='character').length
    const scenarioMsgs = demoConversations[selectedScene.value.title]
    if (nextMsgIndex < scenarioMsgs.length) {
        const nextMsg = scenarioMsgs[nextMsgIndex]
        chatHistory.value.push({ text: nextMsg.text, sender: 'character' })
        currentReplies.value = nextMsg.replies
    } else {
        // Conversation finished
        calculateScore()
        step.value = 4
    }

    // Reset inputs
    typedReply.value = ''
    selectedReply.value = ''
}

const calculateScore = () => {
    // Dummy scoring for demo
    friendlinessScore.value = 85
    feedback.value = { usedName: true, openQuestion: true, empathy: false }
}

const saveSession = () => {
    const saved = JSON.parse(localStorage.getItem('smallTalkSessions') || '[]')
    saved.push({
        scene: selectedScene.value.title,
        chatHistory: chatHistory.value,
        reflection,
        friendlinessScore: friendlinessScore.value,
        feedback: feedback.value
    })
    localStorage.setItem('smallTalkSessions', JSON.stringify(saved))
    step.value = 6
}

const goHome = () => router.push('/activity')
</script>

<style scoped>
.chat-box {
    overflow-y: auto;
    max-height: 250px;
}
textarea {
    min-height: 80px;
}
button {
    text-align: center;
}
</style>
