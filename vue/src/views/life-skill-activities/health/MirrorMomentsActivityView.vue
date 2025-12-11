<template>
  <div class="mirror-moments-wrapper">

    <!-- STEP 1: Affirmation Selection -->
    <div v-if="step === 1" class="screen">
      <h2 class="title">Mirror Moment</h2>
      <p class="subtitle">Choose or write today's affirmation:</p>

      <div class="affirmation-card"
           v-for="(option, idx) in affirmationOptions"
           :key="idx"
           :class="{selected: currentAffirmation === option}"
           @click="selectAffirmation(option)">
        {{ option }}
      </div>

      <div class="custom-affirmation">
        <input v-model="customAffirmation" placeholder="Write your own compliment">
        <button class="secondary-btn" @click="useCustomAffirmation">Use Custom Affirmation</button>
      </div>

      <button class="primary-btn" :disabled="!currentAffirmation" @click="nextStep">
        Next
      </button>
    </div>

    <!-- STEP 2: Mirror Countdown -->
    <div v-if="step === 2" class="screen">
      <h2 class="title">Say It Out Loud</h2>
      <p class="subtitle">Stand in front of a mirror or use your camera.</p>

      <!-- camera preview is always visible on this screen (if permission given) -->
      <video ref="cameraPreview" autoplay muted playsinline class="camera-preview"></video>

      <div v-if="countdownRunning" class="countdown-overlay">{{ countdown }}</div>

      <div class="actions-row">
        <button class="primary-btn" @click="startMirrorCountdown" :disabled="countdownRunning">
          Start Mirror Countdown
        </button>

        <button class="secondary-btn mt-2" @click="skipMirror">
          Skip Mirror
        </button>
      </div>
    </div>

    <!-- STEP 3: Recording Page -->
    <div v-if="step === 3" class="screen">
      <h2 class="title">Record Your Affirmation</h2>
      <p class="subtitle">Your voice will stay private.</p>

      <!-- LIVE PREVIEW WHILE RECORDING VIDEO -->
      <video v-if="showRecordPreview && recordType==='video'"
             ref="recordPreview"
             autoplay
             playsinline
             muted
             class="camera-preview"></video>

      <!-- AUDIO RECORDING INDICATOR -->
      <div v-if="isRecording && recordType==='audio'" class="audio-indicator">
        🎤 Recording Audio...
        <span class="blinking-dot">●</span>
      </div>

      <!-- RECORD / STOP BUTTONS -->
      <div v-if="!isRecording" class="record-buttons">
        <button class="primary-btn" @click="startVideoRecording" :disabled="isRecording">
          🎥 Record Video
        </button>

        <button class="primary-btn" @click="startAudioRecording" :disabled="isRecording">
          🎤 Record Audio
        </button>
      </div>

      <!-- STOP BUTTON (visible while recording) -->
      <div v-if="isRecording" class="stop-wrapper">
        <button class="stop-btn" @click="stopRecording">Stop Recording</button>
      </div>

      <!-- PREVIEW AFTER RECORDING -->
      <div v-if="recordedURL" class="preview-wrapper">
        <p>Preview:</p>

        <video v-if="recordType === 'video'"
               :src="recordedURL"
               controls
               class="preview-video"></video>

        <audio v-if="recordType === 'audio'"
               :src="recordedURL"
               controls></audio>
      </div>

      <button class="primary-btn mt-3" @click="saveMirrorMoment">
        Save & Finish
      </button>
    </div>

    <!-- STEP 4: Summary with Reflection -->
    <div v-if="step === 4" class="screen">
      <h2 class="title">Well Done!</h2>

      <!-- Motivational message -->
      <p class="subtitle">{{ motivationalMessage }}</p>

      <!-- Mirror Streak -->
      <div class="streak-info">
        <p>Mirror Streak: {{ streak }} days</p>
        <p>Badge: {{ badge }}</p>
      </div>

      <!-- REFLECTION TEXTAREA -->
      <div class="reflection-box">
        <label for="reflectionText">Write your reflection (optional, private):</label>
        <textarea id="reflectionText" v-model="reflectionText" placeholder="Your reflection goes here..."></textarea>
        <button class="primary-btn mt-2" @click="saveReflection">Save Reflection</button>
      </div>

      <button class="primary-btn mt-3" @click="resetActivity">Back to Home</button>
    </div>

    <!-- LOG VIEW -->
    <div v-if="showLog" class="screen log-screen">
      <h2 class="title">My Mirror Log</h2>

      <div v-if="!mirrorLog.length" class="empty-log">No entries yet.</div>

      <div v-for="(entry, idx) in mirrorLog" :key="idx" class="log-entry">
        <p><strong>{{ entry.date }}</strong></p>
        <p>{{ entry.affirmation }}</p>
        <p v-if="entry.reflection"><em>Reflection: {{ entry.reflection }}</em></p>

        <video v-if="entry.recordType==='video'" :src="entry.recordURL" controls class="preview-video"></video>
        <audio v-if="entry.recordType==='audio'" :src="entry.recordURL" controls></audio>
      </div>

      <div class="actions-row">
        <button class="primary-btn mt-3" @click="showLog=false">Close Log</button>
        <button class="secondary-btn mt-3" @click="clearLog">Clear Log</button>
      </div>
    </div>

    <!-- Floating Log Button -->
    <button class="floating-log-btn btn btn-secondary" @click="showLog=true">Mirror Log</button>

  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from "vue";

/* ------------------------------ STATE ------------------------------ */
const step = ref(1);

/* Affirmations */
const affirmationOptions = [
  "I helped someone yesterday.",
  "I stayed calm during a tough moment.",
  "I handled my shift with focus."
];
const currentAffirmation = ref("");
const customAffirmation = ref("");

/* Mirror & camera refs */
const cameraPreview = ref(null);
const recordPreview = ref(null);
let mediaStream = null;
let mediaRecorderInstance = null;

/* Countdown state */
const countdown = ref(5);
const countdownRunning = ref(false);

/* Recording */
const isRecording = ref(false);
const recordType = ref(""); // 'audio' or 'video'
const recordedChunks = ref([]);
const recordedURL = ref(null);
const showRecordPreview = ref(false);

/* Log & streak */
const mirrorLog = ref([]);
const showLog = ref(false);
const streak = ref(0);
const badge = ref("");
const motivationalMessages = [
  "You showed up for yourself today!",
  "Your words matter. Keep growing.",
  "Small steps make strong confidence."
];
const motivationalMessage = ref("");
const reflectionText = ref(""); // NEW

/* ------------------------------ HELPERS ------------------------------ */
function nextStep() { step.value++; }
function selectAffirmation(opt) { currentAffirmation.value = opt; }
function useCustomAffirmation() {
  if (customAffirmation.value.trim()) currentAffirmation.value = customAffirmation.value.trim();
}

/* ------------------------------ CAMERA / MEDIA ------------------------------ */
async function openCameraForPreview(previewRef, { video = true, audio = true } = {}) {
  try {
    stopCamera();
    mediaStream = await navigator.mediaDevices.getUserMedia({ video, audio });
    await nextTick();
    if (previewRef && previewRef.value) {
      previewRef.value.srcObject = mediaStream;
      try { await previewRef.value.play(); } catch (_) {}
    }
    return mediaStream;
  } catch (err) {
    console.error("getUserMedia error:", err);
    alert("Camera / microphone permission denied or not available.");
    throw err;
  }
}
function stopCamera() {
  if (mediaStream) mediaStream.getTracks().forEach(t => t.stop());
  if (cameraPreview.value) cameraPreview.value.srcObject = null;
  if (recordPreview.value) recordPreview.value.srcObject = null;
}

/* ------------------------------ MIRROR COUNTDOWN ------------------------------ */
let mirrorTimer = null;
async function startMirrorCountdown() {
  countdown.value = 5;
  countdownRunning.value = true;
  try { await openCameraForPreview(cameraPreview); } catch {}
  mirrorTimer && clearInterval(mirrorTimer);
  mirrorTimer = setInterval(() => {
    countdown.value--;
    if (countdown.value <= 0) { clearInterval(mirrorTimer); mirrorTimer=null; countdownRunning.value=false; }
  }, 1000);
}
function skipMirror() { stopCamera(); countdownRunning.value=false; nextStep(); }

/* ------------------------------ RECORDING ------------------------------ */
async function startVideoRecording() {
  recordedURL.value = null; recordType.value="video"; recordedChunks.value=[]; showRecordPreview.value=true;
  try { await openCameraForPreview(recordPreview); } catch { showRecordPreview.value=false; return; }
  try { mediaRecorderInstance = new MediaRecorder(mediaStream,{ mimeType:"video/webm;codecs=vp8,opus"}); } catch { mediaRecorderInstance = new MediaRecorder(mediaStream); }
  mediaRecorderInstance.ondataavailable = e => { if(e.data && e.data.size>0) recordedChunks.value.push(e.data); };
  mediaRecorderInstance.onstop = () => {
    recordedURL.value = URL.createObjectURL(new Blob(recordedChunks.value,{ type:"video/webm" }));
    stopCamera(); showRecordPreview.value=false; isRecording.value=false; mediaRecorderInstance=null;
  };
  try { mediaRecorderInstance.start(); isRecording.value=true; } catch(err){ console.error(err); alert("Recording not supported."); stopCamera(); isRecording.value=false; }
}

async function startAudioRecording() {
  recordedURL.value=null; recordType.value="audio"; recordedChunks.value=[]; showRecordPreview.value=false;
  try { await openCameraForPreview(null,{ video:false, audio:true }); } catch { return; }
  try { mediaRecorderInstance = new MediaRecorder(mediaStream,{ mimeType:"audio/webm" }); } catch { mediaRecorderInstance = new MediaRecorder(mediaStream); }
  mediaRecorderInstance.ondataavailable=e=>{if(e.data&&e.data.size>0) recordedChunks.value.push(e.data);};
  mediaRecorderInstance.onstop=()=>{
    recordedURL.value = URL.createObjectURL(new Blob(recordedChunks.value,{ type:"audio/webm" }));
    stopCamera(); isRecording.value=false; mediaRecorderInstance=null;
  };
  try { mediaRecorderInstance.start(); isRecording.value=true; } catch(err){ console.error(err); alert("Audio recording not supported."); stopCamera(); isRecording.value=false; }
}

function stopRecording() {
  if(!isRecording.value) return;
  try{ if(mediaRecorderInstance && mediaRecorderInstance.state!=='inactive') mediaRecorderInstance.stop(); else { stopCamera(); isRecording.value=false; mediaRecorderInstance=null; } } catch(e){ console.error(e); stopCamera(); isRecording.value=false; mediaRecorderInstance=null; }
}

/* ------------------------------ SAVE / LOG ------------------------------ */
function saveMirrorMoment() {
  const entry = { date: new Date().toLocaleString(), affirmation: currentAffirmation.value, recordURL:recordedURL.value||null, recordType:recordType.value||null, reflection: "" };
  mirrorLog.value.push(entry);
  localStorage.setItem("mirrorLog", JSON.stringify(mirrorLog.value));
  motivationalMessage.value = motivationalMessages[Math.floor(Math.random()*motivationalMessages.length)];
  updateStreak();
  step.value = 4; // Step 4 shows motivational + reflection
}

function saveReflection() {
  if(reflectionText.value.trim()){
    if(!confirm("Save your reflection privately?")) return;
    // update last log entry
    const lastIndex = mirrorLog.value.length-1;
    if(lastIndex>=0) mirrorLog.value[lastIndex].reflection=reflectionText.value.trim();
    localStorage.setItem("mirrorLog",JSON.stringify(mirrorLog.value));
    alert("Reflection saved privately!");
    reflectionText.value="";
  } else alert("Reflection is empty. Nothing saved.");
}

function updateStreak(){
  const log = mirrorLog.value;
  if(!log.length){ streak.value=0; badge.value="None"; return; }
  let count=0;
  const lastIndex=log.length-1;
  const lastDate=new Date(log[lastIndex].date);
  const lastDayStart = new Date(lastDate.getFullYear(), lastDate.getMonth(), lastDate.getDate()).getTime();
  for(let i=lastIndex;i>=0;i--){
    const d = new Date(log[i].date);
    const dayStart = new Date(d.getFullYear(),d.getMonth(),d.getDate()).getTime();
    if(Math.round((lastDayStart-dayStart)/86400000)===count) count++; else break;
  }
  streak.value=count;
  if(streak.value>=14) badge.value="Gold 🥇";
  else if(streak.value>=7) badge.value="Silver 🥈";
  else if(streak.value>=3) badge.value="Bronze 🥉";
  else badge.value="None";
}

function clearLog(){ if(confirm("Clear all Mirror Log entries?")){ mirrorLog.value=[]; localStorage.removeItem("mirrorLog"); streak.value=0; badge.value="None"; } }
function resetActivity(){ step.value=1; currentAffirmation.value=""; customAffirmation.value=""; recordedURL.value=null; recordType.value=""; isRecording.value=false; showRecordPreview.value=false; reflectionText.value=""; }

/* ------------------------------ LIFECYCLE ------------------------------ */
onMounted(()=>{
  try{ const saved=JSON.parse(localStorage.getItem("mirrorLog")||"[]"); if(Array.isArray(saved)) mirrorLog.value=saved; } catch(e){ mirrorLog.value=[]; }
  updateStreak();
});

onBeforeUnmount(()=>{
  stopRecording(); stopCamera();
  if(recordedURL.value){ try{ URL.revokeObjectURL(recordedURL.value); } catch(e){} }
});
</script>

<style scoped>
/* CLEAN POLISHED UI */
.mirror-moments-wrapper { padding:20px; max-width:460px; margin:auto; font-family:"Segoe UI",sans-serif; position:relative; color:#1b2b33; }
.screen{ margin-bottom:18px; }
.title{ font-size:1.6rem; font-weight:700; margin-bottom:6px; }
.subtitle{ color:#556; margin-bottom:12px; }

/* Buttons */
.primary-btn{ width:100%; padding:12px; margin-top:12px; border-radius:12px; background:#4e8cff; color:#fff; border:none; cursor:pointer; font-weight:700; transition:background .18s; }
.primary-btn:disabled{ opacity:0.6; cursor:not-allowed; }
.primary-btn:hover:not(:disabled){ background:#3b6fd0; }

.secondary-btn{ width:100%; padding:10px; background:#efefef; border:none; border-radius:10px; cursor:pointer; margin-top:8px; font-weight:600; }
.secondary-btn:hover{ background:#e0e0e0; }

/* Affirmation cards */
.affirmation-card{ background:#eef6ff; padding:12px; border-radius:12px; margin-bottom:8px; cursor:pointer; border:1px solid rgba(78,140,255,0.12);}
.affirmation-card.selected{ background:#4e8cff; color:#fff; }

/* custom input */
.custom-affirmation input{ width:100%; padding:10px; border-radius:10px; border:1px solid #ccc; }

/* camera preview */
.camera-preview{ width:100%; height:340px; background:#000; border-radius:12px; object-fit:cover; display:block; margin:12px 0; }

/* countdown big */
.countdown-overlay{ text-align:center; font-size:56px; font-weight:800; color:#4e8cff; margin-top:6px; }

/* record / stop */
.record-buttons{ display:flex; gap:10px; flex-direction:column; }
.stop-wrapper{ margin-top:12px; }
.stop-btn{ background:#d64545; color:white; padding:12px; border-radius:12px; width:100%; border:none; font-weight:800; cursor:pointer; }

/* audio indicator */
.audio-indicator{ margin-top:10px; font-size:1.1rem; color:#d64545; display:flex; justify-content:center; align-items:center; gap:8px; }
.blinking-dot{ animation:blink 1s infinite; color:#d64545; }

/* preview */
.preview-wrapper{ margin-top:12px; }
.preview-video{ width:100%; border-radius:12px; }

/* log */
.log-screen .log-entry{ background:#f7f7f8; padding:12px; border-radius:12px; margin-bottom:10px; }
.empty-log{ color:#666; padding:12px; background:#fafafa; border-radius:10px; }

/* floating log */
.floating-log-btn{ width:100%; color:#fff; padding:12px 18px; border-radius:999px; border:none; cursor:pointer; font-weight:700; box-shadow:0 6px 18px rgba(40,70,120,0.12); }

/* small utility */
.actions-row{ display:flex; gap:12px; flex-direction:column; }

/* REFLECTION */
.reflection-box{ margin-top:16px; }
.reflection-box textarea{ width:100%; min-height:80px; padding:10px; border-radius:10px; border:1px solid #ccc; resize:none; margin-top:6px; }

/* blink */
@keyframes blink{ 50%{ opacity:0; } }
</style>
