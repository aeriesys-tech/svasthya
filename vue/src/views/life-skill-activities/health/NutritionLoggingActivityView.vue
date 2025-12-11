<template>
  <div class="fuelcheck-root">
    <!-- Header -->
    <header class="header">
      <h1>Nutrition Logging</h1>
      <p class="lead">Log what you eat, get a quick nutrient snapshot, and plan a better meal for your next shift.</p>
    </header>

    <!-- Step selector / tabs -->
    <nav class="tabs">
      <button :class="['tab', activeTab === 'log' ? 'active' : '']" @click="activeTab='log'">Log Food</button>
      <button :class="['tab', activeTab === 'summary' ? 'active' : '']" @click="activeTab='summary'">Daily Summary</button>
      <button :class="['tab', activeTab === 'plan' ? 'active' : '']" @click="activeTab='plan'">Meal Planner</button>
      <button :class="['tab', activeTab === 'logbook' ? 'active' : '']" @click="activeTab='logbook'">Logbook</button>
    </nav>

    <!-- LOG FOOD -->
    <section v-if="activeTab === 'log'" class="card">
      <h2 class="section-title">Log a Meal or Drink</h2>

      <div class="row">
        <label class="label">Meal type</label>
        <div class="segmented">
          <button :class="['seg', form.mealType==='Breakfast'?'on':'']" @click="form.mealType='Breakfast'">Breakfast</button>
          <button :class="['seg', form.mealType==='Lunch'?'on':'']" @click="form.mealType='Lunch'">Lunch</button>
          <button :class="['seg', form.mealType==='Snack'?'on':'']" @click="form.mealType='Snack'">Snack</button>
          <button :class="['seg', form.mealType==='Dinner'?'on':'']" @click="form.mealType='Dinner'">Dinner</button>
          <button :class="['seg', form.mealType==='Drink'?'on':'']" @click="form.mealType='Drink'">Drink</button>
        </div>
      </div>

      <div class="row">
        <label class="label">Search food </label>
        <input class="input" v-model="searchQuery" @input="updateSearch" placeholder="e.g. roti, paneer, rice" />
        <ul v-if="searchResults.length" class="search-list">
          <li v-for="f in searchResults" :key="f.name" @click="selectFood(f)" class="search-item">
            <div><strong>{{ f.name }}</strong> <small>({{ f.serving }})</small></div>
            <div class="micro">P {{ f.protein }} • C {{ f.carbs }} • F {{ f.fat }}</div>
          </li>
        </ul>
      </div>

      <div class="row or-row"><span>— or add custom item —</span></div>

      <div class="row">
        <label class="label">Custom food name</label>
        <input class="input" v-model="form.customName" placeholder="e.g. Station canteen thali" />
      </div>

      <div class="row">
        <label class="label">Protein (g) / Carbs (g) / Fat (g)</label>
        <div class="inline-inputs">
          <input type="number" min="0" class="tiny" v-model.number="form.customProtein" placeholder="P" />
          <input type="number" min="0" class="tiny" v-model.number="form.customCarbs" placeholder="C" />
          <input type="number" min="0" class="tiny" v-model.number="form.customFat" placeholder="F" />
        </div>
      </div>

      <div class="row">
        <label class="label">Optional photo (no faces)</label>
        <input type="file" accept="image/*" @change="onPhoto" />
        <div v-if="form.photoPreview" class="photo-preview">
          <img :src="form.photoPreview" alt="meal photo" />
        </div>
      </div>

      <div class="row mt-1">
        <button class="btn-primary" :disabled="!canAdd" @click="addEntry">Add to Log</button>
        <button class="btn-outline" @click="addWaterQuick">Add 250ml Water</button>
      </div>

      <div class="row note">
        <small>Tip: use search for common foods. This is a simple offline database — keep it quick and shift-friendly.</small>
      </div>
    </section>

    <!-- DAILY SUMMARY -->
    <section v-if="activeTab === 'summary'" class="card">
      <h2 class="section-title">Today's Summary — {{ displayDate(todayKey) }}</h2>

      <div class="summary-row">
        <div class="summary-box">
          <div class="big">{{ totals.protein }}g</div>
          <div class="label">Protein</div>
        </div>
        <div class="summary-box">
          <div class="big">{{ totals.carbs }}g</div>
          <div class="label">Carbs</div>
        </div>
        <div class="summary-box">
          <div class="big">{{ totals.fat }}g</div>
          <div class="label">Fat</div>
        </div>
      </div>

      <div class="chart-wrap">
        <svg viewBox="0 0 200 200" class="pie">
          <g transform="translate(100,100)">
            <circle r="90" fill="#f3f5f7"></circle>
            <path
                v-for="(slice, idx) in pieSlices"
                :key="idx"
                :d="slice.path"
                :fill="slice.color"
            />
            <circle r="40" fill="#ffffff"></circle>
            <text x="0" y="6" text-anchor="middle" font-size="12" fill="#334155">{{ totalsSummaryLabel }}</text>
          </g>
        </svg>
      </div>

      <div v-if="todayEntries.length" class="list">
        <div v-for="(e, i) in todayEntries" :key="e.id" class="list-item">
          <div class="list-left">
            <div class="item-name">{{ e.name }}</div>
            <div class="micro">{{ e.mealType }} • {{ timeFromISO(e.date) }}</div>
          </div>
          <div class="list-right">
            <div class="micro">P{{ e.protein }} C{{ e.carbs }} F{{ e.fat }}</div>
            <button class="trash" @click="removeEntry(e.id)">✕</button>
          </div>
        </div>
      </div>
      <div v-else class="empty">No entries yet — add something in Log Food.</div>

      <div class="row mt-1">
        <button class="btn-primary" @click="activeTab='plan'">Plan a Better Meal</button>
        <button class="btn-outline" @click="clearToday">Clear Today</button>
      </div>

      <!-- simple weekly trend -->
      <h3 class="small-title">Weekly check-ins</h3>
      <div class="trend-row">
        <div v-for="(d, idx) in last7" :key="idx" class="trend-col">
          <div class="bar" :style="{height: (d.count*12)+'px'}"></div>
          <div class="bar-label">{{ d.label }}</div>
        </div>
      </div>
    </section>

    <!-- MEAL PLANNER -->
    <section v-if="activeTab === 'plan'" class="card">
      <h2 class="section-title">Meal Planner — Choose a meal to plan</h2>

      <div class="plans-grid">
        <div class="plan-card" v-for="(p, idx) in plannerTemplates" :key="idx">
          <div class="plan-title">{{ p.title }}</div>
          <div class="plan-desc">{{ p.desc }}</div>
          <div class="plan-actions">
            <button class="btn-primary btn-sm" @click="usePlan(p)">Use</button>
            <button class="btn-outline btn-sm" @click="savePlanAsMy(p)">Save</button>
          </div>
        </div>
      </div>

      <div v-if="myPlans.length" class="mt-2">
        <h3 class="small-title">My saved meal plans</h3>
        <div class="list">
          <div class="list-item" v-for="pl in myPlans" :key="pl.id">
            <div class="list-left">
              <div class="item-name">{{ pl.title }}</div>
              <div class="micro">{{ pl.when }}</div>
            </div>
            <div class="list-right">
              <div class="micro">{{ pl.note || '—' }}</div>
              <button class="trash" @click="removePlan(pl.id)">✕</button>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-2">
        <label class="label">Reminder for planned meal (optional)</label>
        <input type="time" v-model="plannerReminder" class="input" />
        <button class="btn-primary mt-1" @click="setPlannerReminder">Set Reminder</button>
        <div v-if="plannerReminderSaved" class="note">Reminder saved: {{ plannerReminderSaved }}</div>
      </div>

      <div class="row mt-2">
        <div class="badges-inline">
          <div class="badge" :class="{'earned': badges.hydration}">Hydration Hero</div>
          <div class="badge" :class="{'earned': badges.balanced}">Balanced Builder</div>
        </div>
      </div>
    </section>

    <!-- LOGBOOK -->
    <section v-if="activeTab === 'logbook'" class="card">
      <h2 class="section-title">Mindful Fuel Logbook</h2>
      <div v-if="entriesAll.length" class="list">
        <div v-for="(d, idx) in entriesAllSorted" :key="d.id" class="list-item">
          <div class="list-left">
            <div class="item-name">{{ d.name }}</div>
            <div class="micro">{{ displayDate(d.key) }} • {{ timeFromISO(d.date) }}</div>
          </div>
          <div class="list-right">
            <div class="micro">P{{ d.protein }} C{{ d.carbs }} F{{ d.fat }}</div>
            <button class="trash" @click="deleteLog(d.id)">✕</button>
          </div>
        </div>
      </div>
      <div v-else class="empty">No logs yet — they'll appear here.</div>

      <div class="row mt-2">
        <button class="btn-outline" @click="exportLogs">Export (JSON)</button>
        <button class="btn-primary" @click="clearAll">Clear All Logs</button>
      </div>
    </section>

    <!-- footer small -->
    <footer class="foot">
      <small>Data stored locally on this device. Photo previews are temporary and stored in memory only.</small>
    </footer>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';

/* ---------- Local food DB (simple) ---------- 
   name, serving text, protein, carbs, fat (grams)
   Add more items as needed for your environment.
*/
const FOOD_DB = [
  /* ---------- Karnataka Tiffin (Breakfast) ---------- */
  { name: 'Thatte Idli (1 pc)', serving: '1 idli', protein: 3, carbs: 28, fat: 0.8 },
  { name: 'Benne Dosa (Davangere, 1 pc)', serving: '1 dosa', protein: 4, carbs: 40, fat: 12 },
  { name: 'Khara Bath (1 cup)', serving: '1 cup', protein: 4, carbs: 32, fat: 10 },
  { name: 'Kesari Bath (1 cup)', serving: '1 cup', protein: 2, carbs: 45, fat: 12 },
  { name: 'Chow Chow Bath (KB+KB)', serving: '2 items', protein: 6, carbs: 75, fat: 22 },
  { name: 'Neer Dose (1 pc)', serving: '1 dosa', protein: 1, carbs: 15, fat: 1 },
  { name: 'Mangalore Buns (1 pc)', serving: '1 bun', protein: 3, carbs: 35, fat: 8 },
  { name: 'Ragi Mudde (1 ball)', serving: '1 ball', protein: 2.5, carbs: 18, fat: 0.5 },
  { name: 'Akki Rotti (1 pc)', serving: '1 rotti', protein: 3, carbs: 28, fat: 4 },
  { name: 'Ragi Rotti (1 pc)', serving: '1 rotti', protein: 3, carbs: 22, fat: 4 },
  { name: 'Uppittu (1 cup)', serving: '1 cup', protein: 4, carbs: 30, fat: 7 },

  /* ---------- Karnataka Rice Varieties ---------- */
  { name: 'Bisi Bele Bath (1 cup)', serving: '1 cup', protein: 6, carbs: 40, fat: 10 },
  { name: 'Puliyogare (1 cup)', serving: '1 cup', protein: 4, carbs: 45, fat: 10 },
  { name: 'Vaangi Bath (1 cup)', serving: '1 cup', protein: 3, carbs: 36, fat: 8 },
  { name: 'Mosaranna (Curd Rice, 1 cup)', serving: '1 cup', protein: 4, carbs: 32, fat: 4 },
  { name: 'Chitranna (Lemon Rice, 1 cup)', serving: '1 cup', protein: 3, carbs: 36, fat: 8 },
  { name: 'Tomato Bath (1 cup)', serving: '1 cup', protein: 3, carbs: 34, fat: 7 },
  { name: 'Ragi Mudde Meal (Mudde+Sambar)', serving: '1 plate', protein: 8, carbs: 42, fat: 6 },

  /* ---------- Karnataka Meals (Veg + Non-Veg) ---------- */
  { name: 'Nati Koli Saaru (1 cup)', serving: '1 cup', protein: 22, carbs: 3, fat: 14 },
  { name: 'Mangalore Fish Curry (1 cup)', serving: '1 cup', protein: 20, carbs: 4, fat: 10 },
  { name: 'Udupi Sambar (1 cup)', serving: '1 cup', protein: 4, carbs: 16, fat: 3 },
  { name: 'Bas Saaru (1 cup)', serving: '1 cup', protein: 5, carbs: 8, fat: 5 },
  { name: 'Mysore Rasam (1 cup)', serving: '1 cup', protein: 2, carbs: 6, fat: 0.5 },
  { name: 'Avarekalu Saaru (1 cup)', serving: '1 cup', protein: 6, carbs: 20, fat: 6 },
  { name: 'Kootu (1 cup)', serving: '1 cup', protein: 5, carbs: 18, fat: 4 },
  { name: 'Palya (Vegetable, 1 bowl)', serving: '1 bowl', protein: 3, carbs: 10, fat: 4 },

  /* ---------- Karnataka Snacks ---------- */
  { name: 'Maddur Vada (1 pc)', serving: '1 vada', protein: 3, carbs: 22, fat: 12 },
  { name: 'Mangalore Bajji (Goli Baje, 3 pcs)', serving: '3 pcs', protein: 3, carbs: 28, fat: 10 },
  { name: 'Nippattu (1 pc)', serving: '1 pc', protein: 2, carbs: 18, fat: 9 },
  { name: 'Kodubale (3 pcs)', serving: '3 pcs', protein: 2, carbs: 25, fat: 12 },

  /* ---------- North Karnataka Specials ---------- */
  { name: 'Jowar Rotti (1 pc)', serving: '1 rotti', protein: 3, carbs: 22, fat: 1 },
  { name: 'Ennegayi (Stuffed Brinjal, 1 cup)', serving: '1 cup', protein: 3, carbs: 10, fat: 8 },
  { name: 'Shenga Chutney Pudi (1 tbsp)', serving: '1 tbsp', protein: 2, carbs: 3, fat: 4 },
  { name: 'Karida Mosaru (Curd+Mirchi, 1 cup)', serving: '1 cup', protein: 4, carbs: 8, fat: 5 },

  /* ---------- Coastal Karnataka ---------- */
  { name: 'Kori Rotti (Chicken+Rotti)', serving: '1 plate', protein: 25, carbs: 40, fat: 14 },
  { name: 'Pundi (Rice Dumpling 3 pcs)', serving: '3 pcs', protein: 4, carbs: 36, fat: 1 },
  { name: 'Kotte Kadubu (1 pc)', serving: '1 pc', protein: 3, carbs: 25, fat: 1 },
  { name: 'Chicken Sukka (1 cup)', serving: '1 cup', protein: 26, carbs: 6, fat: 15 },
  { name: 'Neer Dose + Chicken Curry', serving: '2 dosa+1 cup curry', protein: 23, carbs: 38, fat: 16 },

  /* ---------- Udupi Style ---------- */
  { name: 'Udupi Rasam (1 cup)', serving: '1 cup', protein: 2, carbs: 8, fat: 1 },
  { name: 'Udupi Sambar (1 cup)', serving: '1 cup', protein: 4, carbs: 14, fat: 3 },
  { name: 'Veg Gassi (1 cup)', serving: '1 cup', protein: 4, carbs: 18, fat: 8 },

  /* ---------- Sweets (Karnataka authenticity) ---------- */
  { name: 'Mysore Pak (1 pc)', serving: '1 piece', protein: 2, carbs: 20, fat: 10 },
  { name: 'Obbattu / Holige (1 pc)', serving: '1 holige', protein: 4, carbs: 35, fat: 6 },
  { name: 'Dharwad Peda (1 pc)', serving: '1 peda', protein: 2, carbs: 18, fat: 5 },

  /* ---------- Drinks ---------- */
  { name: 'Filter Coffee (South Karnataka)', serving: '1 cup', protein: 1, carbs: 4, fat: 1 },
  { name: 'Kashaya (Herbal drink)', serving: '1 cup', protein: 1, carbs: 6, fat: 0 },
  { name: 'Spiced Buttermilk (Majjige)', serving: '200 ml', protein: 3, carbs: 6, fat: 1 }
];

/* ---------- localStorage keys ---------- */
const KEY_ENTRIES = 'fuelcheck_entries_v1';
const KEY_PLANS = 'fuelcheck_plans_v1';
const KEY_REMIND = 'fuelcheck_reminder_v1';

/* ---------- reactive state ---------- */
const activeTab = ref('log'); // log, summary, plan, logbook
const todayKey = new Date().toISOString().slice(0,10);

const form = reactive({
  mealType: 'Lunch',
  pickedFood: null, // object from DB
  customName: '',
  customProtein: null,
  customCarbs: null,
  customFat: null,
  photoFile: null,
  photoPreview: null
});

const searchQuery = ref('');
const searchResults = ref([]);

const entriesAll = ref([]); // flat array of log items
const myPlans = ref([]);
const plannerReminder = ref('');
const plannerReminderSaved = ref('');

const badges = reactive({ hydration: false, balanced: false });

/* ---------- util / load ---------- */
function loadStorage() {
  try { entriesAll.value = JSON.parse(localStorage.getItem(KEY_ENTRIES) || '[]'); } catch(e){ entriesAll.value = [];}
  try { myPlans.value = JSON.parse(localStorage.getItem(KEY_PLANS) || '[]'); } catch(e){ myPlans.value = [];}
  try { plannerReminderSaved.value = localStorage.getItem(KEY_REMIND) || ''; } catch(e){ plannerReminderSaved.value = '';}
  computeBadges();
}
onMounted(loadStorage);

/* ---------- search ---------- */
function updateSearch() {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) { searchResults.value = []; return; }
  searchResults.value = FOOD_DB.filter(f => f.name.toLowerCase().includes(q)).slice(0,8);
}

function selectFood(food) {
  form.pickedFood = food;
  form.customName = '';
  form.customProtein = null;
  form.customCarbs = null;
  form.customFat = null;
  searchQuery.value = '';
  searchResults.value = [];
}

/* ---------- photo preview ---------- */
function onPhoto(e) {
  const f = e.target.files && e.target.files[0];
  if (!f) return;
  form.photoFile = f;
  if (form.photoPreview) URL.revokeObjectURL(form.photoPreview);
  form.photoPreview = URL.createObjectURL(f);
}

/* ---------- add entry ---------- */
function genId() { return 'id_'+Math.random().toString(36).slice(2,9); }

const canAdd = computed(() => {
  const hasPicked = form.pickedFood || form.customName.trim();
  const hasMacros = form.pickedFood || (form.customProtein !== null && form.customCarbs !== null && form.customFat !== null);
  return hasPicked && hasMacros;
});

function addEntry() {
  if (!canAdd.value) return;
  const now = new Date();
  let name = '';
  let p = 0, c = 0, f = 0;

  if (form.pickedFood) {
    name = form.pickedFood.name;
    p = Number(form.pickedFood.protein) || 0;
    c = Number(form.pickedFood.carbs) || 0;
    f = Number(form.pickedFood.fat) || 0;
  } else {
    name = form.customName.trim() || 'Custom item';
    p = Number(form.customProtein) || 0;
    c = Number(form.customCarbs) || 0;
    f = Number(form.customFat) || 0;
  }

  // if a water quick add (name includes Water) we treat specially
  const entry = {
    id: genId(),
    key: now.toISOString().slice(0,10),
    date: now.toISOString(),
    mealType: form.mealType,
    name,
    protein: Math.round(p*10)/10,
    carbs: Math.round(c*10)/10,
    fat: Math.round(f*10)/10,
    photo: form.photoPreview || null
  };

  entriesAll.value.push(entry);
  persistEntries();
  resetForm();
  computeBadges();
  // switch to summary to show result
  activeTab.value = 'summary';
}

/* quick water */
function addWaterQuick() {
  form.pickedFood = FOOD_DB.find(x => x.name === 'Water 250ml') || null;
  form.customName = '';
  form.customProtein = 0; form.customCarbs=0; form.customFat=0;
  addEntry();
}

/* ---------- persistence ---------- */
function persistEntries() {
  localStorage.setItem(KEY_ENTRIES, JSON.stringify(entriesAll.value));
}
function persistPlans() {
  localStorage.setItem(KEY_PLANS, JSON.stringify(myPlans.value));
}
function persistReminder() {
  localStorage.setItem(KEY_REMIND, plannerReminderSaved.value || '');
}

/* ---------- helpers ---------- */
function resetForm() {
  form.pickedFood = null;
  form.customName = '';
  form.customProtein = null;
  form.customCarbs = null;
  form.customFat = null;
  form.photoFile = null;
  if (form.photoPreview) { URL.revokeObjectURL(form.photoPreview); form.photoPreview = null; }
}
function timeFromISO(iso) {
  const d = new Date(iso);
  return d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
}
function displayDate(key) {
  const d = new Date(key);
  return d.toLocaleDateString();
}

/* ---------- computed: today entries & totals ---------- */
const todayEntries = computed(() => entriesAll.value.filter(e => e.key === todayKey));
const totals = computed(() => {
  const t = { protein:0, carbs:0, fat:0 };
  todayEntries.value.forEach(e => { t.protein += Number(e.protein||0); t.carbs += Number(e.carbs||0); t.fat += Number(e.fat||0); });
  // round
  t.protein = Math.round(t.protein*10)/10;
  t.carbs = Math.round(t.carbs*10)/10;
  t.fat = Math.round(t.fat*10)/10;
  return t;
});
const totalsSummaryLabel = computed(() => {
  const kal = Math.round((totals.value.protein*4 + totals.value.carbs*4 + totals.value.fat*9));
  return `${kal} kcal`;
});

/* ---------- pie chart slices (SVG path generation) ---------- */
function polarToCartesian(cx, cy, r, angleDeg) {
  const a = (angleDeg-90) * Math.PI/180.0;
  return { x: cx + (r * Math.cos(a)), y: cy + (r * Math.sin(a)) };
}
function describeArc(cx, cy, r, startAng, endAng) {
  const start = polarToCartesian(cx, cy, r, endAng);
  const end = polarToCartesian(cx, cy, r, startAng);
  const largeArcFlag = endAng - startAng <= 180 ? "0" : "1";
  const d = [
    "M", start.x, start.y,
    "A", r, r, 0, largeArcFlag, 0, end.x, end.y,
    "L", cx, cy,
    "Z"
  ].join(" ");
  return d;
}
const pieSlices = computed(() => {
  const p = totals.value.protein || 0;
  const c = totals.value.carbs || 0;
  const f = totals.value.fat || 0;
  const total = p + c + f || 1;
  let start = 0;
  const colors = ['#8b5cf6','#06b6d4','#ffd93d']; // protein, carbs, fat
  const values = [p, c, f];
  const slices = [];
  for (let i=0;i<3;i++){
    const portion = values[i]/total;
    const ang = portion*360;
    const path = describeArc(0,0,90,start, start+ang);
    slices.push({ path, color: colors[i], label: i===0?'Protein': i===1?'Carbs':'Fat', value: values[i] });
    start += ang;
  }
  return slices;
});

/* ---------- last7 simple trend ---------- */
const last7 = computed(() => {
  const arr = [];
  for (let i=6;i>=0;i--) {
    const d = new Date();
    d.setDate(d.getDate()-i);
    const key = d.toISOString().slice(0,10);
    const count = entriesAll.value.filter(e => e.key===key).length;
    arr.push({ key, label: d.toLocaleDateString([], {weekday:'short'}).slice(0,2), count });
  }
  return arr;
});

/* ---------- planner templates & actions ---------- */
const plannerTemplates = [
  { title: 'Healthy Breakfast', desc: 'Egg/curd + roti + fruit', when: 'Before shift' },
  { title: 'Shift Lunch', desc: 'Protein + veg + roti/rice', when: 'Mid shift' },
  { title: 'Portable Dinner', desc: 'Grilled chicken wrap or paneer + salad', when: 'After shift' },
  { title: 'Smart Snack', desc: 'Fruit + handful of nuts', when: 'Snack' }
];

function usePlan(p) {
  // pre-fill form with a balanced example
  form.customName = p.title;
  // rough macros for example
  form.customProtein = 12;
  form.customCarbs = 30;
  form.customFat = 8;
  activeTab.value = 'log';
}
function savePlanAsMy(p) {
  const item = { id: genId(), title: p.title, desc: p.desc, when: p.when, note: '', savedAt: new Date().toISOString() };
  myPlans.value.push(item);
  persistPlans();
  computeBadges();
}

/* planner reminder */
function setPlannerReminder() {
  if (!plannerReminder.value) return;
  plannerReminderSaved.value = plannerReminder.value;
  persistReminder();
  alert('Reminder saved locally (app-level). Implement push separately.');
}

/* remove plan */
function removePlan(id) {
  myPlans.value = myPlans.value.filter(p => p.id !== id);
  persistPlans();
}

/* ---------- badges logic ---------- */
function computeBadges() {
  // hydration hero: at least one water entry (Water 250ml or name includes 'Water') for 7 distinct recent days
  const recentDates = new Set(entriesAll.value.slice(-50).map(e => e.key));
  const waterDays = new Set(entriesAll.value.filter(e => /water/i.test(e.name)).map(e=>e.key));
  // simple: hydration badge if waterDays size >= 3 in recent (adjustable)
  badges.hydration = waterDays.size >= 3;

  // balanced builder: if user saved >=3 plans
  badges.balanced = myPlans.value.length >= 3;
}

/* ---------- logbook helpers ---------- */
const entriesAllSorted = computed(() => entriesAll.value.slice().sort((a,b)=> b.date.localeCompare(a.date)));

function deleteLog(id) {
  entriesAll.value = entriesAll.value.filter(x => x.id !== id);
  persistEntries();
  computeBadges();
}
function clearToday() {
  if (!confirm('Clear today\'s entries?')) return;
  entriesAll.value = entriesAll.value.filter(e => e.key !== todayKey);
  persistEntries();
  computeBadges();
}
function removeEntry(id) {
  entriesAll.value = entriesAll.value.filter(x => x.id !== id);
  persistEntries();
  computeBadges();
}
function clearAll() {
  if (!confirm('Clear all logs?')) return;
  entriesAll.value = [];
  persistEntries();
  computeBadges();
}

/* export */
function exportLogs() {
  const blob = new Blob([JSON.stringify(entriesAll.value, null, 2)], {type:'application/json'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = `fuelcheck_export_${new Date().toISOString().slice(0,10)}.json`;
  a.click();
  URL.revokeObjectURL(url);
}

/* ---------- utilities ---------- */
function savePlannerNoteForPlan(planId, note) {
  const p = myPlans.value.find(x=>x.id===planId);
  if (!p) return;
  p.note = note;
  persistPlans();
}

/* ---------- on changes persist ---------- */
onMounted(() => {
  // ensure nothing needed
});
</script>

<style scoped>
/* Mobile-first layout */
.fuelcheck-root {
  padding: 14px;
  max-width: 760px;
  margin: 0 auto;
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  color: #0f172a;
}

/* header */
.header { text-align:center; margin-bottom:12px;}
.header h1{ margin:0; font-size:1.2rem; font-weight:800;}
.lead{ margin-top:6px; color:#475569; font-size:0.95rem; }

/* tabs */
.tabs { display:flex; gap:6px; margin-bottom:12px; overflow:auto; }
.tab { flex:1; padding:10px; background:#fff; border-radius:10px; border:1px solid #e6eef6; font-weight:700; text-align:center; cursor:pointer;}
.tab.active{ background:#dbeafe; border-color:#7dd3fc; color:#062a43; }

/* card */
.card { background:#fff; padding:12px; border-radius:12px; box-shadow: 0 6px 20px rgba(2,6,23,0.03); margin-bottom:12px; }

/* sections */
.section-title{ font-weight:800; margin-bottom:8px; font-size:1rem; }
.label{ font-weight:700; display:block; margin-bottom:6px; color:#334155; }

/* segmented control */
.segmented { display:flex; gap:6px; flex-wrap:wrap; }
.seg { padding:8px 10px; border-radius:10px; border:1px solid #e6eef6; background:#fff; cursor:pointer; font-weight:700; }
.seg.on{ background:#dbeafe; border-color:#7dd3fc; color:#062a43; }

/* input */
.input { width:100%; padding:10px; border-radius:10px; border:1px solid #e6eef6; margin-bottom:6px; background:#fbfdff; }
.inline-inputs { display:flex; gap:6px; }
.tiny { width:80px; padding:8px; border-radius:8px; border:1px solid #e6eef6; }

/* search list */
.search-list { margin:6px 0 0; padding:0; list-style:none; border-radius:8px; overflow:hidden; }
.search-item { padding:8px; border-bottom:1px solid #f1f5f9; cursor:pointer; display:flex; justify-content:space-between; align-items:center; }
.search-item:hover{ background:#f8fafc; }
.micro { color:#64748b; font-size:0.85rem; }

/* photo preview */
.photo-preview img { width:84px; height:84px; object-fit:cover; border-radius:8px; margin-top:8px; }

/* buttons */
.btn-primary { background:#dc2626; color:#fff; border:none; padding:10px 12px; border-radius:10px; font-weight:800; margin-right:8px; }
.btn-outline { background:transparent; border:1px solid #e6eef6; padding:8px 10px; border-radius:10px; color:#0f172a; }
.btn-sm { padding:6px 8px; font-size:0.9rem; }

/* summary boxes */
.summary-row { display:flex; gap:10px; justify-content:space-between; margin-bottom:12px; }
.summary-box { flex:1; background:#fbfdff; border-radius:8px; padding:10px; text-align:center; border:1px solid #eef2ff; }
.big { font-size:1.25rem; font-weight:800; }

/* chart */
.chart-wrap { display:flex; justify-content:center; margin:10px 0 12px; }
.pie { width:180px; height:180px; }

/* list */
.list { margin-top:8px; }
.list-item { display:flex; justify-content:space-between; align-items:center; padding:10px; border-bottom:1px solid #f1f5f9; }
.list-left { display:flex; flex-direction:column; gap:4px; }
.item-name{ font-weight:700; }
.list-right { text-align:right; display:flex; gap:8px; align-items:center; }

/* trash small */
.trash { background:transparent; border:none; color:#ef4444; font-weight:700; cursor:pointer; }

/* empty */
.empty { text-align:center; color:#64748b; padding:18px 6px; }

/* trend */
.trend-row { display:flex; gap:8px; justify-content:space-between; align-items:end; padding:8px 0; }
.trend-col{ display:flex; flex-direction:column; align-items:center; gap:6px; width:30px; }
.bar { width:18px; background:#e6eef6; border-radius:6px 6px 0 0; transition:height .3s ease; }
.bar-label { font-size:0.7rem; color:#475569; }

/* planner */
.plans-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.plan-card { padding:10px; border-radius:10px; border:1px solid #eef2ff; background:#fff; display:flex; flex-direction:column; gap:6px; }
.plan-title { font-weight:800; }
.plan-desc { color:#475569; font-size:0.9rem; }

/* badges */
.badges-inline { display:flex; gap:8px; margin-top:8px; }
.badge { padding:6px 8px; border-radius:999px; border:1px solid #e6eef6; background:#fbfdff; font-weight:700; color:#334155; }
.badge.earned { background: linear-gradient(180deg,#fff7ed,#fff0db); border-color:#ffd7a8; color:#92400e; }

/* small title */
.small-title { font-weight:800; margin-top:12px; color:#0f172a; }

/* footer */
.foot { text-align:center; color:#64748b; margin-top:8px; font-size:0.85rem; }

/* responsive */
@media(min-width:720px){
  .plans-grid { grid-template-columns: repeat(3,1fr); }
  .search-list { max-height:200px; overflow:auto; }
}
</style>
